<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Membership;
use App\Models\MembershipAdjustment;
use App\Models\Plan;
use App\Models\User;
use App\Modules\Clients\Services\ClientMembershipDomainService;
use App\Services\CashSessionService;
use App\Services\PlanAccessService;
use App\Services\PromotionService;
use App\Support\ActiveGymContext;
use App\Support\ClientAudit;
use App\Support\PlanDuration;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use RuntimeException;

class MembershipController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService,
        private readonly PlanAccessService $planAccessService,
        private readonly PromotionService $promotionService,
        private readonly ClientMembershipDomainService $membershipDomainService
    ) {
    }

    /**
     * Store membership for a client in current gym.
     */
    public function store(Request $request, string $contextGym): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors([
                    'membership' => 'Selecciona una sucursal específica para vender membresías.',
                ]);
        }

        $gymId = $this->resolveGymId($request);
        $actor = $this->resolveActor($request);
        $canManagePromotions = $this->planAccessService->canForGym($gymId, 'promotions');

        $data = $request->validate([
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('plans', 'id')->where(
                    fn ($query) => $query
                        ->where('gym_id', $gymId)
                        ->where('status', 'active')
                ),
            ],
            'starts_at' => ['required', 'date'],
            'status' => ['nullable', Rule::in(['active', 'expired', 'cancelled'])],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
            'payment_received_at' => ['nullable', 'date', 'before_or_equal:today'],
            'promotion_id' => [
                'nullable',
                'integer',
                Rule::exists('promotions', 'id')->where(
                    fn ($query) => $query
                        ->where('gym_id', $gymId)
                        ->where('status', 'active')
                ),
            ],
        ]);

        $plan = Plan::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price', 'status'])
            ->findOrFail($data['plan_id']);

        $promotionId = $data['promotion_id'] ?? null;
        if (! $canManagePromotions && ! empty($promotionId)) {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'promotion_id' => 'Tu plan actual no incluye promociones.',
                ])
                ->withInput();
        }

        $startsAt = Carbon::parse((string) $data['starts_at'])->startOfDay();
        $paymentReceivedAt = ! empty($data['payment_received_at'])
            ? Carbon::parse((string) $data['payment_received_at'])->setTimeFrom(Carbon::now())
            : Carbon::now();
        $pricing = $this->promotionService->resolveForSale(
            gymId: $gymId,
            plan: $plan,
            promotionId: $canManagePromotions ? $promotionId : null,
            date: $startsAt
        );

        if (! empty($data['promotion_id']) && ! $pricing['promotion']) {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'promotion_id' => 'La promoción seleccionada no aplica para este plan, fecha o ya alcanzó su límite.',
                ])
                ->withInput();
        }

        $endsAt = PlanDuration::calculateEndsAt(
            startsAt: $startsAt,
            plan: $plan,
            bonusDays: (int) $pricing['bonus_days']
        );

        $openSession = $this->cashSessionService->getOpenSession($gymId);
        if (! $openSession) {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'cash' => 'Debe abrir caja para cobrar.',
                ])
                ->withInput();
        }

        $membership = null;
        try {
            DB::transaction(function () use (
                $actor,
                $gymId,
                $data,
                $plan,
                $pricing,
                $startsAt,
                $endsAt,
                $paymentReceivedAt,
                $request,
                &$membership
            ): void {
                $membership = Membership::query()->create([
                    'gym_id' => $gymId,
                    'client_id' => $data['client_id'],
                    'plan_id' => $plan->id,
                    'price' => $pricing['final_price'],
                    'promotion_id' => $pricing['promotion']?->id,
                    'promotion_name' => $pricing['promotion']?->name,
                    'promotion_type' => $pricing['promotion']?->type,
                    'promotion_value' => $pricing['promotion']?->value,
                    'discount_amount' => $pricing['discount_amount'],
                    'bonus_days' => $pricing['bonus_days'],
                    'starts_at' => $startsAt->toDateString(),
                    'ends_at' => $endsAt->toDateString(),
                    'status' => $data['status'] ?? 'active',
                ]);

                $description = $this->membershipDomainService->buildMembershipCashDescription(
                    membershipId: (int) $membership->id,
                    planName: (string) $plan->name,
                    basePrice: (float) $pricing['base_price'],
                    promotion: $pricing['promotion']
                );

                $this->cashSessionService->addMovement(
                    gymId: $gymId,
                    userId: (int) $request->user()->id,
                    type: 'income',
                    amount: (float) $pricing['final_price'],
                    method: (string) $data['payment_method'],
                    membershipId: $membership->id,
                    description: $description,
                    occurredAt: $paymentReceivedAt
                );

                if ($pricing['promotion']) {
                    $pricing['promotion']->increment('times_used');
                }

                $this->syncClientStatusForToday(
                    $gymId,
                    (int) $data['client_id'],
                    ClientAudit::managementAttributesFromUser($actor)
                );
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'cash' => $exception->getMessage(),
                ])
                ->withInput();
        }

        return redirect()
            ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
            ->with('status', 'Membresía creada y cobrada correctamente en caja.');
    }

    public function adjust(Request $request, string $contextGym, int $membership): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors([
                    'membership_adjustment' => 'Selecciona una sucursal específica para ajustar membresías.',
                ]);
        }

        $gymId = $this->resolveGymId($request);
        $actor = $this->resolveActor($request);
        $adjustmentType = (string) $request->input('adjustment_type', '');

        $data = $request->validate([
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'adjustment_type' => ['required', Rule::in($this->membershipAdjustmentTypes())],
            'reason' => ['required', Rule::in($this->allowedMembershipAdjustmentReasons($adjustmentType))],
            'notes' => ['nullable', 'string', 'max:500'],
            'starts_at' => [
                Rule::requiredIf(in_array($adjustmentType, ['reschedule_start', 'manual_window'], true)),
                'nullable',
                'date',
            ],
            'ends_at' => [
                Rule::requiredIf($adjustmentType === 'manual_window'),
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
            'extra_days' => [
                Rule::requiredIf($adjustmentType === 'extend_access'),
                'nullable',
                'integer',
                'min:1',
                'max:90',
            ],
        ], [
            'reason.in' => 'Selecciona un motivo válido para el tipo de ajuste elegido.',
        ]);

        $membershipModel = Membership::query()
            ->forGym($gymId)
            ->with(['plan:id,gym_id,name,duration_days,duration_unit,duration_months,price,status'])
            ->findOrFail($membership);

        abort_unless((int) $membershipModel->client_id === (int) $data['client_id'], 404);

        if ((string) $membershipModel->status === 'cancelled') {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'adjustment_type' => 'No se pueden ajustar membresías canceladas desde esta pantalla.',
                ])
                ->withInput();
        }

        $plan = $membershipModel->plan;
        if (! $plan) {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'adjustment_type' => 'La membresía no tiene un plan válido para recalcular fechas.',
                ])
                ->withInput();
        }

        $previousStartsAt = $membershipModel->starts_at?->copy()->startOfDay();
        $previousEndsAt = $membershipModel->ends_at?->copy()->startOfDay();
        abort_if(! $previousStartsAt || ! $previousEndsAt, 422, 'La membresía no tiene una ventana válida para ajustar.');

        $newStartsAt = $previousStartsAt->copy();
        $newEndsAt = $previousEndsAt->copy();

        switch ($data['adjustment_type']) {
            case 'reschedule_start':
                $newStartsAt = Carbon::parse((string) $data['starts_at'])->startOfDay();
                $window = $this->membershipDomainService->resolveMembershipWindow(
                    startsAt: $newStartsAt,
                    plan: $plan,
                    bonusDays: (int) ($membershipModel->bonus_days ?? 0)
                );
                $newEndsAt = $window['ends_at']->copy()->startOfDay();
                break;

            case 'extend_access':
                $newEndsAt = $previousEndsAt->copy()->addDays((int) $data['extra_days']);
                break;

            case 'manual_window':
                $newStartsAt = Carbon::parse((string) $data['starts_at'])->startOfDay();
                $newEndsAt = Carbon::parse((string) $data['ends_at'])->startOfDay();
                break;
        }

        $newStatus = $this->membershipDomainService->resolveMembershipStatus($newStartsAt, $newEndsAt);
        $daysDelta = (int) $previousEndsAt->diffInDays($newEndsAt, false);

        if (
            $newStartsAt->equalTo($previousStartsAt)
            && $newEndsAt->equalTo($previousEndsAt)
            && $newStatus === (string) $membershipModel->status
        ) {
            return redirect()
                ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
                ->withErrors([
                    'adjustment_type' => 'No hay cambios para guardar en la membresía.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $actor, $gymId, $membershipModel, $data, $previousStartsAt, $previousEndsAt, $newStartsAt, $newEndsAt, $newStatus, $daysDelta): void {
            $previousStatus = (string) $membershipModel->status;

            $membershipModel->update([
                'starts_at' => $newStartsAt->toDateString(),
                'ends_at' => $newEndsAt->toDateString(),
                'status' => $newStatus,
            ]);

            MembershipAdjustment::query()->create([
                'gym_id' => $gymId,
                'client_id' => (int) $membershipModel->client_id,
                'membership_id' => (int) $membershipModel->id,
                'performed_by' => (int) $request->user()->id,
                'type' => (string) $data['adjustment_type'],
                'reason' => (string) $data['reason'],
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
                'previous_starts_at' => $previousStartsAt->toDateString(),
                'previous_ends_at' => $previousEndsAt->toDateString(),
                'previous_status' => $previousStatus,
                'new_starts_at' => $newStartsAt->toDateString(),
                'new_ends_at' => $newEndsAt->toDateString(),
                'new_status' => $newStatus,
                'days_delta' => $daysDelta,
            ]);

            $this->syncClientStatusForToday(
                $gymId,
                (int) $membershipModel->client_id,
                ClientAudit::managementAttributesFromUser($actor)
            );
        });

        return redirect()
            ->route('clients.show', ['contextGym' => $contextGym, 'client' => $data['client_id'], 'tab' => 'membership'])
            ->with('status', 'Membresía ajustada correctamente.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function resolveActor(Request $request): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403, 'Usuario no autenticado.');

        return $actor;
    }

    /**
     * @param  array<string, mixed>  $extraUpdates
     */
    private function syncClientStatusForToday(int $gymId, int $clientId, array $extraUpdates = []): void
    {
        $hasActiveMembershipToday = Membership::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->activeOn(now()->toDateString())
            ->exists();

        Client::query()
            ->forGym($gymId)
            ->where('id', $clientId)
            ->update(array_merge([
                'status' => $hasActiveMembershipToday ? 'active' : 'inactive',
            ], $extraUpdates));
    }

    /**
     * @return list<string>
     */
    private function membershipAdjustmentTypes(): array
    {
        return ['reschedule_start', 'extend_access', 'manual_window'];
    }

    /**
     * @return array<string, string>
     */
    private function membershipAdjustmentReasonLabels(): array
    {
        return [
            'payment_registered_late' => 'Pago recibido antes del registro',
            'future_start_requested' => 'Inicio acordado para otra fecha',
            'grace_period' => 'Prórroga o permiso temporal',
            'administrative_correction' => 'Corrección administrativa',
            'owner_exception' => 'Excepción autorizada por el dueño',
        ];
    }
    /**
     * @return list<string>
     */
    private function allowedMembershipAdjustmentReasons(string $adjustmentType): array
    {
        return match ($adjustmentType) {
            'reschedule_start' => [
                'payment_registered_late',
                'future_start_requested',
                'administrative_correction',
            ],
            'extend_access' => [
                'grace_period',
                'administrative_correction',
                'owner_exception',
            ],
            'manual_window' => [
                'future_start_requested',
                'administrative_correction',
                'owner_exception',
            ],
            default => array_keys($this->membershipAdjustmentReasonLabels()),
        };
    }
}

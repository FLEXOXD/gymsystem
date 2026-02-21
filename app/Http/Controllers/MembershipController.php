<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Membership;
use App\Models\Plan;
use App\Services\CashSessionService;
use App\Services\PromotionService;
use App\Support\PlanDuration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class MembershipController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService,
        private readonly PromotionService $promotionService
    ) {
    }

    /**
     * Store membership for a client in current gym.
     */
    public function store(Request $request): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);

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

        $startsAt = Carbon::parse($data['starts_at'])->startOfDay();
        $pricing = $this->promotionService->resolveForSale(
            gymId: $gymId,
            plan: $plan,
            promotionId: $data['promotion_id'] ?? null,
            date: $startsAt
        );

        if (! empty($data['promotion_id']) && ! $pricing['promotion']) {
            return redirect()
                ->route('clients.show', $data['client_id'])
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
                ->route('clients.show', $data['client_id'])
                ->withErrors([
                    'cash' => 'Debe abrir caja para cobrar.',
                ])
                ->withInput();
        }

        $membership = null;
        try {
            DB::transaction(function () use (
                $gymId,
                $data,
                $plan,
                $pricing,
                $startsAt,
                $endsAt,
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

                $description = 'Cobro membresía #'.$membership->id.' - Plan '.$plan->name;
                if ($pricing['promotion']) {
                    $description .= ' - Promo '.$pricing['promotion']->name;
                }

                $this->cashSessionService->addMovement(
                    gymId: $gymId,
                    userId: (int) $request->user()->id,
                    type: 'income',
                    amount: (float) $pricing['final_price'],
                    method: $data['payment_method'],
                    membershipId: $membership->id,
                    description: $description
                );

                if ($pricing['promotion']) {
                    $pricing['promotion']->increment('times_used');
                }

                // Keep client access status aligned with effective memberships.
                $hasActiveMembershipToday = Membership::query()
                    ->forGym($gymId)
                    ->where('client_id', (int) $data['client_id'])
                    ->activeOn(now()->toDateString())
                    ->exists();

                Client::query()
                    ->forGym($gymId)
                    ->where('id', (int) $data['client_id'])
                    ->update([
                        'status' => $hasActiveMembershipToday ? 'active' : 'inactive',
                    ]);
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('clients.show', $data['client_id'])
                ->withErrors([
                    'cash' => $exception->getMessage(),
                ])
                ->withInput();
        }

        return redirect()
            ->route('clients.show', $data['client_id'])
            ->with('status', 'Membresía creada y cobrada correctamente en caja.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}

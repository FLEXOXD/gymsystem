<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientPhotoRequest;
use App\Modules\Clients\Actions\RegisterClientAction;
use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\Promotion;
use App\Services\CashSessionService;
use App\Services\PlanAccessService;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use RuntimeException;

class ClientController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService,
        private readonly PlanAccessService $planAccessService,
        private readonly RegisterClientAction $registerClientAction
    ) {
    }

    /**
     * Display all clients for current gym.
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $canManagePromotions = $this->planAccessService->canForGym($gymId, 'promotions');
        $canManageClientAccounts = $this->planAccessService->canForGym($gymId, 'client_accounts');
        $search = trim((string) $request->query('q', ''));
        $quickFilter = (string) $request->query('filter', 'all');
        if (! in_array($quickFilter, ['all', 'active', 'expiring', 'expired', 'attended_today'], true)) {
            $quickFilter = 'all';
        }

        $now = now();
        $today = $now->copy()->startOfDay();
        $todayDate = $today->toDateString();
        $expiringLimitDate = $today->copy()->addDays(7)->toDateString();

        $latestMembershipSub = Membership::query()
            ->from('memberships as m')
            ->select([
                'm.id as latest_membership_id',
                'm.client_id',
                'm.plan_id',
                'm.price as membership_price',
                'm.promotion_name',
                'm.starts_at',
                'm.ends_at',
                'm.status as membership_status',
            ])
            ->forGyms($gymIds)
            ->whereRaw('m.id = (
                SELECT m2.id
                FROM memberships as m2
                WHERE m2.gym_id = m.gym_id
                  AND m2.client_id = m.client_id
                ORDER BY m2.ends_at DESC, m2.id DESC
                LIMIT 1
            )');

        $clientsQuery = Client::query()
            ->from('clients')
            ->whereIn('clients.gym_id', $gymIds)
            ->search($search)
            ->leftJoin('gyms as g', 'g.id', '=', 'clients.gym_id')
            ->leftJoinSub($latestMembershipSub, 'lm', function ($join): void {
                $join->on('lm.client_id', '=', 'clients.id');
            })
            ->leftJoin('plans as lp', function ($join): void {
                $join->on('lp.id', '=', 'lm.plan_id')
                    ->on('lp.gym_id', '=', 'clients.gym_id');
            })
            ->select([
                'clients.id',
                'clients.gym_id',
                'clients.first_name',
                'clients.last_name',
                'clients.document_number',
                'clients.photo_path',
                'clients.status',
                'g.name as gym_name',
                'g.slug as gym_slug',
                'lm.latest_membership_id',
                'lm.starts_at as membership_starts_at',
                'lm.ends_at as membership_ends_at',
                'lm.membership_status',
                'lp.name as plan_name',
                'lm.membership_price as plan_price',
                'lm.promotion_name',
            ])
            ->selectSub(
                Attendance::query()
                    ->whereIn('attendances.gym_id', $gymIds)
                    ->whereColumn('attendances.gym_id', 'clients.gym_id')
                    ->whereColumn('attendances.client_id', 'clients.id')
                    ->orderByDesc('attendances.date')
                    ->orderByDesc('attendances.time')
                    ->limit(1)
                    ->select('attendances.date'),
                'last_attendance_date'
            )
            ->selectSub(
                Attendance::query()
                    ->whereIn('attendances.gym_id', $gymIds)
                    ->whereColumn('attendances.gym_id', 'clients.gym_id')
                    ->whereColumn('attendances.client_id', 'clients.id')
                    ->orderByDesc('attendances.date')
                    ->orderByDesc('attendances.time')
                    ->limit(1)
                    ->select('attendances.time'),
                'last_attendance_time'
            );

        $this->applyQuickFilter($clientsQuery, $quickFilter, $todayDate, $expiringLimitDate, $gymIds);

        $stats = $this->buildStats(clone $clientsQuery, $todayDate, $expiringLimitDate);

        $clients = $clientsQuery
            ->orderByDesc('clients.id')
            ->paginate(20)
            ->withQueryString();

        $paymentsByMembership = $this->resolveMembershipPayments($gymIds, $clients);
        $clients->setCollection(
            $clients->getCollection()->map(function (Client $client) use ($paymentsByMembership, $now, $request): array {
                $row = $this->buildClientCardRow($client, $paymentsByMembership, $now);
                $row['show_url'] = $this->buildClientShowUrl(
                    $request,
                    (int) $client->id,
                    (int) ($client->gym_id ?? 0),
                    trim((string) ($client->gym_slug ?? ''))
                );

                return $row;
            })
        );

        $plans = Plan::query()
            ->forGyms($gymIds)
            ->active()
            ->select(['id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price'])
            ->orderBy('name')
            ->get();

        $promotions = collect();
        if ($canManagePromotions) {
            $promotions = Promotion::query()
                ->forGyms($gymIds)
                ->active()
                ->applicableOn($todayDate)
                ->select([
                    'id',
                    'plan_id',
                    'name',
                    'type',
                    'value',
                    'starts_at',
                    'ends_at',
                    'max_uses',
                    'times_used',
                ])
                ->orderByDesc('id')
                ->get();
        }

        $errorBag = $request->session()->get('errors');
        $hasFormErrors = $errorBag && $errorBag->isNotEmpty();
        $openCreateModal = (bool) old('_open_create_modal', false) || $hasFormErrors;

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
            'quickFilter' => $quickFilter,
            'stats' => $stats,
            'plans' => $plans,
            'promotions' => $promotions,
            'canManagePromotions' => $canManagePromotions,
            'canManageClientAccounts' => $canManageClientAccounts,
            'openCreateModal' => $openCreateModal,
        ]);
    }

    /**
     * Store a new client for current gym.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['clients' => 'Selecciona una sucursal especifica para crear clientes.']);
        }

        $gymId = $this->resolveGymId($request);
        $canManagePromotions = $this->planAccessService->canForGym($gymId, 'promotions');
        $canManageClientAccounts = $this->planAccessService->canForGym($gymId, 'client_accounts');
        $data = $request->validated();
        $startsMembership = (bool) ($data['start_membership'] ?? false);
        $createAppAccount = (bool) ($data['create_app_account'] ?? false);

        if ($startsMembership && ! $this->cashSessionService->getOpenSession($gymId)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['cash' => 'Debe abrir caja para registrar una membresía.'])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        if ($startsMembership && ! $canManagePromotions && ! empty($data['promotion_id'])) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['promotion_id' => 'Tu plan actual no incluye promociones.'])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        if ($createAppAccount && ! $canManageClientAccounts) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['app_username' => 'Tu plan actual no incluye cuentas cliente con usuario y contrasena.'])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('clients', 'public');
        }

        try {
            $this->registerClientAction->execute(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                data: $data,
                canManagePromotions: $canManagePromotions,
                photoPath: $photoPath,
                canManageClientAccounts: $canManageClientAccounts
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        return redirect()
            ->route('clients.index')
            ->with('status', 'Cliente creado correctamente.');
    }

    public function checkDocument(Request $request): JsonResponse
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $document = Client::normalizeDocumentNumber((string) $request->query('document_number', ''));
        $canonical = Client::canonicalDocumentNumber($document);

        if ($canonical === '') {
            return response()->json([
                'exists' => false,
            ]);
        }

        $client = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'first_name', 'last_name', 'document_number'])
            ->whereRaw("REPLACE(REPLACE(UPPER(document_number), '-', ''), ' ', '') = ?", [$canonical])
            ->first();

        if (! $client) {
            return response()->json([
                'exists' => false,
            ]);
        }

        return response()->json([
            'exists' => true,
            'id' => (int) $client->id,
            'full_name' => $client->full_name,
            'document_number' => (string) $client->document_number,
            'show_url' => $this->buildClientShowUrl(
                $request,
                (int) $client->id,
                (int) ($client->gym_id ?? 0),
                null
            ),
        ]);
    }

    /**
     * Show one client scoped by gym.
     */
    public function show(Request $request, string $contextGym, int $client): View|RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $todayDate = now()->toDateString();

        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select([
                'id',
                'gym_id',
                'first_name',
                'last_name',
                'document_number',
                'phone',
                'photo_path',
                'gender',
                'status',
            ])
            ->with([
                'gym:id,address_country_code',
                'credentials' => fn ($query) => $query
                    ->select(['id', 'gym_id', 'client_id', 'type', 'value', 'status', 'created_at'])
                    ->orderByDesc('id'),
                'memberships' => fn ($query) => $query
                    ->select([
                        'id',
                        'gym_id',
                        'client_id',
                        'plan_id',
                        'price',
                        'promotion_id',
                        'promotion_name',
                        'promotion_type',
                        'promotion_value',
                        'discount_amount',
                        'bonus_days',
                        'starts_at',
                        'ends_at',
                        'status',
                        'created_at',
                    ])
                    ->with(['plan:id,gym_id,name,duration_days,duration_unit,duration_months,price,status'])
                    ->orderByDesc('ends_at')
                    ->orderByDesc('id'),
                'attendances' => fn ($query) => $query
                    ->select(['id', 'gym_id', 'client_id', 'credential_id', 'date', 'time'])
                    ->with(['credential:id,client_id,type,value,status'])
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->limit(10),
            ])
            ->findOrFail($client);

        $clientGymId = (int) $clientModel->gym_id;
        if (ActiveGymContext::isGlobal($request)) {
            $clientGymSlug = trim((string) Gym::query()
                ->whereKey($clientGymId)
                ->value('slug'));

            if ($clientGymSlug !== '') {
                $params = [
                    'contextGym' => $clientGymSlug,
                    'client' => (int) $clientModel->id,
                ];

                $pwaMode = strtolower(trim((string) $request->query('pwa_mode', '')));
                if ($pwaMode === 'standalone') {
                    $params['pwa_mode'] = 'standalone';
                }

                return redirect()->route('clients.show', $params);
            }
        }

        $canManagePromotions = $this->planAccessService->canForGym($clientGymId, 'promotions');

        $plans = Plan::query()
            ->forGym($clientGymId)
            ->active()
            ->select(['id', 'gym_id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price', 'status'])
            ->orderBy('name')
            ->get();

        $promotions = collect();
        if ($canManagePromotions) {
            $promotions = Promotion::query()
                ->forGym($clientGymId)
                ->active()
                ->applicableOn($todayDate)
                ->select([
                    'id',
                    'gym_id',
                    'plan_id',
                    'name',
                    'type',
                    'value',
                    'starts_at',
                    'ends_at',
                    'max_uses',
                    'times_used',
                ])
                ->with(['plan:id,name'])
                ->orderByDesc('id')
                ->get();
        }

        $activeQrCredential = $clientModel->credentials
            ->where('type', 'qr')
            ->where('status', 'active')
            ->first();

        $activeQrSvg = null;
        if ($activeQrCredential) {
            $activeQrSvg = QrCode::format('svg')
                ->size(210)
                ->margin(1)
                ->generate($activeQrCredential->value);
        }

        $latestMembership = $clientModel->memberships->first();
        $membershipState = 'none';

        if ($latestMembership) {
            $isMembershipActive = $latestMembership->status === 'active'
                && $latestMembership->ends_at !== null
                && $latestMembership->ends_at->toDateString() >= $todayDate;

            $membershipState = $isMembershipActive ? 'active' : 'expired';
        }

        $recentMembershipPayments = collect();
        $membershipIds = $clientModel->memberships->pluck('id')->filter()->values();
        if ($membershipIds->isNotEmpty()) {
            $recentMembershipPayments = CashMovement::query()
                ->forGym($clientGymId)
                ->whereIn('membership_id', $membershipIds)
                ->where('type', 'income')
                ->select([
                    'id',
                    'gym_id',
                    'membership_id',
                    'created_by',
                    'type',
                    'amount',
                    'method',
                    'description',
                    'occurred_at',
                ])
                ->with([
                    'createdBy:id,name',
                    'membership:id,plan_id',
                    'membership.plan:id,name',
                ])
                ->orderByDesc('occurred_at')
                ->limit(10)
                ->get();
        }

        return view('clients.show', [
            'client' => $clientModel,
            'plans' => $plans,
            'activeQrCredential' => $activeQrCredential,
            'activeQrSvg' => $activeQrSvg,
            'latestMembership' => $latestMembership,
            'membershipState' => $membershipState,
            'recentMembershipPayments' => $recentMembershipPayments,
            'promotions' => $promotions,
            'canManagePromotions' => $canManagePromotions,
        ]);
    }

    public function updatePhoto(UpdateClientPhotoRequest $request, string $contextGym, int $client): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['clients' => 'Selecciona una sucursal especifica para editar clientes.']);
        }

        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'photo_path'])
            ->findOrFail($client);

        $newPath = $request->file('photo')->store('clients', 'public');
        $oldPath = $clientModel->photo_path;

        $clientModel->update([
            'photo_path' => $newPath,
        ]);

        $this->deletePublicAssetIfLocal($oldPath);

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'Foto del cliente actualizada correctamente.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function deletePublicAssetIfLocal(?string $path): void
    {
        $assetPath = trim((string) $path);
        if (
            $assetPath === ''
            || str_starts_with($assetPath, 'http://')
            || str_starts_with($assetPath, 'https://')
        ) {
            return;
        }

        Storage::disk('public')->delete(ltrim($assetPath, '/'));
    }

    private function applyQuickFilter(
        Builder $query,
        string $quickFilter,
        string $todayDate,
        string $expiringLimitDate,
        array $gymIds
    ): void {
        if ($quickFilter === 'active') {
            $this->applyActiveMembershipConstraint($query, $todayDate);

            return;
        }

        if ($quickFilter === 'expiring') {
            $this->applyActiveMembershipConstraint($query, $todayDate);
            $query->whereDate('lm.ends_at', '<=', $expiringLimitDate);

            return;
        }

        if ($quickFilter === 'expired') {
            $this->applyExpiredConstraint($query, $todayDate);

            return;
        }

        if ($quickFilter === 'attended_today') {
            $query->whereExists(function ($subQuery) use ($todayDate, $gymIds): void {
                $subQuery->selectRaw('1')
                    ->from('attendances as att')
                    ->whereIn('att.gym_id', $gymIds)
                    ->whereColumn('att.gym_id', 'clients.gym_id')
                    ->whereDate('att.date', $todayDate)
                    ->whereColumn('att.client_id', 'clients.id');
            });
        }
    }

    private function applyActiveMembershipConstraint(Builder $query, string $todayDate): void
    {
        $query->whereNotNull('lm.latest_membership_id')
            ->where(function (Builder $membershipState): void {
                $membershipState->whereNull('lm.membership_status')
                    ->orWhere('lm.membership_status', '!=', 'cancelled');
            })
            ->whereDate('lm.ends_at', '>=', $todayDate);
    }

    private function applyExpiredConstraint(Builder $query, string $todayDate): void
    {
        $query->whereNotNull('lm.latest_membership_id')
            ->where(function (Builder $membershipQuery) use ($todayDate): void {
                $membershipQuery->whereDate('lm.ends_at', '<', $todayDate)
                    ->orWhere('lm.membership_status', 'cancelled');
        });
    }

    /**
     * @return array{total:int,active:int,expiring:int,expired:int}
     */
    private function buildStats(Builder $query, string $todayDate, string $expiringLimitDate): array
    {
        $total = (clone $query)->count('clients.id');

        $activeBase = clone $query;
        $this->applyActiveMembershipConstraint($activeBase, $todayDate);
        $active = (clone $activeBase)->count('clients.id');
        $expiring = (clone $activeBase)
            ->whereDate('lm.ends_at', '<=', $expiringLimitDate)
            ->count('clients.id');

        $expiredBase = clone $query;
        $this->applyExpiredConstraint($expiredBase, $todayDate);
        $expired = $expiredBase->count('clients.id');

        return [
            'total' => $total,
            'active' => $active,
            'expiring' => $expiring,
            'expired' => $expired,
        ];
    }

    /**
     * @return array<int, float>
     */
    private function resolveMembershipPayments(array $gymIds, LengthAwarePaginator $clients): array
    {
        $membershipIds = collect($clients->items())
            ->pluck('latest_membership_id')
            ->filter()
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($membershipIds->isEmpty()) {
            return [];
        }

        return CashMovement::query()
            ->forGyms($gymIds)
            ->where('type', 'income')
            ->whereIn('membership_id', $membershipIds)
            ->selectRaw('membership_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('membership_id')
            ->pluck('total', 'membership_id')
            ->map(static fn ($amount): float => (float) $amount)
            ->all();
    }

    /**
     * @param array<int, float> $paymentsByMembership
     * @return array<string, mixed>
     */
    private function buildClientCardRow(Client $client, array $paymentsByMembership, Carbon $now): array
    {
        $membershipEndsAtDate = $client->membership_ends_at ? Carbon::parse((string) $client->membership_ends_at)->startOfDay() : null;
        $membershipExpiresAt = $membershipEndsAtDate?->copy()->endOfDay();
        $remainingMinutes = $membershipExpiresAt !== null
            ? $now->diffInMinutes($membershipExpiresAt, false)
            : null;
        $daysRemaining = $remainingMinutes !== null ? (int) floor($remainingMinutes / 1440) : null;
        $hasMembership = ! empty($client->latest_membership_id);
        $membershipStatus = (string) ($client->membership_status ?? '');
        $isMembershipExpired = $remainingMinutes !== null && $remainingMinutes < 0;
        $isCancelled = $membershipStatus === 'cancelled';

        $generalStatus = match (true) {
            ! $hasMembership => 'inactive',
            $isCancelled || $isMembershipExpired => 'expired',
            default => 'active',
        };

        $planPrice = (float) ($client->plan_price ?? 0);
        $paidAmount = (float) ($paymentsByMembership[(int) ($client->latest_membership_id ?? 0)] ?? 0.0);
        $paymentStatus = match (true) {
            ! $hasMembership => 'pending',
            $generalStatus === 'expired' => 'expired',
            $planPrice > 0 && $paidAmount + 0.0001 >= $planPrice => 'up_to_date',
            default => 'pending',
        };

        $isExpiring = $remainingMinutes !== null && $remainingMinutes > 0 && $remainingMinutes <= (7 * 1440) && $generalStatus === 'active';
        $isAttendedToday = $client->last_attendance_date !== null
            && Carbon::parse((string) $client->last_attendance_date)->isSameDay($now);

        return [
            'id' => (int) $client->id,
            'full_name' => (string) $client->full_name,
            'document_number' => (string) $client->document_number,
            'photo_url' => $this->resolvePhotoUrl($client->photo_path),
            'initials' => $this->initialsOf($client->full_name),
            'gym_name' => trim((string) ($client->gym_name ?? '')) !== '' ? (string) $client->gym_name : ('Sede #'.((int) ($client->gym_id ?? 0))),
            'plan_name' => trim((string) ($client->plan_name ?? '')) !== '' ? (string) $client->plan_name : 'Sin plan',
            'membership_ends_at' => $membershipEndsAtDate?->toDateString(),
            'membership_ends_at_human' => $membershipExpiresAt?->translatedFormat('d M Y H:i') ?? 'N/A',
            'days_remaining' => $daysRemaining,
            'days_badge' => $this->daysBadge($remainingMinutes),
            'payment_status' => $paymentStatus,
            'payment_badge' => $this->paymentBadge($paymentStatus),
            'last_checkin_label' => $this->lastCheckinLabel($client, $now),
            'general_status' => $generalStatus,
            'status_badge' => $this->generalStatusBadge($generalStatus),
            'is_expiring' => $isExpiring,
            'is_expired' => $generalStatus === 'expired',
            'attended_today' => $isAttendedToday,
        ];
    }

    /**
     * @return array{label:string,variant:string}
     */
    private function paymentBadge(string $status): array
    {
        return match ($status) {
            'up_to_date' => ['label' => 'Al día', 'variant' => 'success'],
            'expired' => ['label' => 'Vencido', 'variant' => 'danger'],
            default => ['label' => 'Pendiente', 'variant' => 'warning'],
        };
    }

    /**
     * @return array{label:string,variant:string}
     */
    private function generalStatusBadge(string $status): array
    {
        return match ($status) {
            'active' => ['label' => 'Activo', 'variant' => 'success'],
            'expired' => ['label' => 'Vencido', 'variant' => 'danger'],
            default => ['label' => 'Inactivo', 'variant' => 'muted'],
        };
    }

    /**
     * @return array{label:string,tone:'success'|'warning'|'danger'|'danger-strong'|'neutral'}
     */
    private function daysBadge(?int $remainingMinutes): array
    {
        if ($remainingMinutes === null) {
            return ['label' => 'N/A', 'tone' => 'neutral'];
        }

        if ($remainingMinutes <= 0) {
            return ['label' => 'VENCIDO', 'tone' => 'danger-strong'];
        }

        $daysFloat = $remainingMinutes / 1440;
        $days = intdiv($remainingMinutes, 1440);
        $hours = intdiv($remainingMinutes % 1440, 60);
        $minutes = $remainingMinutes % 60;

        if ($remainingMinutes < 60) {
            $label = "{$minutes}m";
        } elseif ($remainingMinutes < 1440) {
            $label = intdiv($remainingMinutes, 60).'h';
        } elseif ($days === 1 && $hours === 0) {
            $label = '1d';
        } elseif ($hours > 0) {
            $label = "{$days}d {$hours}h";
        } else {
            $label = "{$days}d";
        }

        if ($daysFloat < 5) {
            return ['label' => $label, 'tone' => 'danger'];
        }

        if ($daysFloat <= 10) {
            return ['label' => $label, 'tone' => 'warning'];
        }

        return ['label' => $label, 'tone' => 'success'];
    }

    private function lastCheckinLabel(Client $client, Carbon $now): string
    {
        if (! $client->last_attendance_date) {
            return 'Sin asistencia';
        }

        $date = Carbon::parse((string) $client->last_attendance_date)->startOfDay();
        $timeValue = trim((string) ($client->last_attendance_time ?? ''));
        $timeLabel = $timeValue !== '' ? mb_substr($timeValue, 0, 5) : '--:--';

        if ($date->isSameDay($now)) {
            return "Hoy {$timeLabel}";
        }

        $daysAgo = $date->diffInDays($now);
        if ($daysAgo <= 30) {
            $relative = $daysAgo === 1 ? 'Hace 1 día' : "Hace {$daysAgo} días";

            return "{$relative} {$timeLabel}";
        }

        return $date->translatedFormat('d M Y')." {$timeLabel}";
    }

    private function initialsOf(string $fullName): string
    {
        $parts = collect(preg_split('/\s+/', trim($fullName)) ?: [])
            ->filter()
            ->take(2)
            ->map(static fn ($part): string => mb_strtoupper(mb_substr((string) $part, 0, 1)));

        return $parts->isNotEmpty() ? $parts->implode('') : '--';
    }

    private function buildClientShowUrl(
        Request $request,
        int $clientId,
        int $clientGymId,
        ?string $clientGymSlug = null
    ): string {
        $isGlobal = ActiveGymContext::isGlobal($request);

        if ($isGlobal) {
            $targetSlug = trim((string) $clientGymSlug);
            if ($targetSlug === '' && $clientGymId > 0) {
                $targetSlug = trim((string) Gym::query()
                    ->whereKey($clientGymId)
                    ->value('slug'));
            }

            if ($targetSlug !== '') {
                $params = [
                    'contextGym' => $targetSlug,
                    'client' => $clientId,
                ];

                $pwaMode = strtolower(trim((string) $request->query('pwa_mode', '')));
                if ($pwaMode === 'standalone') {
                    $params['pwa_mode'] = 'standalone';
                }

                return route('clients.show', $params);
            }
        }

        $params = ['client' => $clientId];
        $contextGym = trim((string) ($request->route('contextGym') ?? ''));
        if ($contextGym !== '') {
            $params['contextGym'] = $contextGym;
        }
        if ($isGlobal) {
            $params['scope'] = 'global';
        }

        $pwaMode = strtolower(trim((string) $request->query('pwa_mode', '')));
        if ($pwaMode === 'standalone') {
            $params['pwa_mode'] = 'standalone';
        }

        return route('clients.show', $params);
    }

    private function resolvePhotoUrl(?string $photoPath): ?string
    {
        if (! is_string($photoPath) || trim($photoPath) === '') {
            return null;
        }

        $path = trim($photoPath);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            return url('/'.ltrim($path, '/'));
        }

        return url('/storage/'.ltrim($path, '/'));
    }
}

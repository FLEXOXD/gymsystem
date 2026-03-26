<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientPhotoRequest;
use App\Http\Requests\UpdateClientAppAccountRequest;
use App\Http\Requests\ResetClientAppPasswordRequest;
use App\Modules\Clients\Actions\RegisterClientAction;
use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Models\MembershipAdjustment;
use App\Models\Plan;
use App\Models\ProductSale;
use App\Models\Promotion;
use App\Models\User;
use App\Services\CashSessionService;
use App\Services\ClientProgressOverviewService;
use App\Services\PlanAccessService;
use App\Support\ActiveGymContext;
use App\Support\ClientAudit;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use RuntimeException;

class ClientController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService,
        private readonly PlanAccessService $planAccessService,
        private readonly RegisterClientAction $registerClientAction,
        private readonly ClientProgressOverviewService $clientProgressOverviewService
    ) {
    }

    /**
     * Display all clients for current gym.
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $activePlanKey = $this->planAccessService->currentPlanKeyForGym($gymId);
        $isPlanControl = $activePlanKey === 'basico';
        $isPlanProfessional = $activePlanKey === 'profesional';
        $isPlanPremium = $activePlanKey === 'premium';
        $canManagePromotions = $this->planAccessService->canForGym($gymId, 'promotions');
        $canManageClientAccounts = $this->planAccessService->canForGym($gymId, 'client_accounts');
        $clientAccountsAccessByGym = $this->planAccessService->canForGyms($gymIds, 'client_accounts');
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
                'clients.created_by',
                'clients.created_by_name_snapshot',
                'clients.created_by_role_snapshot',
                'clients.last_managed_by',
                'clients.last_managed_by_name_snapshot',
                'clients.last_managed_by_role_snapshot',
                'clients.last_managed_at',
                'clients.first_name',
                'clients.last_name',
                'clients.document_number',
                'clients.phone',
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

        $professionalBaseQuery = clone $clientsQuery;
        $professionalStats = $this->buildStats(clone $professionalBaseQuery, $todayDate, $expiringLimitDate);

        $clientsWithPromotionBase = clone $professionalBaseQuery;
        $this->applyActiveMembershipConstraint($clientsWithPromotionBase, $todayDate);
        $clientsWithPromotionCount = (clone $clientsWithPromotionBase)
            ->whereNotNull('lm.promotion_name')
            ->count('clients.id');

        $attendedTodayBase = clone $professionalBaseQuery;
        $this->applyQuickFilter($attendedTodayBase, 'attended_today', $todayDate, $expiringLimitDate, $gymIds);
        $attendedTodayCount = $attendedTodayBase->count('clients.id');

        $clientsWithAppAccessCount = 0;
        $activeClientsWithAppAccessCount = 0;
        $activeClientsWithoutAppAccessCount = 0;
        if ($isPlanPremium) {
            $clientsWithAppAccessCount = (clone $professionalBaseQuery)
                ->whereNotNull('clients.app_username')
                ->where('clients.app_username', '!=', '')
                ->count('clients.id');

            $activeClientsWithAppAccessBase = clone $professionalBaseQuery;
            $this->applyActiveMembershipConstraint($activeClientsWithAppAccessBase, $todayDate);
            $activeClientsWithAppAccessCount = (clone $activeClientsWithAppAccessBase)
                ->whereNotNull('clients.app_username')
                ->where('clients.app_username', '!=', '')
                ->count('clients.id');

            $activeClientsWithoutAppAccessCount = max(0, (int) ($professionalStats['active'] ?? 0) - $activeClientsWithAppAccessCount);
        }

        $this->applyQuickFilter($clientsQuery, $quickFilter, $todayDate, $expiringLimitDate, $gymIds);

        $stats = $this->buildStats(clone $clientsQuery, $todayDate, $expiringLimitDate);

        $clients = $clientsQuery
            ->orderByDesc('clients.id')
            ->paginate(20)
            ->withQueryString();

        $paymentsByMembership = $this->resolveMembershipPayments($gymIds, $clients);
        $clientMutationPolicies = $this->resolveClientMutationPolicies($request, $clients->getCollection());
        $clients->setCollection(
            $clients->getCollection()->map(function (Client $client) use ($paymentsByMembership, $now, $request, $clientMutationPolicies, $clientAccountsAccessByGym): array {
                $row = $this->buildClientCardRow($client, $paymentsByMembership, $now);
                $mutationPolicy = $clientMutationPolicies[(int) ($client->gym_id ?? 0)] ?? [
                    'can_manage' => false,
                    'owner_scope_label' => 'dueño del gimnasio',
                    'owner_modal_hint' => 'Confirma con la contraseña del dueño del gimnasio.',
                ];
                $canShowProgress = (bool) ($clientAccountsAccessByGym[(int) ($client->gym_id ?? 0)] ?? false);
                $row['show_url'] = $this->buildClientShowUrl(
                    $request,
                    (int) $client->id,
                    (int) ($client->gym_id ?? 0),
                    trim((string) ($client->gym_slug ?? ''))
                );
                $row['can_show_progress'] = $canShowProgress;
                $row['progress_url'] = $canShowProgress
                    ? $this->buildClientShowUrl(
                        $request,
                        (int) $client->id,
                        (int) ($client->gym_id ?? 0),
                        trim((string) ($client->gym_slug ?? '')),
                        ['tab' => 'progress']
                    )
                    : null;
                $row['can_manage'] = (bool) ($mutationPolicy['can_manage'] ?? false);
                $row['owner_scope_label'] = (string) ($mutationPolicy['owner_scope_label'] ?? 'dueño del gimnasio');
                $row['owner_modal_hint'] = (string) ($mutationPolicy['owner_modal_hint'] ?? 'Confirma con la contraseña del dueño del gimnasio.');
                $row['edit_url'] = $row['can_manage']
                    ? $this->buildClientActionUrl(
                        $request,
                        'clients.basic.update',
                        (int) $client->id,
                        (int) ($client->gym_id ?? 0),
                        trim((string) ($client->gym_slug ?? ''))
                    )
                    : null;
                $row['delete_url'] = $row['can_manage']
                    ? $this->buildClientActionUrl(
                        $request,
                        'clients.destroy',
                        (int) $client->id,
                        (int) ($client->gym_id ?? 0),
                        trim((string) ($client->gym_slug ?? ''))
                    )
                    : null;

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
        $createModalErrorKeys = [
            'first_name',
            'last_name',
            'document_number',
            'phone',
            'gender',
            'photo',
            'start_membership',
            'plan_id',
            'membership_starts_at',
            'membership_price',
            'promotion_id',
            'payment_method',
            'amount_paid',
            'create_app_account',
            'app_username',
            'app_password',
            'app_password_confirmation',
            'cash',
        ];
        $hasCreateErrors = $errorBag
            ? collect($createModalErrorKeys)->contains(static fn (string $key): bool => $errorBag->has($key))
            : false;
        $openCreateModal = (bool) old('_open_create_modal', false) || $hasCreateErrors;
        $professionalClientsDashboard = $this->buildPlanProfessionalClientsDashboard(
            isPlanProfessional: $isPlanProfessional,
            canManagePromotions: $canManagePromotions,
            stats: $professionalStats,
            activePromotionsCount: (int) $promotions->count(),
            clientsWithPromotionCount: $clientsWithPromotionCount,
            attendedTodayCount: $attendedTodayCount
        );
        $premiumClientsDashboard = $this->buildPlanPremiumClientsDashboard(
            isPlanPremium: $isPlanPremium,
            stats: $professionalStats,
            clientsWithAppAccessCount: $clientsWithAppAccessCount,
            activeClientsWithAppAccessCount: $activeClientsWithAppAccessCount,
            activeClientsWithoutAppAccessCount: $activeClientsWithoutAppAccessCount,
            attendedTodayCount: $attendedTodayCount
        );
        $planControlClientsDashboard = $this->buildPlanControlClientsDashboard(
            isPlanControl: $isPlanControl,
            stats: $professionalStats,
            attendedTodayCount: $attendedTodayCount,
            quickFilter: $quickFilter,
            hasSearch: $search !== '',
            isGlobalScope: ActiveGymContext::isGlobal($request)
        );

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
            'planControlClientsDashboard' => $planControlClientsDashboard,
            'professionalClientsDashboard' => $professionalClientsDashboard,
            'premiumClientsDashboard' => $premiumClientsDashboard,
        ]);
    }

    /**
     * Store a new client for current gym.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $redirectParams = $this->panelRouteParams($request);
        if (ActiveGymContext::isGlobal($request)) {
            return $this->redirectToPanelTarget($request, 'clients.index', $redirectParams)
                ->withErrors(['clients' => 'Selecciona una sucursal específica para crear clientes.']);
        }

        $gymId = $this->resolveGymId($request);
        $canManagePromotions = $this->planAccessService->canForGym($gymId, 'promotions');
        $canManageClientAccounts = $this->planAccessService->canForGym($gymId, 'client_accounts');
        $data = $request->validated();
        $startsMembership = (bool) ($data['start_membership'] ?? false);
        $createAppAccount = (bool) ($data['create_app_account'] ?? false);

        if ($startsMembership && ! $this->cashSessionService->getOpenSession($gymId)) {
            return $this->redirectToPanelTarget($request, 'clients.index', $redirectParams)
                ->withErrors(['cash' => 'Debe abrir caja para registrar una membresía.'])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        if ($startsMembership && ! $canManagePromotions && ! empty($data['promotion_id'])) {
            return $this->redirectToPanelTarget($request, 'clients.index', $redirectParams)
                ->withErrors(['promotion_id' => 'Tu plan actual no incluye promociones.'])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        if ($createAppAccount && ! $canManageClientAccounts) {
            return $this->redirectToPanelTarget($request, 'clients.index', $redirectParams)
                ->withErrors(['app_username' => 'Tu plan actual no incluye cuentas cliente con usuario y contraseña.'])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('clients', 'public');
        }

        try {
            $this->registerClientAction->execute(
                gymId: $gymId,
                actor: $this->resolveActor($request),
                data: $data,
                canManagePromotions: $canManagePromotions,
                photoPath: $photoPath,
                canManageClientAccounts: $canManageClientAccounts
            );
        } catch (RuntimeException $exception) {
            return $this->redirectToPanelTarget($request, 'clients.index', $redirectParams)
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput(array_merge($request->except('photo'), ['_open_create_modal' => 1]));
        }

        return $this->redirectToPanelTarget($request, 'clients.index', $redirectParams)
            ->with('status', 'Cliente creado correctamente.');
    }

    private function redirectToPanelTarget(Request $request, string $fallbackRoute, array $fallbackParams = []): RedirectResponse
    {
        $redirectTo = trim((string) $request->input('redirect_to', ''));
        if ($redirectTo !== '' && str_starts_with($redirectTo, '/') && ! str_starts_with($redirectTo, '//')) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route($fallbackRoute, $fallbackParams);
    }

    /**
     * @return array<string, string>
     */
    private function panelRouteParams(Request $request): array
    {
        $params = [];
        $contextGym = trim((string) $request->route('contextGym'));
        if ($contextGym !== '') {
            $params['contextGym'] = $contextGym;
        }

        if (strtolower(trim((string) $request->query('pwa_mode', ''))) === 'standalone') {
            $params['pwa_mode'] = 'standalone';
        }

        return $params;
    }

    public function updateBasic(Request $request, string $contextGym, int $client): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return back()->withErrors(['clients' => 'Selecciona una sede específica para editar clientes.']);
        }

        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'first_name', 'last_name', 'phone'])
            ->findOrFail($client);

        $authorization = $this->resolveClientMutationAuthorization($request, $clientModel);
        if ($authorization['message'] !== null) {
            return back()->withErrors(['clients' => $authorization['message']]);
        }

        $data = $request->validate([
            'edit_client_id' => ['required', 'integer'],
            'edit_first_name' => ['required', 'string', 'max:120'],
            'edit_last_name' => ['required', 'string', 'max:120'],
            'edit_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'edit_first_name.required' => 'Ingresa el nombre del cliente.',
            'edit_first_name.max' => 'El nombre no puede superar 120 caracteres.',
            'edit_last_name.required' => 'Ingresa el apellido del cliente.',
            'edit_last_name.max' => 'El apellido no puede superar 120 caracteres.',
            'edit_phone.required' => 'Ingresa el teléfono del cliente.',
            'edit_phone.max' => 'El teléfono no puede superar 30 caracteres.',
            'edit_phone.regex' => 'El teléfono solo puede contener números y los símbolos + - ( ).',
        ]);

        if ((int) $data['edit_client_id'] !== (int) $clientModel->id) {
            return back()
                ->withErrors(['edit_client_id' => 'No se pudo identificar el cliente a editar.'])
                ->withInput();
        }

        $clientModel->update([
            'first_name' => trim((string) $data['edit_first_name']),
            'last_name' => trim((string) $data['edit_last_name']),
            'phone' => trim((string) $data['edit_phone']),
            ...ClientAudit::managementAttributesFromUser($this->resolveActor($request)),
        ]);

        return back()->with('status', 'Cliente actualizado correctamente.');
    }

    public function destroy(Request $request, string $contextGym, int $client): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return back()->withErrors(['clients' => 'Selecciona una sede específica para eliminar clientes.']);
        }

        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'first_name', 'last_name'])
            ->findOrFail($client);

        $authorization = $this->resolveClientMutationAuthorization($request, $clientModel);
        if ($authorization['message'] !== null) {
            return back()->withErrors(['clients' => $authorization['message']]);
        }

        $data = $request->validate([
            'delete_client_id' => ['required', 'integer'],
            'owner_password' => ['required', 'string'],
        ], [
            'owner_password.required' => 'Ingresa la contraseña del dueño autorizado.',
        ]);

        if ((int) $data['delete_client_id'] !== (int) $clientModel->id) {
            return back()
                ->withErrors(['delete_client_id' => 'No se pudo identificar el cliente a eliminar.'])
                ->withInput($request->except('owner_password'));
        }

        /** @var User|null $approvingOwner */
        $approvingOwner = $authorization['owner'];
        if (! $approvingOwner instanceof User) {
            return back()->withErrors(['clients' => 'No se encontró un dueño autorizado para validar esta acción.']);
        }

        if (! Hash::check((string) $data['owner_password'], (string) $approvingOwner->password)) {
            $passwordMessage = (bool) ($authorization['is_branch_managed'] ?? false)
                ? 'La contraseña del dueño principal no es correcta.'
                : 'La contraseña del dueño del gimnasio no es correcta.';

            return back()
                ->withErrors(['owner_password' => $passwordMessage])
                ->withInput($request->except('owner_password'));
        }

        $clientName = $clientModel->full_name;
        $clientModel->delete();

        return back()->with('status', 'Cliente '.$clientName.' eliminado correctamente.');
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
                'created_by',
                'created_by_name_snapshot',
                'created_by_role_snapshot',
                'last_managed_by',
                'last_managed_by_name_snapshot',
                'last_managed_by_role_snapshot',
                'last_managed_at',
                'created_at',
                'first_name',
                'last_name',
                'document_number',
                'app_username',
                'app_password',
                'phone',
                'photo_path',
                'gender',
                'status',
            ])
            ->with([
                'gym:id,address_country_code,timezone',
                'createdByUser:id,name,is_active',
                'lastManagedByUser:id,name,is_active',
                'fitnessProfile:id,gym_id,client_id,goal,secondary_goal,experience_level,days_per_week,session_minutes',
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
        $canManageClientAccounts = $this->planAccessService->canForGym($clientGymId, 'client_accounts');
        $canUseSalesInventory = $this->planAccessService->canForGym($clientGymId, 'sales_inventory');
        $canViewReports = $request->user() instanceof User
            && $request->user()->isOwner()
            && $this->planAccessService->canForGym($clientGymId, 'reports_base');
        $activePlanKey = $this->planAccessService->currentPlanKeyForGym($clientGymId);
        $isPlanControl = $activePlanKey === 'basico';
        $isPlanProfessional = $activePlanKey === 'profesional';
        $isPlanPremium = $activePlanKey === 'premium';
        $canShowProgress = $canManageClientAccounts;
        $canAdjustMemberships = $request->user() instanceof User && $request->user()->isOwner();

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
            $membershipStartsAt = $latestMembership->starts_at?->copy()->startOfDay();
            $membershipEndsAt = $latestMembership->ends_at?->copy()->startOfDay();
            $isCancelledMembership = (string) $latestMembership->status === 'cancelled';
            $isScheduledMembership = $membershipStartsAt !== null
                && $membershipStartsAt->toDateString() > $todayDate
                && ! $isCancelledMembership;
            $isMembershipActive = ! $isCancelledMembership
                && $membershipStartsAt !== null
                && $membershipStartsAt->toDateString() <= $todayDate
                && $membershipEndsAt !== null
                && $membershipEndsAt->toDateString() >= $todayDate;

            $membershipState = match (true) {
                $isCancelledMembership => 'cancelled',
                $isScheduledMembership => 'scheduled',
                $isMembershipActive => 'active',
                default => 'expired',
            };
        }

        $suggestedPromotion = null;
        if ($canManagePromotions && $promotions->isNotEmpty()) {
            $currentPlanId = (int) ($latestMembership?->plan_id ?? 0);
            $suggestedPromotion = $promotions->first(function (Promotion $promotion) use ($currentPlanId): bool {
                $promotionPlanId = $promotion->plan_id !== null ? (int) $promotion->plan_id : null;

                return $promotionPlanId === null || $promotionPlanId === $currentPlanId;
            }) ?? $promotions->first();
        }

        $recentMembershipPayments = collect();
        $membershipIds = $clientModel->memberships->pluck('id')->filter()->values();
        $totalMembershipRevenue = 0.0;
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

            $totalMembershipRevenue = (float) CashMovement::query()
                ->forGym($clientGymId)
                ->whereIn('membership_id', $membershipIds)
                ->where('type', 'income')
                ->sum('amount');
        }

        $lastMembershipPayment = $recentMembershipPayments->first();

        $membershipAdjustments = collect();
        if ($membershipIds->isNotEmpty()) {
            $membershipAdjustments = MembershipAdjustment::query()
                ->forGym($clientGymId)
                ->whereIn('membership_id', $membershipIds)
                ->select([
                    'id',
                    'gym_id',
                    'client_id',
                    'membership_id',
                    'performed_by',
                    'type',
                    'reason',
                    'notes',
                    'previous_starts_at',
                    'previous_ends_at',
                    'new_starts_at',
                    'new_ends_at',
                    'days_delta',
                    'created_at',
                ])
                ->with([
                    'performedBy:id,name',
                    'membership:id,plan_id',
                    'membership.plan:id,name',
                ])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        $productSalesCount = 0;
        $productSalesRevenue = 0.0;
        $lastProductSale = null;
        if ($canUseSalesInventory && Schema::hasTable('product_sales')) {
            $productSalesAggregate = ProductSale::query()
                ->forGym($clientGymId)
                ->where('client_id', (int) $clientModel->id)
                ->selectRaw('COUNT(*) as total_sales')
                ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
                ->first();

            $productSalesCount = (int) ($productSalesAggregate->total_sales ?? 0);
            $productSalesRevenue = (float) ($productSalesAggregate->total_revenue ?? 0);

            $lastProductSale = ProductSale::query()
                ->forGym($clientGymId)
                ->where('client_id', (int) $clientModel->id)
                ->orderByDesc('sold_at')
                ->first(['id', 'sold_at', 'total_amount']);
        }

        $progressOverview = $canShowProgress
            ? $this->clientProgressOverviewService->build(
                client: $clientModel,
                latestMembership: $latestMembership,
                gymTimezone: (string) ($clientModel->gym?->timezone ?? '')
            )
            : [];

        $clientTimezone = trim((string) ($clientModel->gym?->timezone ?? ''));
        $lastAttendanceDate = $clientModel->attendances->first()?->date?->copy()->startOfDay();
        $daysSinceLastAttendance = $lastAttendanceDate
            ? $lastAttendanceDate->diffInDays(now()->copy()->startOfDay(), false)
            : null;
        $planControlClientDashboard = $this->buildPlanControlClientDetailDashboard(
            isPlanControl: $isPlanControl,
            membershipState: $membershipState,
            latestMembership: $latestMembership,
            lastMembershipPayment: $lastMembershipPayment,
            daysSinceLastAttendance: $daysSinceLastAttendance
        );
        $professionalClientDashboard = $this->buildPlanProfessionalClientDetailDashboard(
            isPlanProfessional: $isPlanProfessional,
            membershipState: $membershipState,
            latestMembership: $latestMembership,
            lastMembershipPayment: $lastMembershipPayment,
            totalMembershipRevenue: $totalMembershipRevenue,
            suggestedPromotion: $suggestedPromotion,
            currentPromotionName: trim((string) ($latestMembership?->promotion_name ?? '')),
            productSalesCount: $productSalesCount,
            productSalesRevenue: $productSalesRevenue,
            lastProductSale: $lastProductSale,
            daysSinceLastAttendance: $daysSinceLastAttendance
        );
        $premiumClientDashboard = $this->buildPlanPremiumClientDetailDashboard(
            isPlanPremium: $isPlanPremium,
            membershipState: $membershipState,
            latestMembership: $latestMembership,
            lastMembershipPayment: $lastMembershipPayment,
            totalMembershipRevenue: $totalMembershipRevenue,
            productSalesCount: $productSalesCount,
            productSalesRevenue: $productSalesRevenue,
            lastProductSale: $lastProductSale,
            daysSinceLastAttendance: $daysSinceLastAttendance,
            appUsername: trim((string) ($clientModel->app_username ?? ''))
        );

        return view('clients.show', [
            'client' => $clientModel,
            'plans' => $plans,
            'activeQrCredential' => $activeQrCredential,
            'activeQrSvg' => $activeQrSvg,
            'latestMembership' => $latestMembership,
            'membershipState' => $membershipState,
            'recentMembershipPayments' => $recentMembershipPayments,
            'membershipAdjustments' => $membershipAdjustments,
            'promotions' => $promotions,
            'canManagePromotions' => $canManagePromotions,
            'canManageClientAccounts' => $canManageClientAccounts,
            'canUseSalesInventory' => $canUseSalesInventory,
            'canViewReports' => $canViewReports,
            'canShowProgress' => $canShowProgress,
            'canAdjustMemberships' => $canAdjustMemberships,
            'progressOverview' => $progressOverview,
            'planControlClientDashboard' => $planControlClientDashboard,
            'professionalClientDashboard' => $professionalClientDashboard,
            'premiumClientDashboard' => $premiumClientDashboard,
            'clientCreationAudit' => $this->buildClientAuditSummary(
                nameSnapshot: (string) ($clientModel->created_by_name_snapshot ?? ''),
                roleSnapshot: (string) ($clientModel->created_by_role_snapshot ?? ''),
                linkedUser: $clientModel->createdByUser,
                timestamp: $clientModel->created_at,
                timezone: $clientTimezone
            ),
            'clientLastManagementAudit' => $this->buildClientAuditSummary(
                nameSnapshot: (string) ($clientModel->last_managed_by_name_snapshot ?? ''),
                roleSnapshot: (string) ($clientModel->last_managed_by_role_snapshot ?? ''),
                linkedUser: $clientModel->lastManagedByUser,
                timestamp: $clientModel->last_managed_at,
                timezone: $clientTimezone
            ),
        ]);
    }

    public function updatePhoto(UpdateClientPhotoRequest $request, string $contextGym, int $client): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['clients' => 'Selecciona una sucursal específica para editar clientes.']);
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
            ...ClientAudit::managementAttributesFromUser($this->resolveActor($request)),
        ]);

        $this->deletePublicAssetIfLocal($oldPath);

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'Foto del cliente actualizada correctamente.');
    }

    public function updateAppAccount(
        UpdateClientAppAccountRequest $request,
        string $contextGym,
        int $client
    ): RedirectResponse {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['clients' => 'Selecciona una sucursal específica para editar usuarios app cliente.']);
        }

        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'app_username', 'app_password'])
            ->findOrFail($client);

        $clientModel->update([
            'app_username' => (string) $request->validated('app_username'),
            ...ClientAudit::managementAttributesFromUser($this->resolveActor($request)),
        ]);

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'Usuario app del cliente actualizado correctamente.');
    }

    public function resetAppPassword(
        ResetClientAppPasswordRequest $request,
        string $contextGym,
        int $client
    ): RedirectResponse {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['clients' => 'Selecciona una sucursal específica para restablecer contraseñas app cliente.']);
        }

        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'app_username', 'app_password'])
            ->findOrFail($client);

        if (trim((string) ($clientModel->app_username ?? '')) === '') {
            return redirect()
                ->route('clients.show', $clientModel->id)
                ->withErrors(['app_username' => 'Define primero el usuario app del cliente.'])
                ->withInput(['active_tab' => 'app_access']);
        }

        $clientModel->update([
            'app_password' => Hash::make((string) $request->validated('app_password')),
            ...ClientAudit::managementAttributesFromUser($this->resolveActor($request)),
        ]);

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'Contraseña app del cliente restablecida correctamente.');
    }

    private function resolveActor(Request $request): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403, 'Usuario no autenticado.');

        return $actor;
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    /**
     * @return array{name:string,role_label:string,display:string,state:?string,state_variant:string,timestamp_label:string}
     */
    private function buildClientAuditSummary(
        string $nameSnapshot,
        string $roleSnapshot,
        ?User $linkedUser,
        mixed $timestamp,
        string $timezone = ''
    ): array {
        $displayName = trim($nameSnapshot) !== '' ? trim($nameSnapshot) : 'Sin registro';
        $state = ClientAudit::linkedUserState($linkedUser, $roleSnapshot);
        $resolvedAt = null;

        if ($timestamp instanceof Carbon) {
            $resolvedAt = $timestamp->copy();
        } elseif ($timestamp !== null && trim((string) $timestamp) !== '') {
            $resolvedAt = Carbon::parse((string) $timestamp);
        }

        if ($resolvedAt && $timezone !== '') {
            $resolvedAt = $resolvedAt->copy()->timezone($timezone);
        }

        $stateVariant = match ($state) {
            'Usuario archivado' => 'warning',
            'Usuario eliminado' => 'danger',
            default => 'muted',
        };

        return [
            'name' => $displayName,
            'role_label' => ClientAudit::roleLabel($roleSnapshot),
            'display' => ClientAudit::actorDisplay($displayName, $roleSnapshot),
            'state' => $state,
            'state_variant' => $stateVariant,
            'timestamp_label' => $resolvedAt?->format('Y-m-d H:i') ?? 'Sin fecha',
        ];
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
            ->whereDate('lm.starts_at', '<=', $todayDate)
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
     * @param array{total:int,active:int,expiring:int,expired:int} $stats
     * @return array<string, mixed>|null
     */
    private function buildPlanProfessionalClientsDashboard(
        bool $isPlanProfessional,
        bool $canManagePromotions,
        array $stats,
        int $activePromotionsCount,
        int $clientsWithPromotionCount,
        int $attendedTodayCount
    ): ?array {
        if (! $isPlanProfessional) {
            return null;
        }

        $renewalOpportunities = max(0, (int) ($stats['expiring'] ?? 0) + (int) ($stats['expired'] ?? 0));
        $alerts = [];

        if ($renewalOpportunities > 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Renovaciones por mover',
                'description' => 'Hay '.$renewalOpportunities.' cliente(s) entre por vencer y vencidos. Esta es la cola comercial mas cercana para cobrar.',
            ];
        }

        if ((int) ($stats['expired'] ?? 0) > 0) {
            $alerts[] = [
                'tone' => 'danger',
                'title' => 'Clientes por recuperar',
                'description' => 'Tienes '.(int) $stats['expired'].' cliente(s) vencidos. Atenderlos rapido evita que se enfrien.',
            ];
        }

        if ($canManagePromotions && $activePromotionsCount === 0 && $renewalOpportunities > 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Promo recomendada',
                'description' => 'Activa una promo corta para acelerar renovaciones y recuperar vencidos desde esta misma base.',
            ];
        } elseif ($clientsWithPromotionCount > 0) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Promos en movimiento',
                'description' => 'Ya tienes '.$clientsWithPromotionCount.' cliente(s) con promo aplicada en membresias activas.',
            ];
        }

        if ($attendedTodayCount === 0 && (int) ($stats['active'] ?? 0) > 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Sin movimiento hoy',
                'description' => 'Todavia no registras asistencias hoy. Cruza recepcion con clientes para empujar renovacion y uso real.',
            ];
        }

        if ($alerts === []) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Base comercial estable',
                'description' => 'Clientes, promos y renovaciones estan en una buena posicion para seguir creciendo.',
            ];
        }

        return [
            'headline' => 'Foco comercial sobre tu base de clientes',
            'summary' => 'Usa esta vista para detectar renovaciones, mover promos y no dejar clientes frios dentro del plan Profesional.',
            'renewal_opportunities' => $renewalOpportunities,
            'expired_count' => (int) ($stats['expired'] ?? 0),
            'active_promotions_count' => $activePromotionsCount,
            'clients_with_promotion_count' => $clientsWithPromotionCount,
            'attended_today_count' => $attendedTodayCount,
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @param array{total:int,active:int,expiring:int,expired:int} $stats
     * @return array<string, mixed>|null
     */
    private function buildPlanPremiumClientsDashboard(
        bool $isPlanPremium,
        array $stats,
        int $clientsWithAppAccessCount,
        int $activeClientsWithAppAccessCount,
        int $activeClientsWithoutAppAccessCount,
        int $attendedTodayCount
    ): ?array {
        if (! $isPlanPremium) {
            return null;
        }

        $renewalPipeline = max(0, (int) ($stats['expiring'] ?? 0) + (int) ($stats['expired'] ?? 0));
        $alerts = [];

        if ($activeClientsWithoutAppAccessCount > 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Accesos app pendientes',
                'description' => 'Hay '.$activeClientsWithoutAppAccessCount.' cliente(s) activos sin usuario app. Ese es el upgrade premium mas visible dentro de esta base.',
            ];
        }

        if ($clientsWithAppAccessCount > 0) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Portal cliente listo',
                'description' => 'Ya tienes '.$clientsWithAppAccessCount.' cliente(s) con acceso listo para portal y app.',
            ];
        } else {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Primer acceso premium por activar',
                'description' => 'Crear el primer usuario cliente vuelve visible la diferencia real entre Profesional y Premium.',
            ];
        }

        if ($renewalPipeline > 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Renovaciones premium por mover',
                'description' => 'Tienes '.$renewalPipeline.' cliente(s) entre por vencer y vencidos. Puedes cerrarlos con una experiencia mucho mas completa.',
            ];
        }

        if ($attendedTodayCount === 0 && (int) ($stats['active'] ?? 0) > 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Sin uso hoy',
                'description' => 'Hay clientes activos premium sin movimiento en recepcion hoy. Conviene revisar si ya usan la experiencia cliente.',
            ];
        }

        return [
            'headline' => 'Base premium lista para activar acceso cliente y renovar mejor',
            'summary' => 'Cruza renovaciones, usuarios app y actividad real sin convertir esta pantalla en otro dashboard separado.',
            'renewal_pipeline' => $renewalPipeline,
            'clients_with_app_access_count' => $clientsWithAppAccessCount,
            'active_clients_with_app_access_count' => $activeClientsWithAppAccessCount,
            'active_clients_without_app_access_count' => $activeClientsWithoutAppAccessCount,
            'attended_today_count' => $attendedTodayCount,
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @param array{total:int,active:int,expiring:int,expired:int} $stats
     * @return array<string, mixed>|null
     */
    private function buildPlanControlClientsDashboard(
        bool $isPlanControl,
        array $stats,
        int $attendedTodayCount,
        string $quickFilter,
        bool $hasSearch,
        bool $isGlobalScope
    ): ?array {
        if (! $isPlanControl) {
            return null;
        }

        $renewalPipeline = max(0, (int) ($stats['expiring'] ?? 0) + (int) ($stats['expired'] ?? 0));
        $activeCount = (int) ($stats['active'] ?? 0);
        $expiredCount = (int) ($stats['expired'] ?? 0);
        $expiringCount = (int) ($stats['expiring'] ?? 0);

        $headline = $renewalPipeline > 0
            ? 'Tu base ya tiene clientes listos para renovar y cobrar'
            : 'Clientes ordenados para recepcion, caja y seguimiento diario';

        $summary = $renewalPipeline > 0
            ? 'Usa esta vista para moverte rapido entre por vencer, vencidos y clientes activos sin convertir el basico en otro dashboard.'
            : 'Mantiene una sola lectura limpia: buscar, filtrar, cobrar y revisar vencimientos desde la misma tabla.';

        if ($isGlobalScope) {
            $summary = 'Vista consolidada de clientes por sede. Puedes revisar vencimientos y actividad, pero las altas y cambios quedan para una sucursal puntual.';
        } elseif ($hasSearch) {
            $summary = 'La tabla esta filtrada por busqueda. Mantienes arriba la lectura completa del pipeline para no perder el contexto.';
        } elseif ($quickFilter !== 'all') {
            $summary = 'Estas trabajando sobre el filtro "'.$quickFilter.'". La lectura superior mantiene el pipeline completo para seguir cobrando sin perder foco.';
        }

        $priorities = [
            [
                'label' => 'Pipeline de renovacion',
                'value' => (string) $renewalPipeline,
                'note' => $renewalPipeline > 0
                    ? 'Por vencer + vencidos listos para cobrar.'
                    : 'Hoy no tienes renovaciones urgentes en cola.',
                'tone' => $renewalPipeline > 0 ? 'warning' : 'neutral',
            ],
            [
                'label' => 'Clientes activos',
                'value' => (string) $activeCount,
                'note' => $activeCount > 0
                    ? 'Base vigente que puede seguir entrando hoy.'
                    : 'Todavia no hay membresias vigentes.',
                'tone' => $activeCount > 0 ? 'success' : 'neutral',
            ],
            [
                'label' => 'Asistieron hoy',
                'value' => (string) $attendedTodayCount,
                'note' => $attendedTodayCount > 0
                    ? 'Recepcion ya registra movimiento real.'
                    : 'Recepcion aun no marca accesos hoy.',
                'tone' => $attendedTodayCount > 0 ? 'info' : 'neutral',
            ],
        ];

        $primaryAction = $expiredCount > 0
            ? ['label' => 'Ver vencidos', 'filter' => 'expired']
            : ($expiringCount > 0
                ? ['label' => 'Ver por vencer', 'filter' => 'expiring']
                : ['label' => 'Ver activos', 'filter' => 'active']);

        return [
            'headline' => $headline,
            'summary' => $summary,
            'priorities' => $priorities,
            'primary_action' => $primaryAction,
            'renewal_pipeline' => $renewalPipeline,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanProfessionalClientDetailDashboard(
        bool $isPlanProfessional,
        string $membershipState,
        ?Membership $latestMembership,
        ?CashMovement $lastMembershipPayment,
        float $totalMembershipRevenue,
        ?Promotion $suggestedPromotion,
        string $currentPromotionName,
        int $productSalesCount,
        float $productSalesRevenue,
        ?ProductSale $lastProductSale,
        ?int $daysSinceLastAttendance
    ): ?array {
        if (! $isPlanProfessional) {
            return null;
        }

        $headline = match ($membershipState) {
            'expired', 'cancelled' => 'Recupera este cliente con una renovacion rapida',
            'scheduled' => 'Cliente listo para iniciar en fecha programada',
            'active' => 'Mantiene al cliente activo y cerca de una nueva venta',
            default => 'Activa una primera membresia y empieza a mover valor',
        };

        $summary = match ($membershipState) {
            'expired', 'cancelled' => 'La ficha ya concentra la renovacion, la promo sugerida y el seguimiento para no dejarlo caer.',
            'scheduled' => 'Usa esta vista para confirmar arranque, asistencia y siguiente cobro sin perder contexto.',
            'active' => 'Cruza membresia, promo y ventas de productos desde una sola lectura comercial.',
            default => 'Puedes usar esta ficha para cobrar, activar promo y llevar el seguimiento desde el dia uno.',
        };

        $lastPaymentLabel = 'Sin cobro registrado';
        $lastPaymentAmount = 0.0;
        if ($lastMembershipPayment) {
            $lastPaymentAmount = (float) ($lastMembershipPayment->amount ?? 0);
            $lastPaymentLabel = $lastMembershipPayment->occurred_at?->format('Y-m-d H:i') ?? 'Cobro sin fecha';
        }

        $promotionTitle = 'Sin promo sugerida';
        $promotionSubtitle = 'Puedes renovar sin promo o crear una desde Planes.';
        $suggestedPromotionId = null;
        if ($currentPromotionName !== '') {
            $promotionTitle = $currentPromotionName;
            $promotionSubtitle = 'Ya existe una promo aplicada en la membresia mas reciente.';
        } elseif ($suggestedPromotion) {
            $promotionTitle = (string) $suggestedPromotion->name;
            $promotionSubtitle = 'Sugerida para renovar este cliente sin salir de la ficha.';
            $suggestedPromotionId = (int) $suggestedPromotion->id;
        }

        $lastProductSaleLabel = 'Sin ventas de productos';
        if ($lastProductSale) {
            $lastProductSaleLabel = $lastProductSale->sold_at?->format('Y-m-d H:i') ?? 'Venta sin fecha';
        }

        $attendanceLabel = 'Sin asistencias registradas';
        if ($daysSinceLastAttendance !== null) {
            if ($daysSinceLastAttendance <= 0) {
                $attendanceLabel = 'Asistio hoy';
            } elseif ($daysSinceLastAttendance === 1) {
                $attendanceLabel = 'Ultima asistencia hace 1 dia';
            } else {
                $attendanceLabel = 'Ultima asistencia hace '.$daysSinceLastAttendance.' dias';
            }
        }

        $alerts = [];

        if (in_array($membershipState, ['expired', 'cancelled'], true)) {
            $alerts[] = [
                'tone' => 'danger',
                'title' => 'Renovacion prioritaria',
                'description' => 'El cliente ya no esta cubierto. Cobrar hoy es la accion comercial mas urgente.',
            ];
        } elseif ($membershipState === 'active' && $latestMembership?->ends_at !== null) {
            $daysLeft = now()->copy()->startOfDay()->diffInDays($latestMembership->ends_at->copy()->startOfDay(), false);
            if ($daysLeft <= 7) {
                $alerts[] = [
                    'tone' => 'warning',
                    'title' => 'Ventana de cierre abierta',
                    'description' => 'La membresia vence pronto. Aprovecha esta ficha para renovar antes de que caiga en vencido.',
                ];
            }
        }

        if ($currentPromotionName !== '') {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Promo ya aplicada',
                'description' => 'La ultima membresia ya uso una promo. Puedes apoyarte en ese historial para la siguiente renovacion.',
            ];
        } elseif ($suggestedPromotion) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Promo disponible',
                'description' => 'Tienes una promo activa compatible con este cliente para cerrar renovacion con menos friccion.',
            ];
        }

        if ($daysSinceLastAttendance !== null && $daysSinceLastAttendance >= 7 && $membershipState === 'active') {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Uso reciente bajo',
                'description' => 'El cliente sigue vigente pero no ha venido en varios dias. Conviene reactivarlo antes de la siguiente renovacion.',
            ];
        }

        if ($productSalesCount === 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Cross-sell pendiente',
                'description' => 'Aun no hay compras de productos. Esta ficha ya puede empujar una venta extra desde el plan Profesional.',
            ];
        } else {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Cliente con venta adicional',
                'description' => 'Este cliente ya compro productos. Repetir esa venta puede subir el ticket sin cambiar de flujo.',
            ];
        }

        return [
            'headline' => $headline,
            'summary' => $summary,
            'current_plan_id' => $latestMembership?->plan_id !== null ? (int) $latestMembership->plan_id : null,
            'last_payment_amount' => round($lastPaymentAmount, 2),
            'last_payment_label' => $lastPaymentLabel,
            'total_membership_revenue' => round($totalMembershipRevenue, 2),
            'promotion_title' => $promotionTitle,
            'promotion_subtitle' => $promotionSubtitle,
            'suggested_promotion_id' => $suggestedPromotionId,
            'product_sales_count' => $productSalesCount,
            'product_sales_revenue' => round($productSalesRevenue, 2),
            'last_product_sale_label' => $lastProductSaleLabel,
            'attendance_label' => $attendanceLabel,
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanPremiumClientDetailDashboard(
        bool $isPlanPremium,
        string $membershipState,
        ?Membership $latestMembership,
        ?CashMovement $lastMembershipPayment,
        float $totalMembershipRevenue,
        int $productSalesCount,
        float $productSalesRevenue,
        ?ProductSale $lastProductSale,
        ?int $daysSinceLastAttendance,
        string $appUsername
    ): ?array {
        if (! $isPlanPremium) {
            return null;
        }

        $headline = match ($membershipState) {
            'expired', 'cancelled' => 'Recupera al cliente y completa su experiencia premium',
            'scheduled' => 'Cliente premium listo para arrancar con acceso y seguimiento',
            'active' => 'Cliente listo para renovar, usar app y sostener valor',
            default => 'Activa membresia y acceso cliente desde esta misma ficha',
        };

        $summary = match ($membershipState) {
            'expired', 'cancelled' => 'No solo toca renovar: tambien conviene dejar listo su acceso app para que el premium se note de inmediato.',
            'scheduled' => 'La ficha ya puede dejar listo el cobro, la asistencia y el acceso cliente antes del arranque.',
            'active' => 'Cruza cobro, acceso app, asistencia y venta adicional dentro de una sola cabecera premium.',
            default => 'Esta ficha puede encender la primera membresia y el primer acceso cliente sin romper el flujo.',
        };

        $hasAppAccess = $appUsername !== '';
        $lastPaymentLabel = 'Sin cobro registrado';
        $lastPaymentAmount = 0.0;
        if ($lastMembershipPayment) {
            $lastPaymentAmount = (float) ($lastMembershipPayment->amount ?? 0);
            $lastPaymentLabel = $lastMembershipPayment->occurred_at?->format('Y-m-d H:i') ?? 'Cobro sin fecha';
        }

        $lastProductSaleLabel = 'Sin ventas de productos';
        if ($lastProductSale) {
            $lastProductSaleLabel = $lastProductSale->sold_at?->format('Y-m-d H:i') ?? 'Venta sin fecha';
        }

        $appAccessValue = $hasAppAccess ? $appUsername : 'Pendiente';
        $appAccessNote = $hasAppAccess
            ? 'Portal y app listos para este cliente.'
            : 'Todavia no tiene usuario app configurado.';

        $attendanceLabel = 'Sin asistencias registradas';
        if ($daysSinceLastAttendance !== null) {
            if ($daysSinceLastAttendance <= 0) {
                $attendanceLabel = 'Asistio hoy';
            } elseif ($daysSinceLastAttendance === 1) {
                $attendanceLabel = 'Ultima asistencia hace 1 dia';
            } else {
                $attendanceLabel = 'Ultima asistencia hace '.$daysSinceLastAttendance.' dias';
            }
        }

        $alerts = [];

        if (! $hasAppAccess) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Acceso app pendiente',
                'description' => 'Este cliente aun no tiene usuario app. Activarlo hace visible la experiencia premium sin cambiar de pantalla.',
            ];
        } else {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Acceso premium listo',
                'description' => 'El cliente ya tiene usuario app. Portal y experiencia movil quedan listos para operar.',
            ];
        }

        if (in_array($membershipState, ['expired', 'cancelled'], true)) {
            $alerts[] = [
                'tone' => 'danger',
                'title' => 'Renovacion prioritaria',
                'description' => 'La cobertura del cliente ya cayo. Conviene renovar y dejar resuelta tambien la parte digital.',
            ];
        } elseif ($membershipState === 'active' && $daysSinceLastAttendance !== null && $daysSinceLastAttendance >= 7) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Uso premium por reactivar',
                'description' => 'El cliente sigue activo pero lleva varios dias sin venir. Reforzar su uso de app y asistencia ayuda a la siguiente renovacion.',
            ];
        }

        if ($productSalesCount === 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Venta adicional pendiente',
                'description' => 'Aun no hay compras de productos en esta ficha. El premium tambien puede subir valor por cliente.',
            ];
        }

        return [
            'headline' => $headline,
            'summary' => $summary,
            'current_plan_id' => $latestMembership?->plan_id !== null ? (int) $latestMembership->plan_id : null,
            'last_payment_amount' => round($lastPaymentAmount, 2),
            'last_payment_label' => $lastPaymentLabel,
            'total_membership_revenue' => round($totalMembershipRevenue, 2),
            'app_access_ready' => $hasAppAccess,
            'app_access_value' => $appAccessValue,
            'app_access_note' => $appAccessNote,
            'product_sales_count' => $productSalesCount,
            'product_sales_revenue' => round($productSalesRevenue, 2),
            'last_product_sale_label' => $lastProductSaleLabel,
            'attendance_label' => $attendanceLabel,
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanControlClientDetailDashboard(
        bool $isPlanControl,
        string $membershipState,
        ?Membership $latestMembership,
        ?CashMovement $lastMembershipPayment,
        ?int $daysSinceLastAttendance
    ): ?array {
        if (! $isPlanControl) {
            return null;
        }

        $headline = match ($membershipState) {
            'expired', 'cancelled' => 'Cliente listo para recuperar desde esta misma ficha',
            'scheduled' => 'Cliente programado y listo para arrancar en fecha',
            'active' => 'Ficha lista para renovar, cobrar y mandar a recepcion',
            default => 'Activa su primera membresia y deja esta ficha operativa',
        };

        $summary = match ($membershipState) {
            'expired', 'cancelled' => 'La accion correcta aqui es directa: revisar vencimiento, cobrar y devolverlo a operacion sin pasos extra.',
            'scheduled' => 'Esta ficha mantiene claro el arranque, el vencimiento y el siguiente cobro sin cargar de ruido la pantalla.',
            'active' => 'Cruza estado, ultimo cobro y asistencia reciente desde una sola lectura limpia para una sede.',
            default => 'Todavia no hay membresia activa. Puedes cobrar la primera y dejar listo el acceso desde aqui mismo.',
        };

        $statusValue = match ($membershipState) {
            'expired' => 'Vencida',
            'cancelled' => 'Cancelada',
            'scheduled' => 'Programada',
            'active' => 'Activa',
            default => 'Sin membresia',
        };

        $statusTone = match ($membershipState) {
            'expired', 'cancelled' => 'warning',
            'active' => 'success',
            default => 'neutral',
        };

        $statusNote = $latestMembership?->ends_at
            ? 'Vence '.$latestMembership->ends_at->format('Y-m-d')
            : 'Sin fecha de vencimiento registrada.';

        $lastPaymentAmount = 0.0;
        $lastPaymentLabel = 'Sin cobro registrado todavia.';
        if ($lastMembershipPayment) {
            $lastPaymentAmount = (float) ($lastMembershipPayment->amount ?? 0);
            $lastPaymentLabel = $lastMembershipPayment->occurred_at?->format('Y-m-d H:i') ?? 'Cobro sin fecha';
        }

        $attendanceValue = 'Sin registro';
        $attendanceNote = 'Aun no hay ingresos registrados.';
        $attendanceTone = 'neutral';
        if ($daysSinceLastAttendance !== null) {
            if ($daysSinceLastAttendance <= 0) {
                $attendanceValue = 'Hoy';
                $attendanceNote = 'Ya tuvo movimiento de recepcion hoy.';
                $attendanceTone = 'info';
            } elseif ($daysSinceLastAttendance === 1) {
                $attendanceValue = 'Ayer';
                $attendanceNote = 'Ultimo ingreso hace 1 dia.';
                $attendanceTone = 'info';
            } else {
                $attendanceValue = $daysSinceLastAttendance.' dias';
                $attendanceNote = 'Ultimo ingreso registrado en recepcion.';
                $attendanceTone = $daysSinceLastAttendance >= 7 ? 'warning' : 'info';
            }
        }

        return [
            'headline' => $headline,
            'summary' => $summary,
            'current_plan_id' => $latestMembership?->plan_id !== null ? (int) $latestMembership->plan_id : null,
            'renewal_action_label' => $latestMembership ? 'Renovar membresia' : 'Cobrar membresia',
            'status_value' => $statusValue,
            'status_note' => $statusNote,
            'status_tone' => $statusTone,
            'last_payment_amount' => round($lastPaymentAmount, 2),
            'last_payment_label' => $lastPaymentLabel,
            'attendance_value' => $attendanceValue,
            'attendance_note' => $attendanceNote,
            'attendance_tone' => $attendanceTone,
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
        $membershipStartsAtDate = $client->membership_starts_at ? Carbon::parse((string) $client->membership_starts_at)->startOfDay() : null;
        $membershipExpiresAt = $membershipEndsAtDate?->copy()->endOfDay();
        $remainingMinutes = $membershipExpiresAt !== null
            ? $now->diffInMinutes($membershipExpiresAt, false)
            : null;
        $daysRemaining = $remainingMinutes !== null ? (int) floor($remainingMinutes / 1440) : null;
        $hasMembership = ! empty($client->latest_membership_id);
        $membershipStatus = (string) ($client->membership_status ?? '');
        $isMembershipExpired = $remainingMinutes !== null && $remainingMinutes < 0;
        $isCancelled = $membershipStatus === 'cancelled';
        $isScheduled = $membershipStartsAtDate !== null && $membershipStartsAtDate->greaterThan($now->copy()->startOfDay()) && ! $isCancelled;

        $generalStatus = match (true) {
            ! $hasMembership => 'inactive',
            $isCancelled || $isMembershipExpired => 'expired',
            $isScheduled => 'scheduled',
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
            'gym_id' => (int) ($client->gym_id ?? 0),
            'full_name' => (string) $client->full_name,
            'first_name' => (string) ($client->first_name ?? ''),
            'last_name' => (string) ($client->last_name ?? ''),
            'created_by_display' => ClientAudit::actorDisplay(
                (string) ($client->created_by_name_snapshot ?? ''),
                (string) ($client->created_by_role_snapshot ?? '')
            ),
            'last_managed_by_display' => ClientAudit::actorDisplay(
                (string) ($client->last_managed_by_name_snapshot ?? ''),
                (string) ($client->last_managed_by_role_snapshot ?? '')
            ),
            'document_number' => (string) $client->document_number,
            'phone' => trim((string) ($client->phone ?? '')),
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
            'scheduled' => ['label' => 'Programado', 'variant' => 'info'],
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

        $daysAgo = (int) $date->diffInDays($now->copy()->startOfDay());
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
        ?string $clientGymSlug = null,
        array $extraQuery = []
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

                $params = array_merge($params, $extraQuery);

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

        $params = array_merge($params, $extraQuery);

        return route('clients.show', $params);
    }

    private function buildClientActionUrl(
        Request $request,
        string $routeName,
        int $clientId,
        int $clientGymId,
        ?string $clientGymSlug = null
    ): string {
        $params = ['client' => $clientId];
        $contextGym = trim((string) ($request->route('contextGym') ?? ''));

        if ($contextGym === '' && $clientGymId > 0) {
            $contextGym = trim((string) ($clientGymSlug ?? ''));
        }

        if ($contextGym !== '') {
            $params['contextGym'] = $contextGym;
        }

        $pwaMode = strtolower(trim((string) $request->query('pwa_mode', '')));
        if ($pwaMode === 'standalone') {
            $params['pwa_mode'] = 'standalone';
        }

        return route($routeName, $params);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Client>  $clients
     * @return array<int, array{can_manage:bool,owner_scope_label:string,owner_modal_hint:string}>
     */
    private function resolveClientMutationPolicies(Request $request, \Illuminate\Support\Collection $clients): array
    {
        $currentUser = $request->user();
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $gymIds = $clients
            ->map(static fn (Client $client): int => (int) ($client->gym_id ?? 0))
            ->filter(static fn (int $gymId): bool => $gymId > 0)
            ->unique()
            ->values()
            ->all();

        if ($gymIds === []) {
            return [];
        }

        $branchParentMap = GymBranchLink::query()
            ->whereIn('branch_gym_id', $gymIds)
            ->pluck('hub_gym_id', 'branch_gym_id')
            ->map(static fn ($gymId): int => (int) $gymId)
            ->all();

        $approvingGymIds = collect($gymIds)
            ->map(static fn (int $gymId): int => (int) ($branchParentMap[$gymId] ?? $gymId))
            ->unique()
            ->values()
            ->all();

        $ownersByGym = User::query()
            ->whereIn('gym_id', $approvingGymIds)
            ->where('role', User::ROLE_OWNER)
            ->orderBy('id')
            ->get(['id', 'gym_id'])
            ->groupBy('gym_id')
            ->map(static fn ($rows) => $rows->first());

        $policies = [];
        foreach ($gymIds as $gymId) {
            $approvingGymId = (int) ($branchParentMap[$gymId] ?? $gymId);
            $approvingOwner = $ownersByGym->get($approvingGymId);
            $isBranchManaged = array_key_exists($gymId, $branchParentMap);

            $policies[$gymId] = [
                'can_manage' => ! $isGlobalScope
                    && $currentUser instanceof User
                    && $currentUser->isOwner()
                    && $approvingOwner instanceof User
                    && (int) $currentUser->id === (int) $approvingOwner->id,
                'owner_scope_label' => $isBranchManaged ? 'dueño principal' : 'dueño del gimnasio',
                'owner_modal_hint' => $isBranchManaged
                    ? 'Confirma con la contraseña del dueño principal de la sede matriz.'
                    : 'Confirma con la contraseña del dueño del gimnasio.',
            ];
        }

        return $policies;
    }

    /**
     * @return array{owner:?User,is_branch_managed:bool,message:?string}
     */
    private function resolveClientMutationAuthorization(Request $request, Client $client): array
    {
        $currentUser = $request->user();
        if (! $currentUser instanceof User || ! $currentUser->isOwner()) {
            return [
                'owner' => null,
                'is_branch_managed' => false,
                'message' => 'Solo el dueño autorizado puede editar o eliminar clientes.',
            ];
        }

        $branchLink = GymBranchLink::query()
            ->where('branch_gym_id', (int) $client->gym_id)
            ->first(['hub_gym_id']);

        $isBranchManaged = $branchLink instanceof GymBranchLink && (int) ($branchLink->hub_gym_id ?? 0) > 0;
        $approvingGymId = $isBranchManaged
            ? (int) ($branchLink->hub_gym_id ?? 0)
            : (int) ($client->gym_id ?? 0);

        $approvingOwner = User::query()
            ->where('gym_id', $approvingGymId)
            ->where('role', User::ROLE_OWNER)
            ->orderBy('id')
            ->first();

        if (! $approvingOwner instanceof User) {
            return [
                'owner' => null,
                'is_branch_managed' => $isBranchManaged,
                'message' => 'No se encontró un dueño autorizado para esta operación.',
            ];
        }

        if ((int) $currentUser->id !== (int) $approvingOwner->id) {
            return [
                'owner' => $approvingOwner,
                'is_branch_managed' => $isBranchManaged,
                'message' => $isBranchManaged
                    ? 'Solo el dueño principal puede editar o eliminar clientes de una sucursal.'
                    : 'Solo el dueño del gimnasio puede editar o eliminar clientes.',
            ];
        }

        return [
            'owner' => $approvingOwner,
            'is_branch_managed' => $isBranchManaged,
            'message' => null,
        ];
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

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Client;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\PresenceSession;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Promotion;
use App\Models\User;
use App\Services\CashierQuotaService;
use App\Services\PlanAccessService;
use App\Services\GymAdminActivityService;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class GymPanelController extends Controller
{
    /**
     * Main operational panel for gym users.
     */
    public function index(
        Request $request,
        PlanAccessService $planAccessService,
        CashierQuotaService $cashierQuotaService
    ): View|RedirectResponse
    {
        $gymId = ActiveGymContext::id($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $currentUser = $request->user();
        $cashierScopeUserId = $currentUser && $currentUser->isCashier()
            ? (int) $currentUser->id
            : null;
        $isCashierScoped = $cashierScopeUserId !== null;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');
        $activePlanKey = $planAccessService->currentPlanKeyForGym($gymId);
        $isPlanControl = $activePlanKey === 'basico';
        $isPlanProfessional = $activePlanKey === 'profesional';
        $isPlanPremium = $activePlanKey === 'premium';
        $isPlanBranches = $activePlanKey === 'sucursales';
        $branchLinksCount = 0;
        $branchNetworkGymsCount = 1;

        if ($isPlanBranches && ! $isCashierScoped) {
            $branchLinksCount = (int) GymBranchLink::query()
                ->where('hub_gym_id', $gymId)
                ->count();
            $branchNetworkGymsCount = max(1, $branchLinksCount + 1);
        }

        $todayDate = now();
        $today = $todayDate->copy()->startOfDay()->toDateString();
        $twoDaysLater = $todayDate->copy()->addDays(2)->toDateString();
        $cashMovementsBaseQuery = fn () => CashMovement::query()
            ->forGyms($gymIds)
            ->createdByUser($cashierScopeUserId);

        $latestMembershipSub = Membership::query()
            ->from('memberships as m')
            ->select([
                'm.id as latest_membership_id',
                'm.client_id',
                'm.plan_id',
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

        $clientsWithLatestMembership = Client::query()
            ->from('clients')
            ->whereIn('clients.gym_id', $gymIds)
            ->leftJoinSub($latestMembershipSub, 'lm', function ($join): void {
                $join->on('lm.client_id', '=', 'clients.id');
            });

        $currentMonthStart = $todayDate->copy()->startOfMonth();
        $currentMonthEnd = $todayDate->copy()->endOfMonth();
        $previousMonthStart = $todayDate->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $todayDate->copy()->subMonthNoOverflow()->endOfMonth();
        $currentYearStart = $todayDate->copy()->startOfYear();
        $currentYearEnd = $todayDate->copy()->endOfYear();

        $totalClients = (clone $clientsWithLatestMembership)->count('clients.id');

        $activeClientsBase = (clone $clientsWithLatestMembership)
            ->whereNotNull('lm.latest_membership_id')
            ->where(function ($query): void {
                $query->whereNull('lm.membership_status')
                    ->orWhere('lm.membership_status', '!=', 'cancelled');
            })
            ->whereDate('lm.starts_at', '<=', $today)
            ->whereDate('lm.ends_at', '>=', $today);

        $activeMemberships = (clone $activeClientsBase)->count('clients.id');

        $expiringSoonMemberships = (clone $activeClientsBase)
            ->whereDate('lm.ends_at', '<=', $twoDaysLater)
            ->count('clients.id');

        $expiredClientsBase = (clone $clientsWithLatestMembership)
            ->whereNotNull('lm.latest_membership_id')
            ->where(function ($membershipQuery) use ($today): void {
                $membershipQuery->whereDate('lm.ends_at', '<', $today)
                    ->orWhere('lm.membership_status', 'cancelled');
            });

        $expiredMemberships = (clone $expiredClientsBase)->count('clients.id');

        $activePlans = Plan::query()
            ->forGyms($gymIds)
            ->where('status', 'active')
            ->count();

        $checkinsToday = Attendance::query()
            ->forGyms($gymIds)
            ->whereDate('date', $today)
            ->count();

        $liveClientsNow = $gymId > 0
            ? PresenceSession::query()
                ->forGym($gymId)
                ->open()
                ->whereDate('check_in_at', $today)
                ->count()
            : 0;

        $incomeToday = (float) $cashMovementsBaseQuery()
            ->whereDate('occurred_at', $today)
            ->where('type', 'income')
            ->sum('amount');

        $expenseToday = (float) $cashMovementsBaseQuery()
            ->whereDate('occurred_at', $today)
            ->where('type', 'expense')
            ->sum('amount');

        $todayBalance = round($incomeToday - $expenseToday, 2);

        $incomeYearToDate = (float) $cashMovementsBaseQuery()
            ->betweenOccurredAt($currentYearStart, $currentYearEnd)
            ->where('type', 'income')
            ->sum('amount');

        $expenseYearToDate = (float) $cashMovementsBaseQuery()
            ->betweenOccurredAt($currentYearStart, $currentYearEnd)
            ->where('type', 'expense')
            ->sum('amount');

        $netYearToDate = round($incomeYearToDate - $expenseYearToDate, 2);

        $incomeCurrentMonth = (float) $cashMovementsBaseQuery()
            ->betweenOccurredAt($currentMonthStart, $currentMonthEnd)
            ->where('type', 'income')
            ->sum('amount');

        $membershipIncomeToday = (float) $cashMovementsBaseQuery()
            ->whereDate('occurred_at', $today)
            ->where('type', 'income')
            ->whereNotNull('membership_id')
            ->sum('amount');

        $membershipIncomeCurrentMonth = (float) $cashMovementsBaseQuery()
            ->betweenOccurredAt($currentMonthStart, $currentMonthEnd)
            ->where('type', 'income')
            ->whereNotNull('membership_id')
            ->sum('amount');

        $productSalesIncomeToday = 0.0;
        $productSalesIncomeCurrentMonth = 0.0;
        if (Schema::hasTable('product_sales')) {
            $productSalesBaseQuery = fn () => ProductSale::query()
                ->forGyms($gymIds)
                ->when($cashierScopeUserId !== null, static function ($query) use ($cashierScopeUserId): void {
                    $query->where('sold_by', (int) $cashierScopeUserId);
                });

            $productSalesIncomeToday = (float) $productSalesBaseQuery()
                ->whereDate('sold_at', $today)
                ->sum('total_amount');

            $productSalesIncomeCurrentMonth = (float) $productSalesBaseQuery()
                ->betweenSoldAt($currentMonthStart, $currentMonthEnd)
                ->sum('total_amount');
        }

        $incomePreviousMonth = (float) $cashMovementsBaseQuery()
            ->betweenOccurredAt($previousMonthStart, $previousMonthEnd)
            ->where('type', 'income')
            ->sum('amount');

        $monthlyIncomeDiff = round($incomeCurrentMonth - $incomePreviousMonth, 2);
        $monthlyIncomePct = null;
        if (abs($incomePreviousMonth) > 0.00001) {
            $monthlyIncomePct = round(($monthlyIncomeDiff / $incomePreviousMonth) * 100, 1);
        }

        $incomeLast6Months = collect(range(5, 1))->map(function (int $monthsBack) use ($cashMovementsBaseQuery, $todayDate) {
            $monthDate = $todayDate->copy()->subMonthsNoOverflow($monthsBack);
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();
            $income = (float) $cashMovementsBaseQuery()
                ->betweenOccurredAt($start, $end)
                ->where('type', 'income')
                ->sum('amount');

            return [
                'label' => $monthDate->translatedFormat('M Y'),
                'income' => round($income, 2),
            ];
        })->push([
            'label' => $todayDate->copy()->translatedFormat('M Y'),
            'income' => round($incomeCurrentMonth, 2),
        ]);

        $movementsTodayCount = $cashMovementsBaseQuery()
            ->whereDate('occurred_at', $today)
            ->count();

        $openSession = null;
        if (! $isGlobalScope) {
            $openSession = CashSession::query()
                ->forGym($gymId)
                ->open()
                ->select(['id', 'gym_id', 'opened_by', 'opened_at', 'opening_balance'])
                ->with('openedBy:id,name')
                ->latest('opened_at')
                ->first();
        }

        $openSessionExpected = null;
        $openSessionScopedSummary = null;
        if ($openSession) {
            $sessionMovementsBaseQuery = fn () => CashMovement::query()
                ->forGym((int) $openSession->gym_id)
                ->where('cash_session_id', $openSession->id)
                ->createdByUser($cashierScopeUserId);

            $sessionIncome = (float) $sessionMovementsBaseQuery()
                ->where('type', 'income')
                ->sum('amount');

            $sessionExpense = (float) $sessionMovementsBaseQuery()
                ->where('type', 'expense')
                ->sum('amount');

            $openSessionScopedSummary = [
                'opening_balance' => round((float) $openSession->opening_balance, 2),
                'income_total' => round($sessionIncome, 2),
                'expense_total' => round($sessionExpense, 2),
                'net_total' => round($sessionIncome - $sessionExpense, 2),
                'visible_total' => round((float) $openSession->opening_balance + $sessionIncome - $sessionExpense, 2),
                'movements_count' => (int) $sessionMovementsBaseQuery()->count(),
            ];

            $openSessionExpected = round((float) $openSession->opening_balance + $sessionIncome - $sessionExpense, 2);
        }

        $hasMembershipHistory = Membership::query()
            ->forGyms($gymIds)
            ->exists();

        $hasCashHistory = CashSession::query()
            ->forGyms($gymIds)
            ->exists()
            || $cashMovementsBaseQuery()->exists();

        $hasCheckinHistory = Attendance::query()
            ->forGyms($gymIds)
            ->exists();

        $planControlDashboard = $this->buildPlanControlDashboard(
            isPlanControl: $isPlanControl,
            isCashierScoped: $isCashierScoped,
            isGlobalScope: $isGlobalScope,
            hasOpenSession: $openSession !== null,
            activePlans: (int) $activePlans,
            totalClients: (int) $totalClients,
            hasMembershipHistory: $hasMembershipHistory,
            hasCashHistory: $hasCashHistory,
            hasCheckinHistory: $hasCheckinHistory,
            activeMemberships: (int) $activeMemberships,
            expiringSoonMemberships: (int) $expiringSoonMemberships,
            expiredMemberships: (int) $expiredMemberships,
            checkinsToday: (int) $checkinsToday,
        );

        $productSalesMonthCount = 0;
        $averageProductTicket = 0.0;
        $activePromotionsCount = 0;
        $endingPromotionsSoonCount = 0;
        $lowStockProductsCount = 0;
        $activeCashiers = 0;
        $maxCashiers = 0;
        $clientsWithAppAccounts = 0;
        $activeClientsWithAppAccounts = 0;
        $activeClientsWithoutAppAccounts = 0;

        if (($isPlanProfessional || $isPlanPremium) && ! $isCashierScoped) {
            $productSalesMonthCount = Schema::hasTable('product_sales')
                ? (int) ProductSale::query()
                    ->forGyms($gymIds)
                    ->betweenSoldAt($currentMonthStart, $currentMonthEnd)
                    ->count()
                : 0;

            $averageProductTicket = $productSalesMonthCount > 0
                ? round($productSalesIncomeCurrentMonth / $productSalesMonthCount, 2)
                : 0.0;

            $activePromotionsCount = (int) Promotion::query()
                ->forGyms($gymIds)
                ->active()
                ->applicableOn($today)
                ->count();

            $endingPromotionsSoonCount = (int) Promotion::query()
                ->forGyms($gymIds)
                ->active()
                ->whereNotNull('ends_at')
                ->whereDate('ends_at', '<=', $twoDaysLater)
                ->count();

            if (Schema::hasTable('products')) {
                $lowStockProductsCount = (int) Product::query()
                    ->forGyms($gymIds)
                    ->where('status', 'active')
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->count();
            }

            $maxCashiers = $cashierQuotaService->maxForGym($gymId);
            $activeCashiers = (int) User::query()
                ->where('gym_id', $gymId)
                ->where('role', User::ROLE_CASHIER)
                ->where('is_active', true)
                ->count();
        }

        if ($isPlanPremium && ! $isCashierScoped) {
            $clientsWithAppAccounts = (int) Client::query()
                ->forGyms($gymIds)
                ->whereNotNull('app_username')
                ->where('app_username', '!=', '')
                ->count();

            $activeClientsWithAppAccounts = (clone $activeClientsBase)
                ->whereNotNull('clients.app_username')
                ->where('clients.app_username', '!=', '')
                ->count('clients.id');

            $activeClientsWithoutAppAccounts = max(0, (int) $activeMemberships - $activeClientsWithAppAccounts);
        }

        $planProfessionalDashboard = $this->buildPlanProfessionalDashboard(
            isPlanProfessional: $isPlanProfessional,
            isCashierScoped: $isCashierScoped,
            membershipIncomeCurrentMonth: $membershipIncomeCurrentMonth,
            productSalesIncomeCurrentMonth: $productSalesIncomeCurrentMonth,
            productSalesMonthCount: $productSalesMonthCount,
            averageProductTicket: $averageProductTicket,
            activePromotionsCount: $activePromotionsCount,
            endingPromotionsSoonCount: $endingPromotionsSoonCount,
            lowStockProductsCount: $lowStockProductsCount,
            activeCashiers: $activeCashiers,
            maxCashiers: $maxCashiers,
            expiringSoonMemberships: (int) $expiringSoonMemberships,
            expiredMemberships: (int) $expiredMemberships,
        );

        $planPremiumDashboard = $this->buildPlanPremiumDashboard(
            isPlanPremium: $isPlanPremium,
            isCashierScoped: $isCashierScoped,
            clientsWithAppAccounts: $clientsWithAppAccounts,
            activeClientsWithAppAccounts: $activeClientsWithAppAccounts,
            activeClientsWithoutAppAccounts: $activeClientsWithoutAppAccounts,
            activeCashiers: $activeCashiers,
            maxCashiers: $maxCashiers,
            activePromotionsCount: $activePromotionsCount,
            expiringSoonMemberships: (int) $expiringSoonMemberships,
            expiredMemberships: (int) $expiredMemberships
        );

        $planBranchesDashboard = $this->buildPlanBranchesDashboard(
            isPlanBranches: $isPlanBranches,
            isCashierScoped: $isCashierScoped,
            isGlobalScope: $isGlobalScope,
            branchLinksCount: $branchLinksCount,
            branchNetworkGymsCount: $branchNetworkGymsCount,
            totalClients: (int) $totalClients,
            activeMemberships: (int) $activeMemberships,
            checkinsToday: (int) $checkinsToday,
            expiringSoonMemberships: (int) $expiringSoonMemberships,
            expiredMemberships: (int) $expiredMemberships
        );

        $upcomingRenewals = (clone $activeClientsBase)
            ->leftJoin('plans as lp', function ($join): void {
                $join->on('lp.id', '=', 'lm.plan_id')
                    ->on('lp.gym_id', '=', 'clients.gym_id');
            })
            ->leftJoin('gyms as gg', 'gg.id', '=', 'clients.gym_id')
            ->whereDate('lm.ends_at', '<=', $twoDaysLater)
            ->orderBy('lm.ends_at')
            ->limit(8)
            ->get([
                'clients.id as client_id',
                'clients.gym_id',
                'clients.first_name',
                'clients.last_name',
                'lm.latest_membership_id',
                'lm.ends_at',
                'lp.name as plan_name',
                'gg.name as gym_name',
            ])
            ->map(function ($row) use ($todayDate) {
                $clientName = trim(((string) ($row->first_name ?? '')).' '.((string) ($row->last_name ?? '')));
                $endsAt = $row->ends_at ? Carbon::parse((string) $row->ends_at) : null;
                $daysLeft = $endsAt ? (int) $todayDate->copy()->startOfDay()->diffInDays($endsAt->copy()->startOfDay(), false) : null;
                $gymName = trim((string) ($row->gym_name ?? ''));
                $gymId = (int) ($row->gym_id ?? 0);

                return (object) [
                    'client_id' => (int) $row->client_id,
                    'client_name' => $clientName !== '' ? $clientName : '-',
                    'plan_name' => (string) ($row->plan_name ?? '-'),
                    'gym_name' => $gymName !== '' ? $gymName : ('Sede #'.$gymId),
                    'ends_at' => $endsAt,
                    'days_left' => $daysLeft,
                ];
            });

        $expiredRenewalCandidates = (clone $expiredClientsBase)
            ->leftJoin('plans as lp', function ($join): void {
                $join->on('lp.id', '=', 'lm.plan_id')
                    ->on('lp.gym_id', '=', 'clients.gym_id');
            })
            ->leftJoin('gyms as gg', 'gg.id', '=', 'clients.gym_id')
            ->orderByDesc('lm.ends_at')
            ->limit(8)
            ->get([
                'clients.id as client_id',
                'clients.gym_id',
                'clients.first_name',
                'clients.last_name',
                'lm.ends_at',
                'lm.membership_status',
                'lp.name as plan_name',
                'gg.name as gym_name',
            ])
            ->map(function ($row) use ($todayDate) {
                $clientName = trim(((string) ($row->first_name ?? '')).' '.((string) ($row->last_name ?? '')));
                $endsAt = $row->ends_at ? Carbon::parse((string) $row->ends_at) : null;
                $daysExpired = $endsAt ? (int) $endsAt->copy()->startOfDay()->diffInDays($todayDate->copy()->startOfDay(), false) : null;
                $gymName = trim((string) ($row->gym_name ?? ''));
                $gymId = (int) ($row->gym_id ?? 0);

                return (object) [
                    'client_id' => (int) $row->client_id,
                    'client_name' => $clientName !== '' ? $clientName : '-',
                    'plan_name' => (string) ($row->plan_name ?? '-'),
                    'gym_name' => $gymName !== '' ? $gymName : ('Sede #'.$gymId),
                    'ends_at' => $endsAt,
                    'days_expired' => $daysExpired,
                    'membership_status' => (string) ($row->membership_status ?? ''),
                ];
            });

        $todayAttendances = Attendance::query()
            ->forGyms($gymIds)
            ->whereDate('date', $today)
            ->orderByDesc('time')
            ->limit(8)
            ->with(['client:id,first_name,last_name', 'gym:id,name'])
            ->get(['id', 'gym_id', 'client_id', 'date', 'time']);

        $recentCashMovements = $cashMovementsBaseQuery()
            ->select(['id', 'gym_id', 'type', 'amount', 'method', 'description', 'created_by', 'occurred_at'])
            ->with(['createdBy:id,name', 'gym:id,name'])
            ->orderByDesc('occurred_at')
            ->limit(8)
            ->get();

        return view('panel.index', [
            'totalClients' => $totalClients,
            'activeMemberships' => $activeMemberships,
            'expiringSoonMemberships' => $expiringSoonMemberships,
            'expiredMemberships' => $expiredMemberships,
            'activePlans' => $activePlans,
            'checkinsToday' => $checkinsToday,
            'liveClientsNow' => $liveClientsNow,
            'incomeToday' => $incomeToday,
            'expenseToday' => $expenseToday,
            'todayBalance' => $todayBalance,
            'incomeYearToDate' => $incomeYearToDate,
            'expenseYearToDate' => $expenseYearToDate,
            'netYearToDate' => $netYearToDate,
            'incomeCurrentMonth' => $incomeCurrentMonth,
            'incomePreviousMonth' => $incomePreviousMonth,
            'monthlyIncomeDiff' => $monthlyIncomeDiff,
            'monthlyIncomePct' => $monthlyIncomePct,
            'incomeLast6Months' => $incomeLast6Months,
            'membershipIncomeToday' => $membershipIncomeToday,
            'membershipIncomeCurrentMonth' => $membershipIncomeCurrentMonth,
            'productSalesIncomeToday' => $productSalesIncomeToday,
            'productSalesIncomeCurrentMonth' => $productSalesIncomeCurrentMonth,
            'movementsTodayCount' => $movementsTodayCount,
            'openSession' => $openSession,
            'openSessionExpected' => $openSessionExpected,
            'openSessionScopedSummary' => $openSessionScopedSummary,
            'upcomingRenewals' => $upcomingRenewals,
            'expiredRenewalCandidates' => $expiredRenewalCandidates,
            'todayAttendances' => $todayAttendances,
            'recentCashMovements' => $recentCashMovements,
            'isCashierScoped' => $isCashierScoped,
            'activePlanKey' => $activePlanKey,
            'planControlDashboard' => $planControlDashboard,
            'planProfessionalDashboard' => $planProfessionalDashboard,
            'planPremiumDashboard' => $planPremiumDashboard,
            'planBranchesDashboard' => $planBranchesDashboard,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanControlDashboard(
        bool $isPlanControl,
        bool $isCashierScoped,
        bool $isGlobalScope,
        bool $hasOpenSession,
        int $activePlans,
        int $totalClients,
        bool $hasMembershipHistory,
        bool $hasCashHistory,
        bool $hasCheckinHistory,
        int $activeMemberships,
        int $expiringSoonMemberships,
        int $expiredMemberships,
        int $checkinsToday
    ): ?array {
        if (! $isPlanControl || $isCashierScoped) {
            return null;
        }

        $checklist = [
            [
                'completed' => $activePlans > 0,
                'label' => 'Primer plan listo',
                'description' => 'Crea al menos un plan activo para vender membresias.',
                'action_label' => 'Ir a planes',
                'action_url' => route('plans.index'),
            ],
            [
                'completed' => $totalClients > 0,
                'label' => 'Primer cliente registrado',
                'description' => 'Carga tu base inicial para empezar a cobrar y renovar.',
                'action_label' => 'Ir a clientes',
                'action_url' => route('clients.index'),
            ],
            [
                'completed' => $hasMembershipHistory,
                'label' => 'Primera membresia vendida',
                'description' => 'Activa al menos una membresia para que el panel empiece a producir.',
                'action_label' => 'Cobrar membresia',
                'action_url' => route('clients.index'),
            ],
            [
                'completed' => $hasCashHistory,
                'label' => 'Primera caja usada',
                'description' => 'Registra un turno de caja para ordenar cobros e ingresos.',
                'action_label' => 'Ir a caja',
                'action_url' => route('cash.index'),
            ],
            [
                'completed' => $hasCheckinHistory,
                'label' => 'Primer acceso controlado',
                'description' => 'Valida un ingreso en recepcion con documento, QR o RFID.',
                'action_label' => 'Ir a recepcion',
                'action_url' => route('reception.index'),
            ],
        ];

        $completedSteps = (int) collect($checklist)
            ->where('completed', true)
            ->count();
        $totalSteps = count($checklist);
        $pendingSteps = max(0, $totalSteps - $completedSteps);
        $progressPercent = $totalSteps > 0
            ? (int) round(($completedSteps / $totalSteps) * 100)
            : 0;

        $priorities = [];

        if (! $isGlobalScope && ! $hasOpenSession) {
            $priorities[] = [
                'tone' => 'warning',
                'label' => 'Caja pendiente',
                'value' => 'Sin abrir',
                'description' => 'Abre tu caja para registrar cobros del dia y no operar por fuera del sistema.',
                'action_label' => 'Abrir caja',
                'action_url' => route('cash.index'),
            ];
        }

        if ($expiredMemberships > 0) {
            $priorities[] = [
                'tone' => 'danger',
                'label' => 'Vencidos por recuperar',
                'value' => (string) $expiredMemberships,
                'description' => 'Hay clientes listos para renovar. Atenderlos rapido protege ingresos.',
                'action_label' => 'Ver vencidos',
                'action_url' => route('clients.index', ['filter' => 'expired']),
            ];
        }

        if ($expiringSoonMemberships > 0) {
            $priorities[] = [
                'tone' => 'warning',
                'label' => 'Renovaciones 48h',
                'value' => (string) $expiringSoonMemberships,
                'description' => 'Estos clientes estan a tiempo de renovar antes de caer en vencido.',
                'action_label' => 'Ver por vencer',
                'action_url' => route('clients.index', ['filter' => 'expiring']),
            ];
        }

        if ($activeMemberships === 0 && $totalClients > 0) {
            $priorities[] = [
                'tone' => 'info',
                'label' => 'Sin membresias activas',
                'value' => '0',
                'description' => 'Tu base existe, pero hoy no tienes membresias vigentes generando operacion.',
                'action_label' => 'Revisar clientes',
                'action_url' => route('clients.index'),
            ];
        }

        if ($checkinsToday === 0 && $activeMemberships > 0) {
            $priorities[] = [
                'tone' => 'info',
                'label' => 'Sin check-ins hoy',
                'value' => '0',
                'description' => 'Usa recepcion para registrar el primer acceso del dia y validar presencia real.',
                'action_label' => 'Ir a recepcion',
                'action_url' => route('reception.index'),
            ];
        }

        $priorities = array_slice($priorities, 0, 3);

        if ($pendingSteps === 0 && $priorities === []) {
            return null;
        }

        $nextAction = collect($checklist)
            ->firstWhere('completed', false);

        $headline = $pendingSteps > 0
            ? 'Te faltan '.$pendingSteps.' pasos para dejar la sede lista.'
            : 'La sede ya esta configurada para operar.';

        $summary = $pendingSteps > 0
            ? 'Completa lo pendiente para que el Plan Control se sienta util desde el primer dia.'
            : 'Ahora enfocate en cobrar, renovar y registrar accesos sin salir del panel.';

        if ($nextAction === null) {
            $nextAction = $priorities[0] ?? [
                'label' => 'Ir a recepcion',
                'description' => 'Continua operando desde el panel principal.',
                'action_label' => 'Ir a recepcion',
                'action_url' => route('reception.index'),
            ];
        }

        return [
            'headline' => $headline,
            'summary' => $summary,
            'progress_label' => $completedSteps.' de '.$totalSteps.' pasos completados',
            'progress_percent' => $progressPercent,
            'checklist' => $checklist,
            'priorities' => $priorities,
            'next_action' => [
                'label' => (string) ($nextAction['label'] ?? 'Continuar operacion'),
                'description' => (string) ($nextAction['description'] ?? 'Usa el panel para seguir operando.'),
                'action_label' => (string) ($nextAction['action_label'] ?? 'Abrir panel'),
                'action_url' => (string) ($nextAction['action_url'] ?? route('panel.index')),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanProfessionalDashboard(
        bool $isPlanProfessional,
        bool $isCashierScoped,
        float $membershipIncomeCurrentMonth,
        float $productSalesIncomeCurrentMonth,
        int $productSalesMonthCount,
        float $averageProductTicket,
        int $activePromotionsCount,
        int $endingPromotionsSoonCount,
        int $lowStockProductsCount,
        int $activeCashiers,
        int $maxCashiers,
        int $expiringSoonMemberships,
        int $expiredMemberships
    ): ?array {
        if (! $isPlanProfessional || $isCashierScoped) {
            return null;
        }

        $commercialMonthTotal = round($membershipIncomeCurrentMonth + $productSalesIncomeCurrentMonth, 2);

        $alerts = [];

        if ($maxCashiers > 0 && $activeCashiers === 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Primer cajero pendiente',
                'description' => 'Tu plan ya incluye 1 cajero. Activalo para repartir cobros y recepcion sin perder control.',
            ];
        }

        if ($lowStockProductsCount > 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Stock bajo',
                'description' => 'Tienes '.$lowStockProductsCount.' producto(s) en alerta. Reponer a tiempo evita perder ventas.',
            ];
        }

        if ($activePromotionsCount === 0 && ($expiringSoonMemberships > 0 || $expiredMemberships > 0)) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Promocion recomendada',
                'description' => 'Activa una promo para mover renovaciones y recuperar clientes por vencer o vencidos.',
            ];
        }

        if ($productSalesMonthCount === 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Ventas de productos sin movimiento',
                'description' => 'Aun no registras ventas de productos este mes. Usa inventario para abrir una fuente extra de ingresos.',
            ];
        }

        if ($alerts === []) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Flujo comercial activo',
                'description' => 'Tu plan profesional ya tiene ventas, promociones y operacion listas para seguir creciendo.',
            ];
        }

        return [
            'headline' => 'Radar comercial del plan profesional',
            'summary' => 'Convierte tu panel en una vista de ventas, promociones, reportes y control del primer cajero.',
            'commercial_month_total' => $commercialMonthTotal,
            'membership_income_current_month' => round($membershipIncomeCurrentMonth, 2),
            'product_sales_income_current_month' => round($productSalesIncomeCurrentMonth, 2),
            'product_sales_month_count' => $productSalesMonthCount,
            'average_product_ticket' => round($averageProductTicket, 2),
            'active_promotions_count' => $activePromotionsCount,
            'ending_promotions_soon_count' => $endingPromotionsSoonCount,
            'low_stock_products_count' => $lowStockProductsCount,
            'active_cashiers' => $activeCashiers,
            'max_cashiers' => $maxCashiers,
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanPremiumDashboard(
        bool $isPlanPremium,
        bool $isCashierScoped,
        int $clientsWithAppAccounts,
        int $activeClientsWithAppAccounts,
        int $activeClientsWithoutAppAccounts,
        int $activeCashiers,
        int $maxCashiers,
        int $activePromotionsCount,
        int $expiringSoonMemberships,
        int $expiredMemberships
    ): ?array {
        if (! $isPlanPremium || $isCashierScoped) {
            return null;
        }

        $alerts = [];

        if ($activeClientsWithoutAppAccounts > 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Clientes premium sin acceso app',
                'description' => 'Tienes '.$activeClientsWithoutAppAccounts.' cliente(s) activos sin usuario app. Aqui esta la mayor mejora inmediata de experiencia premium.',
            ];
        }

        if ($maxCashiers > 0 && $activeCashiers < $maxCashiers) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Cupo de equipo disponible',
                'description' => 'Tu plan permite '.$maxCashiers.' cajero(s) y hoy usas '.$activeCashiers.'. Puedes repartir operacion sin subir de plan.',
            ];
        }

        if ($clientsWithAppAccounts === 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Portal cliente sin activar',
                'description' => 'El premium ya incluye portal y app. Crear el primer acceso cliente hace visible el salto de nivel del plan.',
            ];
        } else {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Experiencia cliente lista',
                'description' => 'Ya tienes '.$clientsWithAppAccounts.' cliente(s) con acceso app. El premium ya se nota en la experiencia final del gimnasio.',
            ];
        }

        if ($activePromotionsCount === 0 && ($expiringSoonMemberships > 0 || $expiredMemberships > 0)) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Promocion premium recomendada',
                'description' => 'Todavia puedes mover renovaciones activando una promo para clientes por vencer o vencidos.',
            ];
        }

        return [
            'headline' => 'Experiencia premium lista para clientes y equipo',
            'summary' => 'Usa el premium como una capa de servicio: portal cliente, accesos app, cajeros y renovaciones dentro de una misma lectura compacta.',
            'portal_status' => $clientsWithAppAccounts > 0 ? 'Listo' : 'Activar',
            'portal_note' => $clientsWithAppAccounts > 0
                ? $clientsWithAppAccounts.' cliente(s) ya pueden usar portal y app.'
                : 'Aun no hay clientes con acceso app activo.',
            'clients_with_app_accounts' => $clientsWithAppAccounts,
            'active_clients_with_app_accounts' => $activeClientsWithAppAccounts,
            'active_clients_without_app_accounts' => $activeClientsWithoutAppAccounts,
            'active_cashiers' => $activeCashiers,
            'max_cashiers' => $maxCashiers,
            'active_promotions_count' => $activePromotionsCount,
            'expiring_memberships' => $expiringSoonMemberships,
            'expired_memberships' => $expiredMemberships,
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPlanBranchesDashboard(
        bool $isPlanBranches,
        bool $isCashierScoped,
        bool $isGlobalScope,
        int $branchLinksCount,
        int $branchNetworkGymsCount,
        int $totalClients,
        int $activeMemberships,
        int $checkinsToday,
        int $expiringSoonMemberships,
        int $expiredMemberships
    ): ?array {
        if (! $isPlanBranches || $isCashierScoped) {
            return null;
        }

        $alerts = [];

        if ($branchLinksCount === 0) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Primera sucursal pendiente',
                'description' => 'Tu plan ya habilita contexto global, pero aun no tienes sucursales vinculadas para consolidar la red.',
            ];
        }

        if ($expiredMemberships > 0) {
            $alerts[] = [
                'tone' => 'danger',
                'title' => 'Renovaciones vencidas en la red',
                'description' => 'Tienes '.$expiredMemberships.' membresia(s) vencida(s) dentro de la operacion multisucursal.',
            ];
        }

        if ($expiringSoonMemberships > 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Renovaciones por vencer',
                'description' => $expiringSoonMemberships.' cliente(s) siguen a tiempo de renovar antes de caer en vencido.',
            ];
        }

        if ($checkinsToday === 0 && $activeMemberships > 0) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Sin check-ins hoy',
                'description' => 'La red tiene membresias activas, pero aun no registra accesos hoy. Usa recepcion para validar movimiento real.',
            ];
        }

        if ($alerts === []) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Red multisucursal estable',
                'description' => 'Tu plan sucursales ya tiene una lectura clara de clientes, renovaciones y presencia desde el panel.',
            ];
        }

        return [
            'headline' => $isGlobalScope
                ? 'Tu red multisucursal ya se controla desde una sola vista.'
                : 'Estas operando la sede principal de la red multisucursal.',
            'summary' => $isGlobalScope
                ? 'Consolida clientes, membresias, presencia y alertas sin cambiar de panel entre sede principal y sucursales.'
                : 'Usa esta capa para pasar de la sede principal al contexto global y tomar decisiones sobre toda la red.',
            'branch_links_count' => $branchLinksCount,
            'branch_network_gyms_count' => $branchNetworkGymsCount,
            'total_clients' => $totalClients,
            'active_memberships' => $activeMemberships,
            'checkins_today' => $checkinsToday,
            'scope_value' => $isGlobalScope ? 'Global' : 'Principal',
            'scope_note' => $isGlobalScope
                ? 'Vista consolidada de todas las sedes vinculadas.'
                : 'Vista de la sede principal con salto rapido al consolidado.',
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    public function liveClients(Request $request): JsonResponse
    {
        $gymId = ActiveGymContext::id($request);
        $today = now()->toDateString();

        $count = $gymId > 0
            ? PresenceSession::query()
                ->forGym($gymId)
                ->open()
                ->whereDate('check_in_at', $today)
                ->count()
            : 0;

        return response()->json([
            'ok' => true,
            'count' => (int) $count,
            'today' => $today,
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    public function ownerActivityHeartbeat(Request $request, GymAdminActivityService $activityService): JsonResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isOwner()) {
            return response()->json([
                'ok' => true,
                'tracked' => false,
            ]);
        }

        $data = $request->validate([
            'signal' => ['nullable', 'string', 'max:40'],
            'channel' => ['nullable', 'string', 'max:20'],
            'route_name' => ['nullable', 'string', 'max:120'],
            'path' => ['nullable', 'string', 'max:255'],
            'remembered' => ['nullable', 'boolean'],
        ]);

        $activityService->touch($request, $user, [
            'signal' => (string) ($data['signal'] ?? 'heartbeat'),
            'channel' => isset($data['channel']) ? (string) $data['channel'] : null,
            'route_name' => (string) ($data['route_name'] ?? ($request->route()?->getName() ?? '')),
            'path' => (string) ($data['path'] ?? $request->getRequestUri()),
            'via_remember' => array_key_exists('remembered', $data)
                ? (bool) $data['remembered']
                : Auth::viaRemember(),
        ]);

        return response()->json([
            'ok' => true,
            'tracked' => true,
            'online_window_seconds' => GymAdminActivityService::ONLINE_WINDOW_SECONDS,
            'server_time' => now('UTC')->toIso8601String(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Client;
use App\Models\Membership;
use App\Models\Plan;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GymPanelController extends Controller
{
    /**
     * Main operational panel for gym users.
     */
    public function index(Request $request): View
    {
        $gymId = ActiveGymContext::id($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $todayDate = now();
        $today = $todayDate->copy()->startOfDay()->toDateString();
        $twoDaysLater = $todayDate->copy()->addDays(2)->toDateString();

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

        $incomeToday = (float) CashMovement::query()
            ->forGyms($gymIds)
            ->whereDate('occurred_at', $today)
            ->where('type', 'income')
            ->sum('amount');

        $expenseToday = (float) CashMovement::query()
            ->forGyms($gymIds)
            ->whereDate('occurred_at', $today)
            ->where('type', 'expense')
            ->sum('amount');

        $todayBalance = round($incomeToday - $expenseToday, 2);

        $incomeYearToDate = (float) CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($currentYearStart, $currentYearEnd)
            ->where('type', 'income')
            ->sum('amount');

        $expenseYearToDate = (float) CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($currentYearStart, $currentYearEnd)
            ->where('type', 'expense')
            ->sum('amount');

        $netYearToDate = round($incomeYearToDate - $expenseYearToDate, 2);

        $incomeCurrentMonth = (float) CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($currentMonthStart, $currentMonthEnd)
            ->where('type', 'income')
            ->sum('amount');

        $incomePreviousMonth = (float) CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($previousMonthStart, $previousMonthEnd)
            ->where('type', 'income')
            ->sum('amount');

        $monthlyIncomeDiff = round($incomeCurrentMonth - $incomePreviousMonth, 2);
        $monthlyIncomePct = null;
        if (abs($incomePreviousMonth) > 0.00001) {
            $monthlyIncomePct = round(($monthlyIncomeDiff / $incomePreviousMonth) * 100, 1);
        }

        $incomeLast6Months = collect(range(5, 1))->map(function (int $monthsBack) use ($gymIds, $todayDate) {
            $monthDate = $todayDate->copy()->subMonthsNoOverflow($monthsBack);
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();
            $income = (float) CashMovement::query()
                ->forGyms($gymIds)
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

        $movementsTodayCount = CashMovement::query()
            ->forGyms($gymIds)
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
        if ($openSession) {
            $sessionIncome = (float) CashMovement::query()
                ->forGym((int) $openSession->gym_id)
                ->where('cash_session_id', $openSession->id)
                ->where('type', 'income')
                ->sum('amount');

            $sessionExpense = (float) CashMovement::query()
                ->forGym((int) $openSession->gym_id)
                ->where('cash_session_id', $openSession->id)
                ->where('type', 'expense')
                ->sum('amount');

            $openSessionExpected = round((float) $openSession->opening_balance + $sessionIncome - $sessionExpense, 2);
        }

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

        $recentCashMovements = CashMovement::query()
            ->forGyms($gymIds)
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
            'movementsTodayCount' => $movementsTodayCount,
            'openSession' => $openSession,
            'openSessionExpected' => $openSessionExpected,
            'upcomingRenewals' => $upcomingRenewals,
            'expiredRenewalCandidates' => $expiredRenewalCandidates,
            'todayAttendances' => $todayAttendances,
            'recentCashMovements' => $recentCashMovements,
        ]);
    }
}

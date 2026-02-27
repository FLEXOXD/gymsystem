<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request, string $contextGym): View|RedirectResponse
    {
        $isGlobalScope = strtolower(trim((string) $request->query('scope', ''))) === 'global';
        if (! $isGlobalScope) {
            $hubGymSlug = trim((string) $request->attributes->get('hub_gym_slug', $contextGym));
            $targetContextGym = $hubGymSlug !== '' ? $hubGymSlug : $contextGym;

            return redirect()->route('branches.index', [
                'contextGym' => $targetContextGym,
                'scope' => 'global',
            ]);
        }

        $hubGymId = $this->resolveGymId($request);
        $hubGym = Gym::query()
            ->withoutDemoSessions()
            ->select([
                'id',
                'name',
                'slug',
                'timezone',
                'currency_code',
                'address_country_code',
                'address_state',
                'address_city',
            ])
            ->findOrFail($hubGymId);

        $links = GymBranchLink::query()
            ->where('hub_gym_id', $hubGymId)
            ->with(['branchGym:id,name,slug,timezone,currency_code'])
            ->orderByDesc('id')
            ->get();

        $linkedBranchIds = $links
            ->pluck('branch_gym_id')
            ->map(static fn ($value): int => (int) $value)
            ->values()
            ->all();

        $gymIds = array_values(array_unique(array_merge([$hubGymId], $linkedBranchIds)));
        $todayDate = now()->toDateString();
        $rangeFrom = Carbon::today()->subDays(29)->startOfDay();
        $rangeTo = Carbon::today()->endOfDay();

        $gyms = Gym::query()
            ->withoutDemoSessions()
            ->whereIn('id', $gymIds)
            ->with(['latestSubscription' => function ($query): void {
                $query->select([
                    'subscriptions.id',
                    'subscriptions.gym_id',
                    'subscriptions.plan_key',
                    'subscriptions.plan_name',
                    'subscriptions.status',
                    'subscriptions.ends_at',
                    'subscriptions.price',
                ]);
            }])
            ->get(['id', 'name', 'slug', 'timezone', 'currency_code'])
            ->sortBy(function (Gym $gym) use ($hubGymId): string {
                $priority = (int) ($gym->id !== $hubGymId);

                return $priority.'|'.mb_strtolower((string) $gym->name);
            })
            ->values();

        $clientsTotals = Client::query()
            ->whereIn('gym_id', $gymIds)
            ->selectRaw('gym_id, COUNT(*) as total')
            ->groupBy('gym_id')
            ->pluck('total', 'gym_id');

        $activeMembershipTotals = Membership::query()
            ->whereIn('gym_id', $gymIds)
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', $todayDate)
            ->selectRaw('gym_id, COUNT(*) as total')
            ->groupBy('gym_id')
            ->pluck('total', 'gym_id');

        $checkinsTodayTotals = Attendance::query()
            ->whereIn('gym_id', $gymIds)
            ->whereDate('date', $todayDate)
            ->selectRaw('gym_id, COUNT(*) as total')
            ->groupBy('gym_id')
            ->pluck('total', 'gym_id');

        $income30dTotals = CashMovement::query()
            ->whereIn('gym_id', $gymIds)
            ->where('type', 'income')
            ->whereBetween('occurred_at', [$rangeFrom, $rangeTo])
            ->selectRaw('gym_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('gym_id')
            ->pluck('total', 'gym_id');

        $expense30dTotals = CashMovement::query()
            ->whereIn('gym_id', $gymIds)
            ->where('type', 'expense')
            ->whereBetween('occurred_at', [$rangeFrom, $rangeTo])
            ->selectRaw('gym_id, COALESCE(SUM(amount), 0) as total')
            ->groupBy('gym_id')
            ->pluck('total', 'gym_id');

        $linkIdByBranchGymId = $links
            ->pluck('id', 'branch_gym_id')
            ->map(static fn ($value): int => (int) $value)
            ->all();

        $branchRows = $gyms->map(function (Gym $gym) use (
            $hubGymId,
            $clientsTotals,
            $activeMembershipTotals,
            $checkinsTodayTotals,
            $income30dTotals,
            $expense30dTotals,
            $linkIdByBranchGymId
        ): array {
            $gymId = (int) $gym->id;
            $income30d = (float) ($income30dTotals[$gymId] ?? 0);
            $expense30d = (float) ($expense30dTotals[$gymId] ?? 0);

            return [
                'gym_id' => $gymId,
                'is_hub' => $gymId === $hubGymId,
                'name' => (string) $gym->name,
                'slug' => (string) $gym->slug,
                'plan_name' => (string) ($gym->latestSubscription?->plan_name ?? '-'),
                'subscription_status' => (string) ($gym->latestSubscription?->status ?? '-'),
                'clients_total' => (int) ($clientsTotals[$gymId] ?? 0),
                'active_memberships' => (int) ($activeMembershipTotals[$gymId] ?? 0),
                'checkins_today' => (int) ($checkinsTodayTotals[$gymId] ?? 0),
                'income_30d' => $income30d,
                'expense_30d' => $expense30d,
                'balance_30d' => round($income30d - $expense30d, 2),
                'link_id' => $linkIdByBranchGymId[$gymId] ?? null,
            ];
        })->values();

        $kpis = [
            'total_gyms' => $branchRows->count(),
            'total_clients' => $branchRows->sum('clients_total'),
            'active_memberships' => $branchRows->sum('active_memberships'),
            'checkins_today' => $branchRows->sum('checkins_today'),
            'income_30d' => (float) $branchRows->sum('income_30d'),
            'expense_30d' => (float) $branchRows->sum('expense_30d'),
        ];
        $kpis['balance_30d'] = round((float) $kpis['income_30d'] - (float) $kpis['expense_30d'], 2);

        return view('branches.index', [
            'hubGym' => $hubGym,
            'branchRows' => $branchRows,
            'kpis' => $kpis,
        ]);
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}

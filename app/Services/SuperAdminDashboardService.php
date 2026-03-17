<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SuperAdminDashboardService
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Get global KPI cards for SuperAdmin.
     *
     * @return array<string, int|float>
     */
    public function getKpis(): array
    {
        $this->syncSubscriptionData();

        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(7)->toDateString();
        $currentCycleRevenueQuery = Subscription::query()
            ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
            ->whereIn('status', ['active', 'grace'])
            ->where(function ($query): void {
                $query
                    ->where('is_branch_managed', false)
                    ->orWhereNull('is_branch_managed');
            });

        $currentCycleRevenue = (float) (clone $currentCycleRevenueQuery)->sum('price');
        $recurringMrr = (float) ((clone $currentCycleRevenueQuery)
            ->selectRaw('COALESCE(SUM(CASE WHEN sucursales_intro_pending = 1 AND sucursales_base_price IS NOT NULL THEN sucursales_base_price ELSE price END), 0) as total')
            ->value('total') ?? 0);

        $planCountRows = Subscription::query()
            ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
            ->where(function ($query): void {
                $query
                    ->where('is_branch_managed', false)
                    ->orWhereNull('is_branch_managed');
            })
            ->selectRaw('LOWER(COALESCE(plan_key, "")) as plan_key, COUNT(*) as total')
            ->groupBy('plan_key')
            ->pluck('total', 'plan_key');

        return [
            'total_gyms' => Gym::query()->withoutDemoSessions()->count(),
            'active_gyms' => Subscription::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                ->where('status', 'active')
                ->count(),
            'grace_gyms' => Subscription::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                ->where('status', 'grace')
                ->count(),
            'suspended_gyms' => Subscription::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                ->where('status', 'suspended')
                ->count(),
            'current_cycle_revenue' => $currentCycleRevenue,
            'recurring_mrr' => $recurringMrr,
            'mrr_estimated' => $recurringMrr,
            'vencen_en_7_dias' => Subscription::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                ->where('status', '<>', 'suspended')
                ->whereDate('ends_at', '<=', $limitDate)
                ->count(),
            'en_gracia_hoy' => Subscription::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                ->where('status', 'grace')
                ->count(),
            'plan_count_basico' => (int) ($planCountRows['basico'] ?? 0),
            'plan_count_profesional' => (int) ($planCountRows['profesional'] ?? 0),
            'plan_count_premium' => (int) ($planCountRows['premium'] ?? 0),
            'plan_count_sucursales' => (int) ($planCountRows['sucursales'] ?? 0),
        ];
    }

    /**
     * Get ordered gym list with subscription details.
     */
    public function getGymsTable(): Collection
    {
        $this->syncSubscriptionData();

        $today = Carbon::today();

        return Gym::query()
            ->withoutDemoSessions()
            ->join('subscriptions', 'subscriptions.gym_id', '=', 'gyms.id')
            ->leftJoin('gyms as billing_owner_gyms', 'billing_owner_gyms.id', '=', 'subscriptions.billing_owner_gym_id')
            ->select([
                'gyms.id as gym_id',
                'gyms.name as gym_name',
                'subscriptions.plan_name',
                'subscriptions.price',
                'subscriptions.ends_at',
                'subscriptions.status',
                'subscriptions.last_payment_method',
                'subscriptions.grace_days',
                'subscriptions.billing_owner_gym_id',
                'subscriptions.is_branch_managed',
                'billing_owner_gyms.name as billing_owner_gym_name',
            ])
            ->orderBy('subscriptions.is_branch_managed')
            ->orderByRaw("CASE subscriptions.status WHEN 'grace' THEN 0 WHEN 'active' THEN 1 WHEN 'suspended' THEN 2 ELSE 3 END")
            ->orderBy('subscriptions.ends_at')
            ->get()
            ->map(function (object $row) use ($today): object {
                $endsAt = Carbon::parse($row->ends_at)->startOfDay();
                $row->is_branch_managed = (bool) ($row->is_branch_managed ?? false);
                $row->days_left = null;
                $row->grace_left = null;

                if ($row->status === 'active') {
                    $row->days_left = max(0, $today->diffInDays($endsAt, false));
                }

                if ($row->status === 'grace') {
                    $graceLimit = $endsAt->copy()->addDays((int) $row->grace_days);
                    $row->grace_left = max(0, $today->diffInDays($graceLimit, false));
                }

                return $row;
            });
    }

    /**
     * Ensure each gym has a subscription and synchronized status.
     */
    private function syncSubscriptionData(): void
    {
        Gym::query()
            ->withoutDemoSessions()
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($gyms): void {
                foreach ($gyms as $gym) {
                    $gymId = (int) $gym->id;
                    $this->subscriptionService->ensureSubscription($gymId);
                    $this->subscriptionService->checkStatus($gymId);
                }
            });
    }
}

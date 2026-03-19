<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymAdminActivityState;
use App\Models\Subscription;
use App\Models\SubscriptionChargeEvent;
use App\Services\GymAdminActivityService;
use App\Support\SuperAdminPlanCatalog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SuperAdminDashboardService
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(): array
    {
        $this->syncSubscriptionData();
        $hasChargeEventsTable = Schema::hasTable('subscription_charge_events');

        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $yearStart = $today->copy()->startOfYear();
        $yearEnd = $today->copy()->endOfYear();
        $expiringLimit = $today->copy()->addDays(7)->toDateString();

        $portfolioSubscriptions = Subscription::query()
            ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
            ->where(function ($query): void {
                $query
                    ->where('is_branch_managed', false)
                    ->orWhereNull('is_branch_managed');
            })
            ->get();
        if ($hasChargeEventsTable) {
            $portfolioSubscriptions->load('latestChargeEvent');
        }

        $activePortfolio = $portfolioSubscriptions
            ->filter(fn (Subscription $subscription): bool => in_array((string) ($subscription->status ?? ''), ['active', 'grace'], true))
            ->values();

        $currentCycleRevenue = round($activePortfolio->sum(fn (Subscription $subscription): float => $this->resolveCurrentCycleTotal($subscription)), 2);
        $recurringMrr = round($activePortfolio->sum(fn (Subscription $subscription): float => $this->resolveRecurringMonthlyValue($subscription)), 2);
        $currentCycleDiscount = round($activePortfolio->sum(fn (Subscription $subscription): float => $this->resolveCurrentCycleDiscount($subscription)), 2);
        $discountedSubscriptions = $activePortfolio
            ->filter(fn (Subscription $subscription): bool => $this->resolveCurrentCycleDiscount($subscription) > 0.009)
            ->count();

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

        $currentMonthRevenue = 0.0;
        $currentYearRevenue = 0.0;
        $currentMonthDiscount = 0.0;
        $currentYearDiscount = 0.0;
        $chargeCountMonth = 0;
        $chargeCountYear = 0;

        if ($hasChargeEventsTable) {
            $chargeEventsQuery = SubscriptionChargeEvent::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                ->whereHas('subscription', function ($query): void {
                    $query->where(function ($subQuery): void {
                        $subQuery
                            ->where('is_branch_managed', false)
                            ->orWhereNull('is_branch_managed');
                    });
                });

            $currentMonthRevenue = round((float) (clone $chargeEventsQuery)
                ->whereBetween('charged_at', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
                ->sum('total_paid'), 2);

            $currentYearRevenue = round((float) (clone $chargeEventsQuery)
                ->whereBetween('charged_at', [$yearStart->copy()->startOfDay(), $yearEnd->copy()->endOfDay()])
                ->sum('total_paid'), 2);

            $currentMonthDiscount = round((float) (clone $chargeEventsQuery)
                ->whereBetween('charged_at', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
                ->sum('discount_amount'), 2);

            $currentYearDiscount = round((float) (clone $chargeEventsQuery)
                ->whereBetween('charged_at', [$yearStart->copy()->startOfDay(), $yearEnd->copy()->endOfDay()])
                ->sum('discount_amount'), 2);

            $chargeCountMonth = (int) (clone $chargeEventsQuery)
                ->whereBetween('charged_at', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
                ->count();

            $chargeCountYear = (int) (clone $chargeEventsQuery)
                ->whereBetween('charged_at', [$yearStart->copy()->startOfDay(), $yearEnd->copy()->endOfDay()])
                ->count();
        }

        $newGymsMonth = Gym::query()
            ->withoutDemoSessions()
            ->whereBetween('created_at', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
            ->count();

        $newGymsYear = Gym::query()
            ->withoutDemoSessions()
            ->whereBetween('created_at', [$yearStart->copy()->startOfDay(), $yearEnd->copy()->endOfDay()])
            ->count();

        $avgTicketMonth = $chargeCountMonth > 0
            ? round($currentMonthRevenue / $chargeCountMonth, 2)
            : 0.0;

        return [
            'kpis' => [
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
                'current_cycle_discount' => $currentCycleDiscount,
                'recurring_mrr' => $recurringMrr,
                'annual_run_rate' => round($recurringMrr * 12, 2),
                'current_month_revenue' => $currentMonthRevenue,
                'current_year_revenue' => $currentYearRevenue,
                'current_month_discount' => $currentMonthDiscount,
                'current_year_discount' => $currentYearDiscount,
                'charges_this_month' => $chargeCountMonth,
                'charges_this_year' => $chargeCountYear,
                'new_gyms_month' => $newGymsMonth,
                'new_gyms_year' => $newGymsYear,
                'avg_ticket_month' => $avgTicketMonth,
                'discounted_subscriptions' => $discountedSubscriptions,
                'vencen_en_7_dias' => Subscription::query()
                    ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                    ->where('status', '<>', 'suspended')
                    ->whereDate('ends_at', '<=', $expiringLimit)
                    ->count(),
                'en_gracia_hoy' => Subscription::query()
                    ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
                    ->where('status', 'grace')
                    ->count(),
            ],
            'plan_mix' => collect(SuperAdminPlanCatalog::defaults())
                ->map(function (array $plan) use ($planCountRows): array {
                    $planKey = strtolower(trim((string) ($plan['plan_key'] ?? '')));

                    return [
                        'plan_key' => $planKey,
                        'name' => (string) ($plan['name'] ?? 'PLAN'),
                        'count' => (int) ($planCountRows[$planKey] ?? 0),
                    ];
                })
                ->values()
                ->all(),
            'reports' => [
                'monthly_rows' => $this->buildMonthlyRevenueRows($today, $hasChargeEventsTable),
                'owner_activity_rows' => $this->buildOwnerActivityRows(),
            ],
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
                'subscriptions.plan_key',
                'subscriptions.plan_template_id',
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
     * @return array<int, array<string, int|float|string>>
     */
    private function buildMonthlyRevenueRows(Carbon $today, bool $hasChargeEventsTable): array
    {
        if (! $hasChargeEventsTable) {
            return [];
        }

        $windowStart = $today->copy()->startOfMonth()->subMonths(11);
        $windowEnd = $today->copy()->endOfMonth();

        $events = SubscriptionChargeEvent::query()
            ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
            ->whereHas('subscription', function ($query): void {
                $query->where(function ($subQuery): void {
                    $subQuery
                        ->where('is_branch_managed', false)
                        ->orWhereNull('is_branch_managed');
                });
            })
            ->whereBetween('charged_at', [$windowStart->copy()->startOfDay(), $windowEnd->copy()->endOfDay()])
            ->get(['charged_at', 'total_paid', 'discount_amount']);

        $newGyms = Gym::query()
            ->withoutDemoSessions()
            ->whereBetween('created_at', [$windowStart->copy()->startOfDay(), $windowEnd->copy()->endOfDay()])
            ->get(['created_at']);

        $rows = [];

        for ($index = 0; $index < 12; $index++) {
            $month = $windowStart->copy()->addMonths($index);
            $key = $month->format('Y-m');
            $monthEvents = $events->filter(fn (SubscriptionChargeEvent $event): bool => $event->charged_at?->format('Y-m') === $key);
            $monthNewGyms = $newGyms->filter(fn (Gym $gym): bool => $gym->created_at?->format('Y-m') === $key)->count();

            $rows[] = [
                'month_key' => $key,
                'month_label' => $month->format('m/Y'),
                'revenue' => round($monthEvents->sum(fn (SubscriptionChargeEvent $event): float => (float) ($event->total_paid ?? 0)), 2),
                'discount' => round($monthEvents->sum(fn (SubscriptionChargeEvent $event): float => (float) ($event->discount_amount ?? 0)), 2),
                'charges' => $monthEvents->count(),
                'new_gyms' => $monthNewGyms,
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildOwnerActivityRows(int $limit = 18): array
    {
        if (! Schema::hasTable('gym_admin_activity_states')) {
            return [];
        }

        $activityService = app(GymAdminActivityService::class);

        return GymAdminActivityState::query()
            ->whereHas('gym', fn ($query) => $query->withoutDemoSessions())
            ->with([
                'gym:id,name,slug',
                'user:id,name,email',
            ])
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id')
            ->limit(max(1, $limit))
            ->get()
            ->map(function (GymAdminActivityState $state) use ($activityService): array {
                return [
                    'gym_name' => trim((string) ($state->gym_name ?: $state->gym?->name ?: 'Gym sin nombre')),
                    'gym_slug' => trim((string) ($state->gym?->slug ?? '')),
                    'user_name' => trim((string) ($state->user_name ?: $state->user?->name ?: 'Admin principal')),
                    'user_email' => trim((string) ($state->user_email ?: $state->user?->email ?: '-')),
                    'ip_address' => trim((string) ($state->last_ip_address ?? '')) ?: '-',
                    'last_login_at' => $state->last_login_at,
                    'last_activity_at' => $state->last_activity_at,
                    'channel_label' => $activityService->channelLabel($state->last_channel),
                    'status_key' => $activityService->isOnline($state->last_activity_at) ? 'online' : 'offline',
                    'status_label' => $activityService->isOnline($state->last_activity_at) ? 'Activo ahora' : 'Inactivo',
                    'signal' => trim((string) ($state->last_activity_signal ?? '')) ?: 'activity',
                    'via_remember' => (bool) ($state->last_via_remember ?? false),
                ];
            })
            ->values()
            ->all();
    }

    private function resolveCurrentCycleTotal(Subscription $subscription): float
    {
        $latestChargeEvent = $subscription->relationLoaded('latestChargeEvent')
            ? $subscription->getRelation('latestChargeEvent')
            : null;
        if ($latestChargeEvent !== null && (float) ($latestChargeEvent->total_paid ?? 0) > 0) {
            return round((float) $latestChargeEvent->total_paid, 2);
        }

        $billingCycles = $this->estimateBillingCyclesFromSubscription($subscription);

        return round(max(0, (float) ($subscription->price ?? 0)) * $billingCycles, 2);
    }

    private function resolveCurrentCycleDiscount(Subscription $subscription): float
    {
        $latestChargeEvent = $subscription->relationLoaded('latestChargeEvent')
            ? $subscription->getRelation('latestChargeEvent')
            : null;
        if ($latestChargeEvent !== null) {
            return round(max(0, (float) ($latestChargeEvent->discount_amount ?? 0)), 2);
        }

        $baseMonthlyPrice = (float) ($subscription->sucursales_base_price ?? $subscription->price ?? 0);
        $billingCycles = $this->estimateBillingCyclesFromSubscription($subscription);
        $baseTotal = round(max(0, $baseMonthlyPrice) * $billingCycles, 2);
        $effectiveTotal = round(max(0, (float) ($subscription->price ?? 0)) * $billingCycles, 2);

        return round(max(0, $baseTotal - $effectiveTotal), 2);
    }

    private function resolveRecurringMonthlyValue(Subscription $subscription): float
    {
        $latestChargeEvent = $subscription->relationLoaded('latestChargeEvent')
            ? $subscription->getRelation('latestChargeEvent')
            : null;
        if ($latestChargeEvent !== null && (float) ($latestChargeEvent->base_monthly_price ?? 0) > 0) {
            return round((float) $latestChargeEvent->base_monthly_price, 2);
        }

        if ($subscription->sucursales_base_price !== null && (float) $subscription->sucursales_base_price > 0) {
            return round((float) $subscription->sucursales_base_price, 2);
        }

        return round((float) ($subscription->price ?? 0), 2);
    }

    private function estimateBillingCyclesFromSubscription(Subscription $subscription): int
    {
        try {
            $startsAt = Carbon::parse($subscription->starts_at)->startOfDay();
            $endsAt = Carbon::parse($subscription->ends_at)->startOfDay();
            $coverageDays = max(1, $startsAt->diffInDays($endsAt) + 1);
        } catch (\Throwable) {
            return 1;
        }

        $baseDurationDays = 30;
        if ($coverageDays <= $baseDurationDays) {
            return 1;
        }

        $ratio = $coverageDays / $baseDurationDays;
        $rounded = max(1, (int) round($ratio));

        if (abs($ratio - $rounded) <= 0.35) {
            return $rounded;
        }

        return max(1, (int) floor($ratio));
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

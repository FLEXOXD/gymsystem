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

        return [
            'total_gyms' => Gym::query()->count(),
            'active_gyms' => Subscription::query()->where('status', 'active')->count(),
            'grace_gyms' => Subscription::query()->where('status', 'grace')->count(),
            'suspended_gyms' => Subscription::query()->where('status', 'suspended')->count(),
            'mrr_estimated' => (float) Subscription::query()
                ->whereIn('status', ['active', 'grace'])
                ->sum('price'),
            'vencen_en_7_dias' => Subscription::query()
                ->where('status', '<>', 'suspended')
                ->whereDate('ends_at', '<=', $limitDate)
                ->count(),
            'en_gracia_hoy' => Subscription::query()->where('status', 'grace')->count(),
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
            ->join('subscriptions', 'subscriptions.gym_id', '=', 'gyms.id')
            ->select([
                'gyms.id as gym_id',
                'gyms.name as gym_name',
                'subscriptions.plan_name',
                'subscriptions.price',
                'subscriptions.ends_at',
                'subscriptions.status',
                'subscriptions.last_payment_method',
                'subscriptions.grace_days',
            ])
            ->orderByRaw("CASE subscriptions.status WHEN 'grace' THEN 0 WHEN 'active' THEN 1 WHEN 'suspended' THEN 2 ELSE 3 END")
            ->orderBy('subscriptions.ends_at')
            ->get()
            ->map(function (object $row) use ($today): object {
                $endsAt = Carbon::parse($row->ends_at)->startOfDay();
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

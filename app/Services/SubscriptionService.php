<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SubscriptionService
{
    public const PAYMENT_METHODS = [
        'transferencia',
        'deposito',
        'efectivo',
        'payphone',
        'western_union',
        'paypal',
    ];

    /**
     * Get current subscription for a gym.
     */
    public function getSubscription(int $gymId): ?Subscription
    {
        return Subscription::query()
            ->where('gym_id', $gymId)
            ->first();
    }

    /**
     * Check and synchronize status based on end date + grace days.
     */
    public function checkStatus(int $gymId): ?Subscription
    {
        $subscription = $this->getSubscription($gymId);
        if (! $subscription) {
            return null;
        }

        if ($subscription->status === 'suspended') {
            return $subscription;
        }

        $today = Carbon::today();
        $endsAt = Carbon::parse($subscription->ends_at)->startOfDay();
        $graceDays = max(0, (int) ($subscription->grace_days ?? 3));
        $graceLimit = $endsAt->copy()->addDays($graceDays);

        if ($today->lte($endsAt)) {
            $status = 'active';
        } elseif ($today->lte($graceLimit)) {
            $status = 'grace';
        } else {
            $status = 'suspended';
        }

        if ($subscription->status !== $status) {
            $subscription->status = $status;
            $subscription->save();
        }

        return $subscription->fresh();
    }

    /**
     * Check if access is allowed (active or grace).
     */
    public function isActive(int $gymId): bool
    {
        $subscription = $this->checkStatus($gymId);
        if (! $subscription) {
            return false;
        }

        return in_array($subscription->status, ['active', 'grace'], true);
    }

    /**
     * Renew subscription by N months.
     */
    public function renew(int $gymId, int $months = 1, string $paymentMethod = 'efectivo', ?array $planTemplate = null): Subscription
    {
        $months = max(1, $months);

        if (! in_array($paymentMethod, self::PAYMENT_METHODS, true)) {
            throw new InvalidArgumentException('Método de pago invalido.');
        }

        return DB::transaction(function () use ($gymId, $months, $paymentMethod, $planTemplate): Subscription {
            $subscription = Subscription::query()
                ->where('gym_id', $gymId)
                ->lockForUpdate()
                ->first();

            $startsAt = Carbon::today();
            $defaultEndsAt = $startsAt->copy()->addMonthsNoOverflow($months)->subDay();
            $planName = (string) ($subscription?->plan_name ?? 'Plan Mensual');
            $price = (float) ($subscription?->price ?? 29.99);
            $endsAt = $defaultEndsAt;

            if (is_array($planTemplate)) {
                $templateName = trim((string) ($planTemplate['name'] ?? ''));
                if ($templateName !== '') {
                    $planName = $templateName;
                }

                $price = isset($planTemplate['price']) ? (float) $planTemplate['price'] : $price;
                $durationUnit = strtolower(trim((string) ($planTemplate['duration_unit'] ?? 'days')));
                $durationMonths = max(1, (int) ($planTemplate['duration_months'] ?? 1));
                $durationDays = max(1, (int) ($planTemplate['duration_days'] ?? 30));

                $endsAt = $durationUnit === 'months'
                    ? $startsAt->copy()->addMonthsNoOverflow($durationMonths)->subDay()
                    : $startsAt->copy()->addDays($durationDays)->subDay();
            }

            $payload = [
                'plan_name' => $planName,
                'price' => $price,
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt->toDateString(),
                'status' => 'active',
                'last_payment_method' => $paymentMethod,
                'grace_days' => $subscription?->grace_days ?? 3,
            ];

            if ($subscription) {
                $subscription->update($payload);

                return $subscription->fresh();
            }

            return Subscription::query()->create([
                'gym_id' => $gymId,
                ...$payload,
            ]);
        });
    }

    /**
     * Suspend current active/trial subscription.
     */
    public function suspend(int $gymId): void
    {
        Subscription::query()
            ->where('gym_id', $gymId)
            ->update([
                'status' => 'suspended',
            ]);
    }

    /**
     * Ensure a base subscription exists for a gym.
     */
    public function ensureSubscription(int $gymId): Subscription
    {
        $startsAt = Carbon::today();
        $endsAt = $startsAt->copy()->addMonthNoOverflow()->subDay();
        $defaultPlan = Plan::query()
            ->where('gym_id', $gymId)
            ->where('status', 'active')
            ->orderBy('id')
            ->first(['name', 'price']);

        return Subscription::query()->firstOrCreate([
            'gym_id' => $gymId,
        ], [
            'gym_id' => $gymId,
            'plan_name' => $defaultPlan?->name ?? 'Plan Mensual',
            'price' => $defaultPlan?->price ?? 29.99,
            'starts_at' => $startsAt->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'status' => 'active',
            'last_payment_method' => null,
            'grace_days' => 3,
        ]);
    }
}

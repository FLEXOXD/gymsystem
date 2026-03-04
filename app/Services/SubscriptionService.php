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

    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

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
        return $this->checkStatusInternal($gymId, []);
    }

    /**
     * Keep a managed branch subscription aligned with its billing owner.
     */
    public function syncManagedSubscriptionWithOwner(int $gymId): ?Subscription
    {
        $subscription = $this->getSubscription($gymId);
        if (! $subscription) {
            return null;
        }

        if (! (bool) ($subscription->is_branch_managed ?? false)) {
            return $subscription;
        }

        $billingOwnerGymId = (int) ($subscription->billing_owner_gym_id ?? 0);
        if ($billingOwnerGymId <= 0 || $billingOwnerGymId === $gymId) {
            return $subscription;
        }

        $ownerSubscription = $this->checkStatusInternal($billingOwnerGymId, [$gymId]);
        if (! $ownerSubscription) {
            return $subscription;
        }

        $payload = [
            'starts_at' => $ownerSubscription->starts_at?->toDateString() ?? $subscription->starts_at?->toDateString(),
            'ends_at' => $ownerSubscription->ends_at?->toDateString() ?? $subscription->ends_at?->toDateString(),
            'status' => (string) ($ownerSubscription->status ?? 'active'),
            'grace_days' => (int) ($ownerSubscription->grace_days ?? 3),
        ];

        $currentStartsAt = $subscription->starts_at?->toDateString();
        $currentEndsAt = $subscription->ends_at?->toDateString();
        $currentStatus = (string) ($subscription->status ?? '');
        $currentGraceDays = (int) ($subscription->grace_days ?? 3);

        if (
            $currentStartsAt !== $payload['starts_at']
            || $currentEndsAt !== $payload['ends_at']
            || $currentStatus !== $payload['status']
            || $currentGraceDays !== $payload['grace_days']
        ) {
            $subscription->update($payload);

            return $subscription->fresh();
        }

        return $subscription;
    }

    /**
     * Sync all managed branch subscriptions that depend on the hub subscription.
     */
    public function syncManagedBranchesFromHub(int $hubGymId): void
    {
        $hubSubscription = $this->getSubscription($hubGymId);
        if (! $hubSubscription) {
            return;
        }

        Subscription::query()
            ->where('is_branch_managed', true)
            ->where('billing_owner_gym_id', $hubGymId)
            ->where('gym_id', '<>', $hubGymId)
            ->update([
                'starts_at' => $hubSubscription->starts_at?->toDateString(),
                'ends_at' => $hubSubscription->ends_at?->toDateString(),
                'status' => (string) ($hubSubscription->status ?? 'active'),
                'grace_days' => (int) ($hubSubscription->grace_days ?? 3),
            ]);
    }

    /**
     * Internal status checker to prevent recursive owner loops.
     *
     * @param  array<int, int>  $visitedGymIds
     */
    private function checkStatusInternal(int $gymId, array $visitedGymIds): ?Subscription
    {
        if (in_array($gymId, $visitedGymIds, true)) {
            return $this->getSubscription($gymId);
        }

        $visitedGymIds[] = $gymId;
        $subscription = $this->getSubscription($gymId);
        if (! $subscription) {
            return null;
        }

        if ((bool) ($subscription->is_branch_managed ?? false)) {
            $billingOwnerGymId = (int) ($subscription->billing_owner_gym_id ?? 0);
            if ($billingOwnerGymId > 0 && $billingOwnerGymId !== $gymId) {
                $ownerSubscription = $this->checkStatusInternal($billingOwnerGymId, $visitedGymIds);
                if ($ownerSubscription) {
                    $this->syncManagedSubscriptionWithOwner($gymId);

                    return $this->getSubscription($gymId);
                }
            }
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
            $planKey = $this->planAccessService->inferPlanKey(
                rawPlanKey: (string) ($subscription?->plan_key ?? ''),
                planName: $planName,
                price: $price
            );
            $planTemplateId = $subscription?->plan_template_id !== null ? (int) $subscription->plan_template_id : null;
            $featureVersion = $this->resolveFeatureVersion(
                planTemplate: $planTemplate ?? [],
                fallback: (string) ($subscription?->feature_version ?? '')
            );
            $sucursalesIntroPending = (bool) ($subscription?->sucursales_intro_pending ?? false);
            $sucursalesBasePrice = $subscription?->sucursales_base_price !== null
                ? (float) $subscription->sucursales_base_price
                : null;
            $sucursalesIntroDiscountPercent = $subscription?->sucursales_intro_discount_percent !== null
                ? (int) $subscription->sucursales_intro_discount_percent
                : null;

            if (is_array($planTemplate)) {
                ['plan_name' => $planName, 'price' => $price, 'ends_at' => $endsAt] = $this->resolvePlanTemplateSnapshot(
                    startsAt: $startsAt,
                    fallbackPlanName: $planName,
                    fallbackPrice: $price,
                    planTemplate: $planTemplate
                );
                $planKey = $this->planAccessService->inferPlanKey(
                    rawPlanKey: (string) ($planTemplate['plan_key'] ?? ''),
                    planName: $planName,
                    price: $price
                );
                $planTemplateId = $this->resolvePlanTemplateId($planTemplate, $planTemplateId);
                $featureVersion = $this->resolveFeatureVersion(
                    planTemplate: $planTemplate,
                    fallback: $featureVersion
                );
            }

            [
                'price' => $price,
                'sucursales_intro_pending' => $sucursalesIntroPending,
                'sucursales_base_price' => $sucursalesBasePrice,
                'sucursales_intro_discount_percent' => $sucursalesIntroDiscountPercent,
            ] = $this->resolveSucursalesIntroPricing(
                planKey: $planKey,
                price: $price,
                planTemplate: $planTemplate,
                currentPending: $sucursalesIntroPending,
                currentBasePrice: $sucursalesBasePrice,
                currentDiscountPercent: $sucursalesIntroDiscountPercent
            );

            $payload = [
                'plan_key' => $planKey,
                'plan_template_id' => $planTemplateId,
                'feature_version' => $featureVersion,
                'plan_name' => $planName,
                'price' => $price,
                'sucursales_intro_pending' => $sucursalesIntroPending,
                'sucursales_base_price' => $sucursalesBasePrice,
                'sucursales_intro_discount_percent' => $sucursalesIntroDiscountPercent,
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt->toDateString(),
                'status' => 'active',
                'last_payment_method' => $paymentMethod,
                'grace_days' => $subscription?->grace_days ?? 3,
            ];

            if ($subscription) {
                $subscription->update($payload);
                $this->invalidatePlanAccessCache((int) $subscription->gym_id);

                $updated = $subscription->fresh();
                if (! (bool) ($updated->is_branch_managed ?? false)) {
                    $this->syncManagedBranchesFromHub((int) $updated->gym_id);
                }

                return $updated;
            }

            $created = Subscription::query()->create([
                'gym_id' => $gymId,
                ...$payload,
            ]);
            $this->invalidatePlanAccessCache((int) $created->gym_id);

            if (! (bool) ($created->is_branch_managed ?? false)) {
                $this->syncManagedBranchesFromHub((int) $created->gym_id);
            }

            return $created;
        });
    }

    /**
     * Apply a plan template snapshot to a gym subscription without requiring payment input.
     *
     * @param  array<string, mixed>  $planTemplate
     */
    public function applyPlanTemplate(int $gymId, array $planTemplate, ?string $paymentMethod = null): Subscription
    {
        return DB::transaction(function () use ($gymId, $planTemplate, $paymentMethod): Subscription {
            $subscription = Subscription::query()
                ->where('gym_id', $gymId)
                ->lockForUpdate()
                ->first();

            $startsAt = Carbon::today();
            $fallbackPlanName = (string) ($subscription?->plan_name ?? 'Plan Mensual');
            $fallbackPrice = (float) ($subscription?->price ?? 29.99);
            ['plan_name' => $planName, 'price' => $price, 'ends_at' => $endsAt] = $this->resolvePlanTemplateSnapshot(
                startsAt: $startsAt,
                fallbackPlanName: $fallbackPlanName,
                fallbackPrice: $fallbackPrice,
                planTemplate: $planTemplate
            );
            $planKey = $this->planAccessService->inferPlanKey(
                rawPlanKey: (string) ($planTemplate['plan_key'] ?? ($subscription?->plan_key ?? '')),
                planName: $planName,
                price: $price
            );
            $planTemplateId = $this->resolvePlanTemplateId(
                planTemplate: $planTemplate,
                fallback: $subscription?->plan_template_id !== null ? (int) $subscription->plan_template_id : null
            );
            $featureVersion = $this->resolveFeatureVersion(
                planTemplate: $planTemplate,
                fallback: (string) ($subscription?->feature_version ?? '')
            );
            $sucursalesIntroPending = (bool) ($subscription?->sucursales_intro_pending ?? false);
            $sucursalesBasePrice = $subscription?->sucursales_base_price !== null
                ? (float) $subscription->sucursales_base_price
                : null;
            $sucursalesIntroDiscountPercent = $subscription?->sucursales_intro_discount_percent !== null
                ? (int) $subscription->sucursales_intro_discount_percent
                : null;

            [
                'price' => $price,
                'sucursales_intro_pending' => $sucursalesIntroPending,
                'sucursales_base_price' => $sucursalesBasePrice,
                'sucursales_intro_discount_percent' => $sucursalesIntroDiscountPercent,
            ] = $this->resolveSucursalesIntroPricing(
                planKey: $planKey,
                price: $price,
                planTemplate: $planTemplate,
                currentPending: $sucursalesIntroPending,
                currentBasePrice: $sucursalesBasePrice,
                currentDiscountPercent: $sucursalesIntroDiscountPercent
            );

            $payload = [
                'plan_key' => $planKey,
                'plan_template_id' => $planTemplateId,
                'feature_version' => $featureVersion,
                'plan_name' => $planName,
                'price' => $price,
                'sucursales_intro_pending' => $sucursalesIntroPending,
                'sucursales_base_price' => $sucursalesBasePrice,
                'sucursales_intro_discount_percent' => $sucursalesIntroDiscountPercent,
                'starts_at' => $startsAt->toDateString(),
                'ends_at' => $endsAt->toDateString(),
                'status' => 'active',
                'last_payment_method' => $paymentMethod,
                'grace_days' => $subscription?->grace_days ?? 3,
            ];

            if ($subscription) {
                $subscription->update($payload);
                $this->invalidatePlanAccessCache((int) $subscription->gym_id);

                $updated = $subscription->fresh();
                if (! (bool) ($updated->is_branch_managed ?? false)) {
                    $this->syncManagedBranchesFromHub((int) $updated->gym_id);
                }

                return $updated;
            }

            $created = Subscription::query()->create([
                'gym_id' => $gymId,
                ...$payload,
            ]);
            $this->invalidatePlanAccessCache((int) $created->gym_id);

            if (! (bool) ($created->is_branch_managed ?? false)) {
                $this->syncManagedBranchesFromHub((int) $created->gym_id);
            }

            return $created;
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

        $subscription = $this->getSubscription($gymId);
        if ($subscription && ! (bool) ($subscription->is_branch_managed ?? false)) {
            $this->syncManagedBranchesFromHub($gymId);
        }
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
        $defaultPlanName = (string) ($defaultPlan?->name ?? 'Plan Mensual');
        $defaultPlanPrice = (float) ($defaultPlan?->price ?? 29.99);
        $defaultPlanKey = $this->planAccessService->inferPlanKey(
            rawPlanKey: '',
            planName: $defaultPlanName,
            price: $defaultPlanPrice
        );
        $defaultFeatureVersion = $this->defaultFeatureVersion();

        $subscription = Subscription::query()->firstOrCreate([
            'gym_id' => $gymId,
        ], [
            'gym_id' => $gymId,
            'plan_key' => $defaultPlanKey,
            'plan_template_id' => null,
            'feature_version' => $defaultFeatureVersion,
            'plan_name' => $defaultPlanName,
            'price' => $defaultPlanPrice,
            'starts_at' => $startsAt->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'status' => 'active',
            'last_payment_method' => null,
            'grace_days' => 3,
        ]);
        if ((bool) ($subscription->wasRecentlyCreated ?? false)) {
            $this->invalidatePlanAccessCache((int) $subscription->gym_id);
        }

        $resolvedPlanKey = $this->planAccessService->inferPlanKey(
            rawPlanKey: (string) ($subscription->plan_key ?? ''),
            planName: (string) ($subscription->plan_name ?? $defaultPlanName),
            price: isset($subscription->price) ? (float) $subscription->price : $defaultPlanPrice
        );
        $resolvedFeatureVersion = trim((string) ($subscription->feature_version ?? ''));
        if ($resolvedFeatureVersion === '') {
            $resolvedFeatureVersion = $defaultFeatureVersion;
        }

        $payload = [];
        if (trim((string) ($subscription->plan_key ?? '')) !== $resolvedPlanKey) {
            $payload['plan_key'] = $resolvedPlanKey;
        }
        if ($resolvedFeatureVersion !== (string) ($subscription->feature_version ?? '')) {
            $payload['feature_version'] = $resolvedFeatureVersion;
        }

        if ($payload !== []) {
            $subscription->update($payload);
            $this->invalidatePlanAccessCache((int) $subscription->gym_id);

            $updated = $subscription->fresh();
            if ((bool) ($updated->is_branch_managed ?? false)) {
                return $this->syncManagedSubscriptionWithOwner($gymId) ?? $updated;
            }

            return $updated;
        }

        if ((bool) ($subscription->is_branch_managed ?? false)) {
            return $this->syncManagedSubscriptionWithOwner($gymId) ?? $subscription;
        }

        return $subscription;
    }

    /**
     * @param  array<string, mixed>  $planTemplate
     * @return array{plan_name:string,price:float,ends_at:Carbon}
     */
    private function resolvePlanTemplateSnapshot(Carbon $startsAt, string $fallbackPlanName, float $fallbackPrice, array $planTemplate): array
    {
        $planName = $fallbackPlanName;
        $price = $fallbackPrice;

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

        return [
            'plan_name' => $planName,
            'price' => $price,
            'ends_at' => $endsAt,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $planTemplate
     * @return array{price:float,sucursales_intro_pending:bool,sucursales_base_price:?float,sucursales_intro_discount_percent:?int}
     */
    private function resolveSucursalesIntroPricing(
        string $planKey,
        float $price,
        ?array $planTemplate,
        bool $currentPending,
        ?float $currentBasePrice,
        ?int $currentDiscountPercent
    ): array {
        $resolvedPlanKey = strtolower(trim($planKey));
        $isTemplateDrivenUpdate = is_array($planTemplate);
        $resolvedPrice = max(0, $price);
        $pending = $currentPending;
        $basePrice = $currentBasePrice;
        $discountPercent = $currentDiscountPercent;

        if ($resolvedPlanKey !== 'sucursales') {
            return [
                'price' => $resolvedPrice,
                'sucursales_intro_pending' => false,
                'sucursales_base_price' => null,
                'sucursales_intro_discount_percent' => null,
            ];
        }

        if (! $isTemplateDrivenUpdate) {
            if ($pending) {
                $resolvedPrice = $basePrice !== null ? max(0, $basePrice) : $resolvedPrice;
                $pending = false;
                $basePrice = null;
                $discountPercent = null;
            }

            return [
                'price' => $resolvedPrice,
                'sucursales_intro_pending' => $pending,
                'sucursales_base_price' => $basePrice,
                'sucursales_intro_discount_percent' => $discountPercent,
            ];
        }

        $applyIntroFirstCycle = (bool) ($planTemplate['intro_discount_first_cycle'] ?? false);
        if (! $applyIntroFirstCycle) {
            return [
                'price' => $resolvedPrice,
                'sucursales_intro_pending' => false,
                'sucursales_base_price' => null,
                'sucursales_intro_discount_percent' => null,
            ];
        }

        $introPercent = $this->resolveIntroDiscountPercent($planTemplate['intro_discount_percent'] ?? null);
        $base = max(0, $resolvedPrice);
        $discounted = round($base * (1 - ($introPercent / 100)), 2);

        return [
            'price' => max(0, $discounted),
            'sucursales_intro_pending' => true,
            'sucursales_base_price' => $base,
            'sucursales_intro_discount_percent' => $introPercent,
        ];
    }

    private function resolveIntroDiscountPercent(mixed $value): int
    {
        $percent = is_numeric($value) ? (int) round((float) $value) : 50;

        return max(1, min(100, $percent));
    }

    /**
     * @param  array<string, mixed>  $planTemplate
     */
    private function resolvePlanTemplateId(array $planTemplate, ?int $fallback = null): ?int
    {
        foreach (['template_id', 'plan_template_id', 'id'] as $key) {
            if (! array_key_exists($key, $planTemplate)) {
                continue;
            }

            if (! is_numeric($planTemplate[$key])) {
                continue;
            }

            $value = (int) $planTemplate[$key];
            if ($value > 0) {
                return $value;
            }
        }

        return $fallback;
    }

    /**
     * @param  array<string, mixed>  $planTemplate
     */
    private function resolveFeatureVersion(array $planTemplate, ?string $fallback = null): string
    {
        if (array_key_exists('feature_version', $planTemplate)) {
            $candidate = trim((string) $planTemplate['feature_version']);
            if ($candidate !== '') {
                return $candidate;
            }
        }

        $fallbackVersion = trim((string) ($fallback ?? ''));

        return $fallbackVersion !== '' ? $fallbackVersion : $this->defaultFeatureVersion();
    }

    private function defaultFeatureVersion(): string
    {
        $configured = trim((string) config('plan_features.default_feature_version', 'v1'));

        return $configured !== '' ? $configured : 'v1';
    }

    private function invalidatePlanAccessCache(int $gymId): void
    {
        if ($gymId <= 0) {
            return;
        }

        $this->planAccessService->forgetGym($gymId);
    }
}

<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionChargeEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
                $this->recordRenewalChargeEvent(
                    subscription: $updated,
                    months: $months,
                    paymentMethod: $paymentMethod,
                    planTemplate: $planTemplate
                );
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
            $this->recordRenewalChargeEvent(
                subscription: $created,
                months: $months,
                paymentMethod: $paymentMethod,
                planTemplate: $planTemplate
            );

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
                $this->recordAppliedPlanChargeEvent(
                    subscription: $updated,
                    planTemplate: $planTemplate,
                    paymentMethod: $paymentMethod
                );
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
            $this->recordAppliedPlanChargeEvent(
                subscription: $created,
                planTemplate: $planTemplate,
                paymentMethod: $paymentMethod
            );

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
     * @param  array<string, mixed>|null  $planTemplate
     */
    private function recordRenewalChargeEvent(
        Subscription $subscription,
        int $months,
        string $paymentMethod,
        ?array $planTemplate = null
    ): void {
        $payload = $this->buildChargeEventPayload(
            subscription: $subscription,
            planTemplate: $planTemplate,
            defaultEventType: 'renewal',
            paymentMethod: $paymentMethod,
            fallbackBillingCycles: $months,
            allowFallbackWithoutBillingEvent: true
        );

        if ($payload === null) {
            return;
        }

        $this->persistChargeEvent($subscription, $payload);
    }

    /**
     * @param  array<string, mixed>  $planTemplate
     */
    private function recordAppliedPlanChargeEvent(
        Subscription $subscription,
        array $planTemplate,
        ?string $paymentMethod = null
    ): void {
        $payload = $this->buildChargeEventPayload(
            subscription: $subscription,
            planTemplate: $planTemplate,
            defaultEventType: 'signup',
            paymentMethod: $paymentMethod,
            fallbackBillingCycles: (int) ($planTemplate['billing_cycles'] ?? 1),
            allowFallbackWithoutBillingEvent: false
        );

        if ($payload === null) {
            return;
        }

        $this->persistChargeEvent($subscription, $payload);
    }

    /**
     * @param  array<string, mixed>|null  $planTemplate
     * @return array<string, float|int|string|null|Carbon>|null
     */
    private function buildChargeEventPayload(
        Subscription $subscription,
        ?array $planTemplate,
        string $defaultEventType,
        ?string $paymentMethod,
        int $fallbackBillingCycles,
        bool $allowFallbackWithoutBillingEvent
    ): ?array {
        if ((bool) ($subscription->is_branch_managed ?? false)) {
            return null;
        }

        $resolvedPlanTemplate = is_array($planTemplate) ? $planTemplate : [];
        $billingEvent = is_array($resolvedPlanTemplate['billing_event'] ?? null)
            ? $resolvedPlanTemplate['billing_event']
            : [];

        if ($billingEvent === [] && ! $allowFallbackWithoutBillingEvent) {
            return null;
        }

        $billingCycles = max(1, (int) ($billingEvent['billing_cycles']
            ?? $resolvedPlanTemplate['billing_cycles']
            ?? $fallbackBillingCycles));

        $effectiveMonthlyPrice = round((float) ($billingEvent['effective_monthly_price']
            ?? $resolvedPlanTemplate['price']
            ?? $subscription->price
            ?? 0), 2);

        $baseMonthlyPrice = round((float) ($billingEvent['base_monthly_price']
            ?? $this->resolveBaseMonthlyChargePrice($subscription, $resolvedPlanTemplate, $effectiveMonthlyPrice)), 2);

        $baseTotal = round((float) ($billingEvent['base_total'] ?? ($baseMonthlyPrice * $billingCycles)), 2);
        $totalPaid = round((float) ($billingEvent['final_total']
            ?? $billingEvent['total_paid']
            ?? ($effectiveMonthlyPrice * $billingCycles)), 2);
        $discountAmount = round(max(0, (float) ($billingEvent['discount_amount'] ?? ($baseTotal - $totalPaid))), 2);
        $bonusDays = max(0, (int) ($billingEvent['bonus_days'] ?? $resolvedPlanTemplate['bonus_days'] ?? 0));
        $chargedAt = $this->resolveChargeEventDate($billingEvent['charged_at'] ?? null);

        return [
            'plan_template_id' => $this->resolveChargeEventPlanTemplateId($subscription, $resolvedPlanTemplate, $billingEvent),
            'promotion_template_id' => $this->resolveChargeEventPromotionId($billingEvent),
            'plan_key' => $this->resolveChargeEventPlanKey($subscription, $resolvedPlanTemplate, $billingEvent),
            'plan_name' => $this->resolveChargeEventPlanName($subscription, $resolvedPlanTemplate, $billingEvent),
            'event_type' => trim((string) ($billingEvent['event_type'] ?? $defaultEventType)) ?: $defaultEventType,
            'payment_method' => $this->resolveChargeEventPaymentMethod($subscription, $paymentMethod, $billingEvent),
            'billing_cycles' => $billingCycles,
            'base_monthly_price' => max(0, $baseMonthlyPrice),
            'effective_monthly_price' => max(0, $effectiveMonthlyPrice),
            'base_total' => max(0, $baseTotal),
            'discount_amount' => $discountAmount,
            'total_paid' => max(0, $totalPaid),
            'bonus_days' => $bonusDays,
            'charged_at' => $chargedAt,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $planTemplate
     * @param  array<string, mixed>  $billingEvent
     */
    private function resolveChargeEventPlanTemplateId(
        Subscription $subscription,
        ?array $planTemplate,
        array $billingEvent
    ): ?int {
        $resolvedPlanTemplate = is_array($planTemplate) ? $planTemplate : [];

        foreach ([
            $billingEvent['plan_template_id'] ?? null,
            $billingEvent['template_id'] ?? null,
            $resolvedPlanTemplate['plan_template_id'] ?? null,
            $resolvedPlanTemplate['template_id'] ?? null,
            $resolvedPlanTemplate['id'] ?? null,
            $subscription->plan_template_id,
        ] as $candidate) {
            if (! is_numeric($candidate)) {
                continue;
            }

            $value = (int) $candidate;
            if ($value > 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $planTemplate
     * @param  array<string, mixed>  $billingEvent
     */
    private function resolveChargeEventPlanKey(
        Subscription $subscription,
        ?array $planTemplate,
        array $billingEvent
    ): ?string {
        $resolvedPlanTemplate = is_array($planTemplate) ? $planTemplate : [];

        foreach ([
            $billingEvent['plan_key'] ?? null,
            $resolvedPlanTemplate['plan_key'] ?? null,
            $subscription->plan_key,
        ] as $candidate) {
            $resolved = trim((string) ($candidate ?? ''));
            if ($resolved !== '') {
                return $resolved;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $planTemplate
     * @param  array<string, mixed>  $billingEvent
     */
    private function resolveChargeEventPlanName(
        Subscription $subscription,
        ?array $planTemplate,
        array $billingEvent
    ): string {
        $resolvedPlanTemplate = is_array($planTemplate) ? $planTemplate : [];

        foreach ([
            $billingEvent['plan_name'] ?? null,
            $billingEvent['name'] ?? null,
            $resolvedPlanTemplate['name'] ?? null,
            $subscription->plan_name,
        ] as $candidate) {
            $resolved = trim((string) ($candidate ?? ''));
            if ($resolved !== '') {
                return $resolved;
            }
        }

        return 'Plan comercial';
    }

    /**
     * @param  array<string, mixed>  $billingEvent
     */
    private function resolveChargeEventPromotionId(array $billingEvent): ?int
    {
        foreach ([
            $billingEvent['promotion_template_id'] ?? null,
            $billingEvent['promotion_id'] ?? null,
        ] as $candidate) {
            if (! is_numeric($candidate)) {
                continue;
            }

            $value = (int) $candidate;
            if ($value > 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $billingEvent
     */
    private function resolveChargeEventPaymentMethod(
        Subscription $subscription,
        ?string $paymentMethod,
        array $billingEvent
    ): ?string {
        foreach ([
            $billingEvent['payment_method'] ?? null,
            $paymentMethod,
            $subscription->last_payment_method,
        ] as $candidate) {
            $resolved = trim((string) ($candidate ?? ''));
            if ($resolved !== '') {
                return $resolved;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $planTemplate
     */
    private function resolveBaseMonthlyChargePrice(
        Subscription $subscription,
        ?array $planTemplate,
        float $effectiveMonthlyPrice
    ): float {
        $resolvedPlanTemplate = is_array($planTemplate) ? $planTemplate : [];
        $resolvedPlanKey = strtolower(trim((string) ($resolvedPlanTemplate['plan_key'] ?? $subscription->plan_key ?? '')));

        if ($resolvedPlanKey === 'sucursales') {
            $templateBasePrice = $resolvedPlanTemplate['sucursales_base_price'] ?? null;
            if (is_numeric($templateBasePrice) && (float) $templateBasePrice > 0) {
                return round((float) $templateBasePrice, 2);
            }

            if ($subscription->sucursales_base_price !== null && (float) $subscription->sucursales_base_price > 0) {
                return round((float) $subscription->sucursales_base_price, 2);
            }
        }

        return round($effectiveMonthlyPrice, 2);
    }

    private function resolveChargeEventDate(mixed $value): Carbon
    {
        try {
            if ($value instanceof Carbon) {
                return $value->copy();
            }

            if ($value !== null && trim((string) $value) !== '') {
                return Carbon::parse((string) $value);
            }
        } catch (\Throwable) {
            // Fallback handled below.
        }

        return Carbon::now();
    }

    /**
     * @param  array<string, float|int|string|null|Carbon>  $payload
     */
    private function persistChargeEvent(Subscription $subscription, array $payload): void
    {
        if (! $this->supportsChargeEventsTable()) {
            return;
        }

        SubscriptionChargeEvent::query()->create([
            'gym_id' => (int) $subscription->gym_id,
            'subscription_id' => (int) $subscription->id,
            'plan_template_id' => $payload['plan_template_id'],
            'promotion_template_id' => $payload['promotion_template_id'],
            'plan_key' => $payload['plan_key'],
            'plan_name' => (string) $payload['plan_name'],
            'event_type' => (string) $payload['event_type'],
            'payment_method' => $payload['payment_method'],
            'billing_cycles' => (int) $payload['billing_cycles'],
            'base_monthly_price' => (float) $payload['base_monthly_price'],
            'effective_monthly_price' => (float) $payload['effective_monthly_price'],
            'base_total' => (float) $payload['base_total'],
            'discount_amount' => (float) $payload['discount_amount'],
            'total_paid' => (float) $payload['total_paid'],
            'bonus_days' => (int) $payload['bonus_days'],
            'charged_at' => $payload['charged_at'],
        ]);
    }

    private function supportsChargeEventsTable(): bool
    {
        return Schema::hasTable('subscription_charge_events')
            && Schema::hasColumns('subscription_charge_events', [
                'gym_id',
                'subscription_id',
                'plan_template_id',
                'promotion_template_id',
                'plan_key',
                'plan_name',
                'event_type',
                'payment_method',
                'billing_cycles',
                'base_monthly_price',
                'effective_monthly_price',
                'base_total',
                'discount_amount',
                'total_paid',
                'bonus_days',
                'charged_at',
            ]);
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
        $billingCycles = max(1, (int) ($planTemplate['billing_cycles'] ?? 1));
        $durationMonths = max(1, (int) ($planTemplate['duration_months'] ?? 1)) * $billingCycles;
        $durationDays = max(1, (int) ($planTemplate['duration_days'] ?? 30)) * $billingCycles;
        $bonusDays = max(0, (int) ($planTemplate['bonus_days'] ?? 0));

        $endsAt = $durationUnit === 'months'
            ? $startsAt->copy()->addMonthsNoOverflow($durationMonths)->subDay()
            : $startsAt->copy()->addDays($durationDays)->subDay();
        if ($bonusDays > 0) {
            $endsAt = $endsAt->copy()->addDays($bonusDays);
        }

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
        $hasKnownPlanKey = $resolvedPlanKey !== '';
        if (! $hasKnownPlanKey) {
            $pending = false;
            $basePrice = null;
            $discountPercent = null;
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

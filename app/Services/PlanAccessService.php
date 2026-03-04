<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Support\Facades\Cache;

class PlanAccessService
{
    /**
     * @var array<int, string>
     */
    private array $resolvedPlanKeysByGym = [];
    /**
     * @var array<string, array<string, bool>>|null
     */
    private ?array $featureMatrix = null;

    public function currentPlanKey(User $user): string
    {
        if ($user->gym_id === null) {
            return $this->defaultPlanKey();
        }

        return $this->currentPlanKeyForGym((int) $user->gym_id);
    }

    public function currentPlanKeyForGym(int $gymId): string
    {
        if ($gymId <= 0) {
            return $this->defaultPlanKey();
        }

        $this->preloadPlanKeysForGyms([$gymId]);

        return $this->resolvedPlanKeysByGym[$gymId] ?? $this->defaultPlanKey();
    }

    public function can(User $user, string $feature): bool
    {
        if ($user->gym_id === null) {
            return true;
        }

        return $this->canForGym((int) $user->gym_id, $feature);
    }

    public function canForGym(int $gymId, string $feature): bool
    {
        if ($gymId <= 0) {
            return false;
        }

        $featureKey = trim($feature);
        if ($featureKey === '') {
            return false;
        }

        $planKey = $this->currentPlanKeyForGym($gymId);
        $matrix = $this->featureMatrix();
        $defaultPlanFeatures = (array) ($matrix[$this->defaultPlanKey()] ?? []);
        $planFeatures = (array) ($matrix[$planKey] ?? $defaultPlanFeatures);

        return (bool) ($planFeatures[$featureKey] ?? false);
    }

    /**
     * @param  array<int, int|string>  $gymIds
     * @return array<int, bool>
     */
    public function canForGyms(array $gymIds, string $feature): array
    {
        $normalizedGymIds = $this->normalizeGymIds($gymIds);
        $featureKey = trim($feature);
        if ($normalizedGymIds === [] || $featureKey === '') {
            return [];
        }

        $this->preloadPlanKeysForGyms($normalizedGymIds);
        $matrix = $this->featureMatrix();
        $defaultPlanFeatures = (array) ($matrix[$this->defaultPlanKey()] ?? []);
        $result = [];

        foreach ($normalizedGymIds as $gymId) {
            $planKey = $this->resolvedPlanKeysByGym[$gymId] ?? $this->defaultPlanKey();
            $planFeatures = (array) ($matrix[$planKey] ?? $defaultPlanFeatures);
            $result[$gymId] = (bool) ($planFeatures[$featureKey] ?? false);
        }

        return $result;
    }

    /**
     * @param  array<int, int|string>  $gymIds
     */
    public function preloadPlanKeysForGyms(array $gymIds): void
    {
        $normalizedGymIds = $this->normalizeGymIds($gymIds);
        if ($normalizedGymIds === []) {
            return;
        }

        $pendingGymIds = array_values(array_filter(
            $normalizedGymIds,
            fn (int $gymId): bool => ! array_key_exists($gymId, $this->resolvedPlanKeysByGym)
        ));

        if ($pendingGymIds === []) {
            return;
        }

        $cacheSeconds = $this->accessCacheSeconds();
        if ($cacheSeconds > 0) {
            $cacheKeysByGym = [];
            foreach ($pendingGymIds as $gymId) {
                $cacheKeysByGym[$gymId] = $this->cacheKey($gymId);
            }

            $cachedRows = Cache::many(array_values($cacheKeysByGym));
            foreach ($cacheKeysByGym as $gymId => $cacheKey) {
                $cachedPlanKey = $cachedRows[$cacheKey] ?? null;
                if (! is_string($cachedPlanKey) || trim($cachedPlanKey) === '') {
                    continue;
                }

                $this->resolvedPlanKeysByGym[$gymId] = trim($cachedPlanKey);
            }

            $pendingGymIds = array_values(array_filter(
                $pendingGymIds,
                fn (int $gymId): bool => ! array_key_exists($gymId, $this->resolvedPlanKeysByGym)
            ));
            if ($pendingGymIds === []) {
                return;
            }
        }

        $subscriptions = Subscription::query()
            ->whereIn('gym_id', $pendingGymIds)
            ->select(['gym_id', 'plan_key', 'plan_name', 'price'])
            ->get()
            ->keyBy('gym_id');

        foreach ($pendingGymIds as $gymId) {
            $subscription = $subscriptions->get($gymId);
            $resolvedPlanKey = $this->inferPlanKey(
                rawPlanKey: (string) ($subscription?->plan_key ?? ''),
                planName: (string) ($subscription?->plan_name ?? ''),
                price: $subscription !== null ? (float) $subscription->price : null
            );
            $this->resolvedPlanKeysByGym[$gymId] = $resolvedPlanKey;

            if ($cacheSeconds > 0) {
                Cache::put($this->cacheKey($gymId), $resolvedPlanKey, now()->addSeconds($cacheSeconds));
            }
        }
    }

    public function forgetGym(int $gymId): void
    {
        if ($gymId <= 0) {
            return;
        }

        unset($this->resolvedPlanKeysByGym[$gymId]);
        Cache::forget($this->cacheKey($gymId));
    }

    public function inferPlanKey(string $rawPlanKey, string $planName, ?float $price): string
    {
        $knownKeys = SuperAdminPlanCatalog::keys();
        $normalizedKey = strtolower(trim($rawPlanKey));
        if (in_array($normalizedKey, $knownKeys, true)) {
            return $normalizedKey;
        }

        $normalizedName = $this->normalizeText($planName);
        $nameMatchers = [
            'basico' => 'basico',
            'profesional' => 'profesional',
            'premium' => 'premium',
            'sucursales' => 'sucursales',
            'sucursal' => 'sucursales',
            'multi sede' => 'sucursales',
            'multi-sede' => 'sucursales',
            'multi gym' => 'sucursales',
            'multi-gym' => 'sucursales',
        ];
        foreach ($nameMatchers as $needle => $planKey) {
            if ($normalizedName !== '' && str_contains($normalizedName, $needle)) {
                return $planKey;
            }
        }

        if ($price !== null) {
            foreach (SuperAdminPlanCatalog::defaults() as $default) {
                $catalogPlanKey = strtolower(trim((string) ($default['plan_key'] ?? '')));
                if (! in_array($catalogPlanKey, $knownKeys, true)) {
                    continue;
                }

                $referencePrices = [(float) ($default['price'] ?? 0)];
                if (array_key_exists('discount_price', $default)) {
                    $referencePrices[] = (float) ($default['discount_price'] ?? 0);
                }

                foreach ($referencePrices as $referencePrice) {
                    if (abs($referencePrice - $price) < 0.01) {
                        return $catalogPlanKey;
                    }
                }
            }
        }

        return $this->defaultPlanKey();
    }

    public function defaultPlanKey(): string
    {
        $configured = strtolower(trim((string) config('plan_features.default_plan_key', 'basico')));
        $knownKeys = SuperAdminPlanCatalog::keys();

        return in_array($configured, $knownKeys, true) ? $configured : 'basico';
    }

    private function featureMatrix(): array
    {
        if ($this->featureMatrix !== null) {
            return $this->featureMatrix;
        }

        $this->featureMatrix = (array) config('plan_features.matrix', []);

        return $this->featureMatrix;
    }

    private function accessCacheSeconds(): int
    {
        return max(0, (int) config('plan_features.access_cache_seconds', 60));
    }

    private function cacheKey(int $gymId): string
    {
        return 'plan_access:gym:'.$gymId.':plan_key';
    }

    /**
     * @param  array<int, int|string>  $gymIds
     * @return array<int, int>
     */
    private function normalizeGymIds(array $gymIds): array
    {
        $normalized = [];
        foreach ($gymIds as $gymId) {
            $value = (int) $gymId;
            if ($value <= 0) {
                continue;
            }

            $normalized[$value] = $value;
        }

        return array_values($normalized);
    }

    private function normalizeText(string $value): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($value));

        return mb_strtolower((string) ($normalized ?? ''));
    }
}

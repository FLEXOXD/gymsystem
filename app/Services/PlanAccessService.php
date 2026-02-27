<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Support\SuperAdminPlanCatalog;

class PlanAccessService
{
    /**
     * @var array<int, string>
     */
    private array $resolvedPlanKeysByGym = [];

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

        if (array_key_exists($gymId, $this->resolvedPlanKeysByGym)) {
            return $this->resolvedPlanKeysByGym[$gymId];
        }

        $subscription = Subscription::query()
            ->where('gym_id', $gymId)
            ->select(['plan_key', 'plan_name', 'price'])
            ->first();

        $planKey = $this->inferPlanKey(
            rawPlanKey: (string) ($subscription?->plan_key ?? ''),
            planName: (string) ($subscription?->plan_name ?? ''),
            price: $subscription !== null ? (float) $subscription->price : null
        );

        $this->resolvedPlanKeysByGym[$gymId] = $planKey;

        return $planKey;
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
        $matrix = (array) config('plan_features.matrix', []);
        $defaultPlanFeatures = (array) ($matrix[$this->defaultPlanKey()] ?? []);
        $planFeatures = (array) ($matrix[$planKey] ?? $defaultPlanFeatures);

        return (bool) ($planFeatures[$featureKey] ?? false);
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

    private function normalizeText(string $value): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($value));

        return mb_strtolower((string) ($normalized ?? ''));
    }
}

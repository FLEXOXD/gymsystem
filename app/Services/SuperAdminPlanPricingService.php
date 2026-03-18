<?php

namespace App\Services;

use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SuperAdminPlanPricingService
{
    /**
     * @return array{
     *   promotion:?SuperAdminPromotionTemplate,
     *   base_monthly_price:float,
     *   effective_monthly_price:float,
     *   base_total:float,
     *   final_total:float,
     *   discount_amount:float,
     *   bonus_days:int,
     *   billing_cycles:int
     * }
     */
    public function resolveSelection(
        SuperAdminPlanTemplate $planTemplate,
        int $billingCycles = 1,
        ?float $customMonthlyPrice = null,
        Carbon|string|null $date = null
    ): array {
        $resolvedBillingCycles = max(1, min(24, $billingCycles));
        $baseMonthlyPrice = $this->resolveBaseMonthlyPrice($planTemplate, $customMonthlyPrice);
        $baseTotal = round($baseMonthlyPrice * $resolvedBillingCycles, 2);
        $promotion = $this->resolveApplicablePromotion($planTemplate, $resolvedBillingCycles, $date);

        if (! $promotion) {
            return [
                'promotion' => null,
                'base_monthly_price' => $baseMonthlyPrice,
                'effective_monthly_price' => $baseMonthlyPrice,
                'base_total' => $baseTotal,
                'final_total' => $baseTotal,
                'discount_amount' => 0.0,
                'bonus_days' => 0,
                'billing_cycles' => $resolvedBillingCycles,
            ];
        }

        $value = (float) ($promotion->value ?? 0);
        $discountAmount = 0.0;
        $finalTotal = $baseTotal;
        $bonusDays = 0;

        switch ((string) $promotion->type) {
            case 'percentage':
                $percent = min(max($value, 0), 100);
                $discountAmount = round($baseTotal * ($percent / 100), 2);
                $finalTotal = max(0, round($baseTotal - $discountAmount, 2));
                break;

            case 'fixed':
                $discountAmount = min(max(round($value, 2), 0), $baseTotal);
                $finalTotal = max(0, round($baseTotal - $discountAmount, 2));
                break;

            case 'final_price':
                $finalTotal = max(0, round($value, 2));
                $discountAmount = max(0, round($baseTotal - $finalTotal, 2));
                break;

            case 'bonus_days':
                $bonusDays = max(0, (int) round($value));
                break;

            case 'two_for_one':
            case 'bring_friend':
                $percent = $value > 0 ? min(max($value, 0), 100) : 50;
                $discountAmount = round($baseTotal * ($percent / 100), 2);
                $finalTotal = max(0, round($baseTotal - $discountAmount, 2));
                break;
        }

        $effectiveMonthlyPrice = round($finalTotal / $resolvedBillingCycles, 2);

        return [
            'promotion' => $promotion,
            'base_monthly_price' => $baseMonthlyPrice,
            'effective_monthly_price' => $effectiveMonthlyPrice,
            'base_total' => $baseTotal,
            'final_total' => $finalTotal,
            'discount_amount' => $discountAmount,
            'bonus_days' => $bonusDays,
            'billing_cycles' => $resolvedBillingCycles,
        ];
    }

    /**
     * @param  iterable<int, SuperAdminPlanTemplate>  $planTemplates
     * @return array<int, list<array<string, int|float|string|null>>>
     */
    public function promotionRulesForPlanTemplates(iterable $planTemplates, Carbon|string|null $date = null): array
    {
        if (! $this->supportsPromotionCatalog()) {
            return [];
        }

        $templateIds = collect($planTemplates)
            ->map(static fn (SuperAdminPlanTemplate $template): int => (int) $template->id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values()
            ->all();

        if ($templateIds === []) {
            return [];
        }

        $targetDate = $this->resolveTargetDate($date);

        return SuperAdminPromotionTemplate::query()
            ->where('status', 'active')
            ->whereIn('plan_template_id', $templateIds)
            ->where(function ($query) use ($targetDate): void {
                $query->whereNull('starts_at')
                    ->orWhereDate('starts_at', '<=', $targetDate);
            })
            ->where(function ($query) use ($targetDate): void {
                $query->whereNull('ends_at')
                    ->orWhereDate('ends_at', '>=', $targetDate);
            })
            ->orderByRaw('CASE WHEN duration_months IS NULL THEN 1 ELSE 0 END')
            ->orderBy('duration_months')
            ->orderByDesc('id')
            ->get(['id', 'plan_template_id', 'name', 'type', 'value', 'duration_months'])
            ->groupBy(static fn (SuperAdminPromotionTemplate $promotion): int => (int) $promotion->plan_template_id)
            ->map(function (Collection $group): array {
                return $group
                    ->map(function (SuperAdminPromotionTemplate $promotion): array {
                        return [
                            'id' => (int) $promotion->id,
                            'name' => (string) $promotion->name,
                            'type' => (string) $promotion->type,
                            'value' => $promotion->value !== null ? (float) $promotion->value : null,
                            'duration_months' => $promotion->duration_months !== null ? (int) $promotion->duration_months : null,
                        ];
                    })
                    ->values()
                    ->all();
            })
            ->all();
    }

    private function resolveApplicablePromotion(
        SuperAdminPlanTemplate $planTemplate,
        int $billingCycles,
        Carbon|string|null $date = null
    ): ?SuperAdminPromotionTemplate {
        if (! $this->supportsPromotionCatalog()) {
            return null;
        }

        $targetDate = $this->resolveTargetDate($date);

        return SuperAdminPromotionTemplate::query()
            ->where('status', 'active')
            ->where('plan_template_id', (int) $planTemplate->id)
            ->where(function ($query) use ($targetDate): void {
                $query->whereNull('starts_at')
                    ->orWhereDate('starts_at', '<=', $targetDate);
            })
            ->where(function ($query) use ($targetDate): void {
                $query->whereNull('ends_at')
                    ->orWhereDate('ends_at', '>=', $targetDate);
            })
            ->where(function ($query) use ($billingCycles): void {
                $query->where('duration_months', $billingCycles)
                    ->orWhereNull('duration_months');
            })
            ->orderByRaw('CASE WHEN duration_months = '.$billingCycles.' THEN 0 ELSE 1 END')
            ->orderByDesc('id')
            ->first();
    }

    private function resolveBaseMonthlyPrice(SuperAdminPlanTemplate $planTemplate, ?float $customMonthlyPrice = null): float
    {
        $resolvedPlanKey = method_exists($planTemplate, 'resolvedFeaturePlanKey')
            ? (string) $planTemplate->resolvedFeaturePlanKey()
            : (string) ($planTemplate->feature_plan_key ?? $planTemplate->plan_key ?? 'basico');

        if ($resolvedPlanKey === 'sucursales' && $customMonthlyPrice !== null && $customMonthlyPrice > 0) {
            return round($customMonthlyPrice, 2);
        }

        return round((float) $planTemplate->price, 2);
    }

    private function resolveTargetDate(Carbon|string|null $date = null): string
    {
        if ($date instanceof Carbon) {
            return $date->copy()->toDateString();
        }

        if (is_string($date) && trim($date) !== '') {
            return Carbon::parse($date)->toDateString();
        }

        return Carbon::today()->toDateString();
    }

    private function supportsPromotionCatalog(): bool
    {
        return Schema::hasTable('superadmin_promotion_templates')
            && Schema::hasColumns('superadmin_promotion_templates', [
                'plan_template_id',
                'name',
                'type',
                'value',
                'starts_at',
                'ends_at',
                'status',
                'duration_months',
            ]);
    }
}

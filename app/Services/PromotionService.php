<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionService
{
    /**
     * @return array{
     *   promotion:?Promotion,
     *   base_price:float,
     *   final_price:float,
     *   discount_amount:float,
     *   bonus_days:int
     * }
     */
    public function resolveForSale(int $gymId, Plan $plan, mixed $promotionId, Carbon|string|null $date = null): array
    {
        $basePrice = round((float) $plan->price, 2);
        $targetDate = $date instanceof Carbon
            ? $date->copy()->toDateString()
            : (is_string($date) && trim($date) !== '' ? Carbon::parse($date)->toDateString() : now()->toDateString());

        if (! is_numeric($promotionId) || (int) $promotionId <= 0) {
            return [
                'promotion' => null,
                'base_price' => $basePrice,
                'final_price' => $basePrice,
                'discount_amount' => 0.0,
                'bonus_days' => 0,
            ];
        }

        $promotion = Promotion::query()
            ->forGym($gymId)
            ->active()
            ->where('id', (int) $promotionId)
            ->applicableOn($targetDate)
            ->where(function ($query) use ($plan): void {
                $query->whereNull('plan_id')
                    ->orWhere('plan_id', $plan->id);
            })
            ->first();

        if (! $promotion) {
            return [
                'promotion' => null,
                'base_price' => $basePrice,
                'final_price' => $basePrice,
                'discount_amount' => 0.0,
                'bonus_days' => 0,
            ];
        }

        if ($promotion->max_uses !== null && (int) $promotion->times_used >= (int) $promotion->max_uses) {
            return [
                'promotion' => null,
                'base_price' => $basePrice,
                'final_price' => $basePrice,
                'discount_amount' => 0.0,
                'bonus_days' => 0,
            ];
        }

        $value = (float) ($promotion->value ?? 0);
        $discountAmount = 0.0;
        $finalPrice = $basePrice;
        $bonusDays = 0;

        switch ($promotion->type) {
            case 'percentage':
                $percent = min(max($value, 0), 100);
                $discountAmount = round($basePrice * ($percent / 100), 2);
                $finalPrice = max(0, round($basePrice - $discountAmount, 2));
                break;

            case 'fixed':
                $discountAmount = min(max(round($value, 2), 0), $basePrice);
                $finalPrice = max(0, round($basePrice - $discountAmount, 2));
                break;

            case 'final_price':
                $finalPrice = max(0, round($value, 2));
                $discountAmount = max(0, round($basePrice - $finalPrice, 2));
                break;

            case 'bonus_days':
                $bonusDays = max(0, (int) round($value));
                break;

            case 'two_for_one':
            case 'bring_friend':
                // Simplificado: aplica 50% por defecto como incentivo comercial.
                $percent = $value > 0 ? min(max($value, 0), 100) : 50;
                $discountAmount = round($basePrice * ($percent / 100), 2);
                $finalPrice = max(0, round($basePrice - $discountAmount, 2));
                break;
        }

        return [
            'promotion' => $promotion,
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'discount_amount' => $discountAmount,
            'bonus_days' => $bonusDays,
        ];
    }
}


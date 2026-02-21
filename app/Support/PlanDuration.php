<?php

namespace App\Support;

use App\Models\Plan;
use Carbon\Carbon;

class PlanDuration
{
    public static function normalizeUnit(?string $unit): string
    {
        return strtolower(trim((string) $unit)) === 'months' ? 'months' : 'days';
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function normalizeForPersistence(array $data): array
    {
        $unit = self::normalizeUnit((string) ($data['duration_unit'] ?? 'days'));
        $data['duration_unit'] = $unit;

        if ($unit === 'months') {
            $months = max(1, (int) ($data['duration_months'] ?? 1));
            $data['duration_months'] = $months;
            // Legacy compatibility: keep duration_days populated.
            $data['duration_days'] = max(1, $months * 30);

            return $data;
        }

        $days = max(1, (int) ($data['duration_days'] ?? 30));
        $data['duration_days'] = $days;
        $data['duration_months'] = null;

        return $data;
    }

    public static function label(?string $unit, int $durationDays, ?int $durationMonths): string
    {
        if (self::normalizeUnit($unit) === 'months') {
            $months = max(1, (int) ($durationMonths ?? 1));

            return $months.' '.($months === 1 ? 'mes' : 'meses');
        }

        $days = max(1, (int) $durationDays);

        return $days.' '.($days === 1 ? 'día' : 'días');
    }

    public static function calculateEndsAt(Carbon|string $startsAt, Plan|array $plan, int $bonusDays = 0): Carbon
    {
        $start = $startsAt instanceof Carbon
            ? $startsAt->copy()->startOfDay()
            : Carbon::parse((string) $startsAt)->startOfDay();

        $unit = $plan instanceof Plan
            ? self::normalizeUnit($plan->duration_unit ?? 'days')
            : self::normalizeUnit((string) ($plan['duration_unit'] ?? 'days'));

        $durationDays = $plan instanceof Plan
            ? (int) $plan->duration_days
            : (int) ($plan['duration_days'] ?? 1);

        $durationMonths = $plan instanceof Plan
            ? ($plan->duration_months !== null ? (int) $plan->duration_months : null)
            : (isset($plan['duration_months']) ? (int) $plan['duration_months'] : null);

        $bonusDays = max(0, (int) $bonusDays);

        if ($unit === 'months') {
            $months = max(1, (int) ($durationMonths ?? 1));
            $endsAt = $start->copy()->addMonthsNoOverflow($months);
            if ($bonusDays > 0) {
                $endsAt->addDays($bonusDays);
            }

            return $endsAt;
        }

        $days = max(1, $durationDays) + $bonusDays;

        return $start->copy()->addDays($days - 1);
    }
}

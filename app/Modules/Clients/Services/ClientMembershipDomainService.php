<?php

namespace App\Modules\Clients\Services;

use App\Models\Promotion;
use App\Models\Plan;
use App\Support\PlanDuration;
use Carbon\Carbon;

class ClientMembershipDomainService
{
    /**
     * @return array{ends_at: Carbon, status: string}
     */
    public function resolveMembershipWindow(Carbon $startsAt, Plan $plan, int $bonusDays): array
    {
        $endsAt = PlanDuration::calculateEndsAt(
            startsAt: $startsAt,
            plan: $plan,
            bonusDays: $bonusDays
        );

        return [
            'ends_at' => $endsAt,
            'status' => $endsAt->isBefore(now()->startOfDay()) ? 'expired' : 'active',
        ];
    }

    public function buildMembershipCashDescription(
        int $membershipId,
        string $planName,
        float $basePrice,
        ?Promotion $promotion
    ): string {
        $description = 'Cobro membresía #'.$membershipId
            .' - Plan '.$planName
            .' (PVP '.number_format($basePrice, 2, '.', '').')';

        if ($promotion) {
            $description .= ' - Promo '.$promotion->name;
        }

        return $description;
    }
}


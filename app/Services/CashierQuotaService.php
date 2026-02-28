<?php

namespace App\Services;

use App\Models\GymBranchLink;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class CashierQuotaService
{
    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

    public function maxForGym(int $gymId): int
    {
        if ($gymId <= 0) {
            return 0;
        }

        $planKey = $this->planAccessService->currentPlanKeyForGym($gymId);
        $baseQuota = match ($planKey) {
            'profesional' => 1,
            'premium' => 2,
            'sucursales' => 2,
            default => 0,
        };

        // For managed branches under a "sucursales" hub, keep 2 cashiers per branch.
        $branchLink = GymBranchLink::query()
            ->where('branch_gym_id', $gymId)
            ->where('status', 'active')
            ->first(['hub_gym_id']);

        if ($branchLink && $this->planAccessService->currentPlanKeyForGym((int) $branchLink->hub_gym_id) === 'sucursales') {
            return max($baseQuota, 2);
        }

        return $baseQuota;
    }

    public function countForGym(int $gymId): int
    {
        if ($gymId <= 0) {
            return 0;
        }

        if (! Schema::hasColumn('users', 'role')) {
            return 0;
        }

        $query = User::query()
            ->where('gym_id', $gymId)
            ->where('role', User::ROLE_CASHIER);

        if (Schema::hasColumn('users', 'is_active')) {
            $query->where('is_active', true);
        }

        return $query->count();
    }

    public function remainingForGym(int $gymId): int
    {
        $remaining = $this->maxForGym($gymId) - $this->countForGym($gymId);

        return max(0, $remaining);
    }

    public function canCreateForGym(int $gymId): bool
    {
        return $this->remainingForGym($gymId) > 0;
    }
}

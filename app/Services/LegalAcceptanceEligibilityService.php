<?php

namespace App\Services;

use App\Models\GymBranchLink;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class LegalAcceptanceEligibilityService
{
    public function canUserAccept(?User $user): bool
    {
        if (! $user || $user->gym_id === null) {
            return false;
        }

        $gymId = (int) $user->gym_id;
        if ($gymId <= 0) {
            return false;
        }

        if ($this->isBranchGym($gymId)) {
            return false;
        }

        return $this->isPrincipalOwner($user, $gymId);
    }

    private function isBranchGym(int $gymId): bool
    {
        if (! Schema::hasTable('gym_branch_links')) {
            return false;
        }

        return GymBranchLink::query()
            ->where('branch_gym_id', $gymId)
            ->where('status', 'active')
            ->exists();
    }

    private function isPrincipalOwner(User $user, int $gymId): bool
    {
        if (Schema::hasColumn('users', 'role')) {
            if (! $user->isOwner()) {
                return false;
            }

            $principalOwnerId = (int) (User::query()
                ->where('gym_id', $gymId)
                ->where('role', User::ROLE_OWNER)
                ->orderBy('id')
                ->value('id') ?? 0);

            return $principalOwnerId > 0 && (int) $user->id === $principalOwnerId;
        }

        $principalUserId = (int) (User::query()
            ->where('gym_id', $gymId)
            ->orderBy('id')
            ->value('id') ?? 0);

        return $principalUserId > 0 && (int) $user->id === $principalUserId;
    }
}

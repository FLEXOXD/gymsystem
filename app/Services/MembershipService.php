<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Membership;
use Carbon\Carbon;

class MembershipService
{
    /**
     * Get current active and valid membership for a client.
     */
    public function getActiveMembership(Client $client): ?Membership
    {
        $today = Carbon::today()->toDateString();

        return Membership::query()
            ->where('gym_id', $client->gym_id)
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->whereDate('starts_at', '<=', $today)
            ->whereDate('ends_at', '>=', $today)
            ->orderByDesc('ends_at')
            ->first();
    }

    /**
     * Determine if a client can enter based on membership status and dates.
     */
    public function isValid(Client $client): bool
    {
        return $this->getActiveMembership($client) !== null;
    }
}

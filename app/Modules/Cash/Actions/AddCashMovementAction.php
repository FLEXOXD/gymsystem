<?php

namespace App\Modules\Cash\Actions;

use App\Services\CashSessionService;

class AddCashMovementAction
{
    public function __construct(
        private readonly CashSessionService $cashSessionService
    ) {
    }

    /**
     * @param array{
     *   type: string,
     *   amount: float|int|string,
     *   method: string,
     *   membership_id?: int|string|null,
     *   description?: string|null
     * } $data
     */
    public function execute(int $gymId, int $userId, array $data): void
    {
        $membershipId = isset($data['membership_id']) ? (int) $data['membership_id'] : null;

        $this->cashSessionService->addMovement(
            gymId: $gymId,
            userId: $userId,
            type: (string) $data['type'],
            amount: (float) $data['amount'],
            method: (string) $data['method'],
            membershipId: $membershipId,
            description: $data['description'] ?? null
        );
    }
}


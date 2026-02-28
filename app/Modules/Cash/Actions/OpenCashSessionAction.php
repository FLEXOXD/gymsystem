<?php

namespace App\Modules\Cash\Actions;

use App\Services\CashSessionService;

class OpenCashSessionAction
{
    public function __construct(
        private readonly CashSessionService $cashSessionService
    ) {
    }

    /**
     * @param array{opening_balance: float|int|string, notes?: string|null} $data
     */
    public function execute(int $gymId, int $userId, array $data): void
    {
        $this->cashSessionService->openSession(
            gymId: $gymId,
            userId: $userId,
            openingBalance: (float) $data['opening_balance'],
            notes: $data['notes'] ?? null
        );
    }
}


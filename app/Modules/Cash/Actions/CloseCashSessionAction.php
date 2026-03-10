<?php

namespace App\Modules\Cash\Actions;

use App\Models\CashSession;
use App\Services\CashSessionService;

class CloseCashSessionAction
{
    public function __construct(
        private readonly CashSessionService $cashSessionService
    ) {
    }

    /**
     * @param array{closing_balance: float|int|string, notes?: string|null, difference_reason?: string|null} $data
     */
    public function execute(int $gymId, int $userId, array $data): CashSession
    {
        return $this->cashSessionService->closeSession(
            gymId: $gymId,
            userId: $userId,
            closingBalance: (float) $data['closing_balance'],
            closingNotes: $data['notes'] ?? null,
            differenceReason: $data['difference_reason'] ?? null
        );
    }
}

<?php

namespace App\Modules\Cash\Services;

use App\Models\CashMovement;
use Illuminate\Support\Collection;

class CashSessionReadService
{
    /**
     * @return array<string, float|int>
     */
    public function buildSessionSummary(int $gymId, int $sessionId, float $openingBalance): array
    {
        $totals = CashMovement::query()
            ->forGym($gymId)
            ->where('cash_session_id', $sessionId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->selectRaw('COUNT(*) as movements_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN 1 ELSE 0 END), 0) as income_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN 1 ELSE 0 END), 0) as expense_count")
            ->first();

        $incomeTotal = (float) ($totals->income_total ?? 0);
        $expenseTotal = (float) ($totals->expense_total ?? 0);

        return [
            'income_total' => $incomeTotal,
            'expense_total' => $expenseTotal,
            'expected_balance' => round($openingBalance + $incomeTotal - $expenseTotal, 2),
            'movements_count' => (int) ($totals->movements_count ?? 0),
            'income_count' => (int) ($totals->income_count ?? 0),
            'expense_count' => (int) ($totals->expense_count ?? 0),
        ];
    }

    public function buildMethodTotals(int $gymId, int $sessionId): Collection
    {
        return CashMovement::query()
            ->forGym($gymId)
            ->where('cash_session_id', $sessionId)
            ->selectRaw('method')
            ->selectRaw('COUNT(*) as movements_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->groupBy('method')
            ->orderBy('method')
            ->get();
    }

    public function latestMovements(int $gymId, int $sessionId, int $limit = 10): Collection
    {
        return CashMovement::query()
            ->forGym($gymId)
            ->where('cash_session_id', $sessionId)
            ->select([
                'id',
                'gym_id',
                'cash_session_id',
                'type',
                'amount',
                'method',
                'membership_id',
                'created_by',
                'description',
                'occurred_at',
            ])
            ->with([
                'createdBy:id,name',
                'membership:id,client_id',
                'membership.client:id,first_name,last_name',
            ])
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get();
    }
}


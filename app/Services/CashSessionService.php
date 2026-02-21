<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CashSessionService
{
    /**
     * Get open session for a gym.
     */
    public function getOpenSession(int $gymId): ?CashSession
    {
        return CashSession::query()
            ->forGym($gymId)
            ->open()
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Open a new cash session.
     *
     * @throws RuntimeException
     */
    public function openSession(int $gymId, int $userId, float $openingBalance, ?string $notes = null): CashSession
    {
        return DB::transaction(function () use ($gymId, $userId, $openingBalance, $notes): CashSession {
            $hasOpenSession = CashSession::query()
                ->forGym($gymId)
                ->open()
                ->lockForUpdate()
                ->exists();

            if ($hasOpenSession) {
                throw new RuntimeException('Ya existe una caja abierta para este gimnasio.');
            }

            return CashSession::query()->create([
                'gym_id' => $gymId,
                'opened_by' => $userId,
                'opened_at' => Carbon::now(),
                'opening_balance' => round($openingBalance, 2),
                'status' => 'open',
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Add a movement to current open session.
     *
     * @throws RuntimeException
     */
    public function addMovement(
        int $gymId,
        int $userId,
        string $type,
        float $amount,
        string $method,
        ?int $membershipId = null,
        ?string $description = null
    ): CashMovement {
        $session = $this->getOpenSession($gymId);
        if (! $session) {
            throw new RuntimeException('Debe abrir caja para registrar movimientos.');
        }

        $membership = null;
        if ($membershipId) {
            $membership = Membership::query()
                ->forGym($gymId)
                ->select(['id', 'gym_id'])
                ->find($membershipId);
            if (! $membership) {
                throw new RuntimeException('La membresía no pertenece al gimnasio actual.');
            }
        }

        return CashMovement::query()->create([
            'gym_id' => $gymId,
            'cash_session_id' => $session->id,
            'type' => $type,
            'amount' => round($amount, 2),
            'method' => $method,
            'membership_id' => $membership?->id,
            'created_by' => $userId,
            'description' => $description,
            'occurred_at' => Carbon::now(),
        ]);
    }

    /**
     * Close the current open session and calculate expected balance.
     *
     * @throws RuntimeException
     */
    public function closeSession(int $gymId, int $userId, float $closingBalance, ?string $notes = null): CashSession
    {
        return DB::transaction(function () use ($gymId, $userId, $closingBalance, $notes): CashSession {
            $session = CashSession::query()
                ->forGym($gymId)
                ->open()
                ->lockForUpdate()
                ->first();

            if (! $session) {
                throw new RuntimeException('No existe una caja abierta para cerrar.');
            }

            $totals = CashMovement::query()
                ->forGym($gymId)
                ->where('cash_session_id', $session->id)
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
                ->first();

            $incomeTotal = (float) ($totals->income_total ?? 0);
            $expenseTotal = (float) ($totals->expense_total ?? 0);

            $expectedBalance = round(((float) $session->opening_balance) + $incomeTotal - $expenseTotal, 2);
            $difference = round($closingBalance - $expectedBalance, 2);

            $session->update([
                'closed_by' => $userId,
                'closed_at' => Carbon::now(),
                'closing_balance' => round($closingBalance, 2),
                'expected_balance' => $expectedBalance,
                'difference' => $difference,
                'status' => 'closed',
                'notes' => $notes ?: $session->notes,
            ]);

            return $session->fresh();
        });
    }
}

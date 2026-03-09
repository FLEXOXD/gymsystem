<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $this->assertUserCanOpenCash(gymId: $gymId, userId: $userId);

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
        $this->assertUserCanManageMovements(gymId: $gymId, userId: $userId);

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
        $this->assertUserCanCloseCash(gymId: $gymId, userId: $userId);

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

    private function assertUserCanOpenCash(int $gymId, int $userId): void
    {
        $user = $this->resolveOperator($gymId, $userId);
        if ($user && ! $user->canOpenCashBox()) {
            throw new RuntimeException('No tienes permiso para abrir caja. Esta acción la controla el dueño del gimnasio.');
        }
    }

    private function assertUserCanCloseCash(int $gymId, int $userId): void
    {
        $user = $this->resolveOperator($gymId, $userId);
        if ($user && ! $user->canCloseCashBox()) {
            throw new RuntimeException('No tienes permiso para cerrar caja. Esta acción la controla el dueño del gimnasio.');
        }
    }

    private function assertUserCanManageMovements(int $gymId, int $userId): void
    {
        $user = $this->resolveOperator($gymId, $userId);
        if ($user && ! $user->canManageCashMovements()) {
            throw new RuntimeException('No tienes permiso para registrar movimientos de caja.');
        }
    }

    private function resolveOperator(int $gymId, int $userId): ?User
    {
        if ($gymId <= 0 || $userId <= 0 || ! Schema::hasColumn('users', 'role')) {
            return null;
        }

        $columns = ['id', 'gym_id', 'role'];
        if (Schema::hasColumn('users', 'is_active')) {
            $columns[] = 'is_active';
        }
        if (Schema::hasColumn('users', 'can_open_cash')) {
            $columns[] = 'can_open_cash';
        }
        if (Schema::hasColumn('users', 'can_close_cash')) {
            $columns[] = 'can_close_cash';
        }
        if (Schema::hasColumn('users', 'can_manage_cash_movements')) {
            $columns[] = 'can_manage_cash_movements';
        }

        $user = User::query()
            ->where('id', $userId)
            ->first($columns);

        if (! $user) {
            throw new RuntimeException('No se pudo validar permisos del operador de caja.');
        }

        $operatorGymId = (int) ($user->gym_id ?? 0);
        if ($operatorGymId !== $gymId && ! $this->canHubOperateBranchCash($operatorGymId, $gymId)) {
            throw new RuntimeException('No tienes permisos para operar caja en esta sede.');
        }

        if (! $user->isActiveAccount()) {
            throw new RuntimeException('Tu usuario está desactivado.');
        }

        return $user;
    }

    private function canHubOperateBranchCash(int $operatorGymId, int $targetGymId): bool
    {
        if ($operatorGymId <= 0 || $targetGymId <= 0 || $operatorGymId === $targetGymId) {
            return false;
        }

        return GymBranchLink::query()
            ->where('hub_gym_id', $operatorGymId)
            ->where('branch_gym_id', $targetGymId)
            ->where('status', 'active')
            ->where('cash_managed_by_hub', true)
            ->exists();
    }
}

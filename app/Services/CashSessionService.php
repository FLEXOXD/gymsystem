<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\Gym;
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
        $this->autoCloseExpiredSessions($gymId);

        return $this->queryOpenSession($gymId);
    }

    /**
     * Open a new cash session.
     *
     * @throws RuntimeException
     */
    public function openSession(int $gymId, int $userId, float $openingBalance, ?string $notes = null): CashSession
    {
        $this->assertUserCanOpenCash(gymId: $gymId, userId: $userId);
        $this->autoCloseExpiredSessions($gymId);

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
                'opened_at' => $this->currentGymTimestamp($gymId),
                'opening_balance' => round($openingBalance, 2),
                'status' => 'open',
                'notes' => $this->normalizeText($notes),
                'close_source' => 'manual',
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
        ?string $description = null,
        Carbon|string|null $occurredAt = null
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
                throw new RuntimeException('La membresia no pertenece al gimnasio actual.');
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
            'occurred_at' => $this->normalizeOccurredAt($gymId, $occurredAt),
        ]);
    }

    /**
     * Close the current open session and calculate expected balance.
     *
     * @throws RuntimeException
     */
    public function closeSession(
        int $gymId,
        int $userId,
        float $closingBalance,
        ?string $closingNotes = null,
        ?string $differenceReason = null
    ): CashSession {
        $this->assertUserCanCloseCash(gymId: $gymId, userId: $userId);
        $this->autoCloseExpiredSessions($gymId);

        return DB::transaction(function () use ($gymId, $userId, $closingBalance, $closingNotes, $differenceReason): CashSession {
            $session = CashSession::query()
                ->forGym($gymId)
                ->open()
                ->lockForUpdate()
                ->first();

            if (! $session) {
                throw new RuntimeException('No existe una caja abierta para cerrar.');
            }

            return $this->closeSessionRecord(
                $session,
                closedBy: $userId,
                closingBalance: $closingBalance,
                closingNotes: $closingNotes,
                differenceReason: $differenceReason,
                closeSource: 'manual',
                closedAt: $this->nowForGym($gymId)
            );
        });
    }

    /**
     * Auto-close any cash session that crossed local midnight.
     */
    public function autoCloseExpiredSessions(?int $gymId = null): int
    {
        $query = CashSession::query()
            ->open()
            ->select([
                'id',
                'gym_id',
                'opened_at',
                'opening_balance',
                'status',
            ])
            ->with(['gym:id,timezone']);

        if ($gymId !== null && $gymId > 0) {
            $query->forGym($gymId);
        }

        $sessions = $query->get();
        $closedCount = 0;

        foreach ($sessions as $session) {
            $timezone = $this->resolveGymTimezone(
                (int) $session->gym_id,
                $session->gym instanceof Gym ? $session->gym : null
            );
            $closeAt = $this->midnightClosureTime($session, $timezone);

            if (! $closeAt || $this->nowForGym((int) $session->gym_id, $timezone)->lt($closeAt)) {
                continue;
            }

            $didClose = DB::transaction(function () use ($session): bool {
                $lockedSession = CashSession::query()
                    ->with(['gym:id,timezone'])
                    ->lockForUpdate()
                    ->find($session->id);

                if (! $lockedSession || (string) $lockedSession->status !== 'open') {
                    return false;
                }

                $timezone = $this->resolveGymTimezone(
                    (int) $lockedSession->gym_id,
                    $lockedSession->gym instanceof Gym ? $lockedSession->gym : null
                );
                $closeAt = $this->midnightClosureTime($lockedSession, $timezone);

                if (! $closeAt || $this->nowForGym((int) $lockedSession->gym_id, $timezone)->lt($closeAt)) {
                    return false;
                }

                $totals = $this->calculateSessionTotals(
                    gymId: (int) $lockedSession->gym_id,
                    sessionId: $lockedSession->id,
                    openingBalance: (float) $lockedSession->opening_balance
                );

                $this->closeSessionRecord(
                    $lockedSession,
                    closedBy: null,
                    closingBalance: (float) $totals['expected_balance'],
                    closingNotes: null,
                    differenceReason: null,
                    closeSource: 'auto_midnight',
                    closedAt: $closeAt
                );

                return true;
            });

            if ($didClose) {
                $closedCount++;
            }
        }

        return $closedCount;
    }

    private function closeSessionRecord(
        CashSession $session,
        ?int $closedBy,
        float $closingBalance,
        ?string $closingNotes,
        ?string $differenceReason,
        string $closeSource,
        Carbon $closedAt
    ): CashSession {
        $totals = $this->calculateSessionTotals(
            gymId: (int) $session->gym_id,
            sessionId: $session->id,
            openingBalance: (float) $session->opening_balance
        );

        $expectedBalance = (float) $totals['expected_balance'];
        $difference = round($closingBalance - $expectedBalance, 2);
        $normalizedDifferenceReason = abs($difference) > 0.00001
            ? $this->normalizeText($differenceReason)
            : null;

        if ($closeSource === 'manual' && abs($difference) > 0.00001 && $normalizedDifferenceReason === null) {
            throw new RuntimeException('Debes indicar el motivo de la diferencia antes de cerrar caja.');
        }

        $session->update([
            'closed_by' => $closedBy,
            'closed_at' => $closedAt->format('Y-m-d H:i:s'),
            'closing_balance' => round($closingBalance, 2),
            'expected_balance' => $expectedBalance,
            'difference' => $difference,
            'status' => 'closed',
            'closing_notes' => $this->normalizeText($closingNotes),
            'difference_reason' => $normalizedDifferenceReason,
            'close_source' => $closeSource,
        ]);

        return $session->fresh();
    }

    /**
     * @return array{income_total:float,expense_total:float,expected_balance:float}
     */
    private function calculateSessionTotals(int $gymId, int $sessionId, float $openingBalance): array
    {
        $totals = CashMovement::query()
            ->forGym($gymId)
            ->where('cash_session_id', $sessionId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->first();

        $incomeTotal = (float) ($totals->income_total ?? 0);
        $expenseTotal = (float) ($totals->expense_total ?? 0);

        return [
            'income_total' => $incomeTotal,
            'expense_total' => $expenseTotal,
            'expected_balance' => round($openingBalance + $incomeTotal - $expenseTotal, 2),
        ];
    }

    private function queryOpenSession(int $gymId): ?CashSession
    {
        return CashSession::query()
            ->forGym($gymId)
            ->open()
            ->orderByDesc('id')
            ->first();
    }

    private function currentGymTimestamp(int $gymId): string
    {
        return $this->nowForGym($gymId)->format('Y-m-d H:i:s');
    }

    private function nowForGym(int $gymId, ?string $timezone = null): Carbon
    {
        return Carbon::now($timezone ?: $this->resolveGymTimezone($gymId));
    }

    private function normalizeOccurredAt(int $gymId, Carbon|string|null $occurredAt): string
    {
        $timezone = $this->resolveGymTimezone($gymId);

        if ($occurredAt instanceof Carbon) {
            return $occurredAt->copy()->timezone($timezone)->format('Y-m-d H:i:s');
        }

        if (is_string($occurredAt) && trim($occurredAt) !== '') {
            return Carbon::parse($occurredAt, $timezone)->format('Y-m-d H:i:s');
        }

        return $this->nowForGym($gymId, $timezone)->format('Y-m-d H:i:s');
    }

    private function midnightClosureTime(CashSession $session, string $timezone): ?Carbon
    {
        $openedAt = $this->parseStoredDateTime((string) $session->getRawOriginal('opened_at'), $timezone);

        return $openedAt?->copy()->addDay()->startOfDay();
    }

    private function parseStoredDateTime(string $value, string $timezone): ?Carbon
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        try {
            return Carbon::parse($normalized, $timezone);
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolveGymTimezone(int $gymId, ?Gym $gym = null): string
    {
        $timezone = trim((string) ($gym?->timezone ?? ''));

        if ($timezone === '' && $gymId > 0) {
            $timezone = trim((string) Gym::query()
                ->whereKey($gymId)
                ->value('timezone'));
        }

        return in_array($timezone, timezone_identifiers_list(), true) ? $timezone : 'UTC';
    }

    private function normalizeText(?string $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function assertUserCanOpenCash(int $gymId, int $userId): void
    {
        $user = $this->resolveOperator($gymId, $userId);
        if ($user && ! $user->canOpenCashBox()) {
            throw new RuntimeException('No tienes permiso para abrir caja. Esta accion la controla el dueno del gimnasio.');
        }
    }

    private function assertUserCanCloseCash(int $gymId, int $userId): void
    {
        $user = $this->resolveOperator($gymId, $userId);
        if ($user && ! $user->canCloseCashBox()) {
            throw new RuntimeException('No tienes permiso para cerrar caja. Esta accion la controla el dueno del gimnasio.');
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
            throw new RuntimeException('Tu usuario esta desactivado.');
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

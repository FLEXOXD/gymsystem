<?php

namespace App\Services;

use App\Models\PresenceSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresenceSessionService
{
    public function countOpenForGym(int $gymId): int
    {
        if ($gymId <= 0) {
            return 0;
        }

        return PresenceSession::query()
            ->forGym($gymId)
            ->open()
            ->count();
    }

    /**
     * Close any stale open session for the same client and create a fresh session.
     */
    public function registerCheckIn(
        int $gymId,
        int $clientId,
        int $checkInBy,
        string $checkInMethod,
        ?int $attendanceId = null,
        ?Carbon $checkInAt = null
    ): void {
        if ($gymId <= 0 || $clientId <= 0) {
            return;
        }

        $resolvedCheckInAt = ($checkInAt ?? now())->copy();
        $resolvedMethod = $this->normalizeMethod($checkInMethod);
        $resolvedBy = $checkInBy > 0 ? $checkInBy : null;
        $resolvedAttendanceId = ($attendanceId ?? 0) > 0 ? (int) $attendanceId : null;

        DB::transaction(function () use (
            $gymId,
            $clientId,
            $resolvedCheckInAt,
            $resolvedBy,
            $resolvedMethod,
            $resolvedAttendanceId
        ): void {
            PresenceSession::query()
                ->forGym($gymId)
                ->where('client_id', $clientId)
                ->open()
                ->lockForUpdate()
                ->get()
                ->each(function (PresenceSession $session) use ($resolvedCheckInAt, $resolvedBy, $resolvedMethod): void {
                    $session->forceFill([
                        'check_out_at' => $resolvedCheckInAt,
                        'check_out_by' => $resolvedBy,
                        'check_out_method' => $resolvedMethod,
                        'check_out_reason' => 'auto_reentry',
                    ])->save();
                });

            PresenceSession::query()->create([
                'gym_id' => $gymId,
                'client_id' => $clientId,
                'check_in_attendance_id' => $resolvedAttendanceId,
                'check_in_by' => $resolvedBy,
                'check_in_method' => $resolvedMethod,
                'check_in_at' => $resolvedCheckInAt,
            ]);
        }, 3);
    }

    public function registerCheckOut(
        int $gymId,
        int $clientId,
        int $checkOutBy,
        string $checkOutMethod,
        ?Carbon $checkOutAt = null,
        string $reason = 'manual'
    ): ?PresenceSession {
        if ($gymId <= 0 || $clientId <= 0) {
            return null;
        }

        $resolvedCheckOutAt = ($checkOutAt ?? now())->copy();
        $resolvedMethod = $this->normalizeMethod($checkOutMethod);
        $resolvedBy = $checkOutBy > 0 ? $checkOutBy : null;
        $resolvedReason = trim($reason) !== '' ? trim($reason) : 'manual';

        return DB::transaction(function () use (
            $gymId,
            $clientId,
            $resolvedCheckOutAt,
            $resolvedBy,
            $resolvedMethod,
            $resolvedReason
        ): ?PresenceSession {
            $openSession = PresenceSession::query()
                ->forGym($gymId)
                ->where('client_id', $clientId)
                ->open()
                ->orderByDesc('check_in_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (! $openSession) {
                return null;
            }

            $openSession->forceFill([
                'check_out_at' => $resolvedCheckOutAt,
                'check_out_by' => $resolvedBy,
                'check_out_method' => $resolvedMethod,
                'check_out_reason' => $resolvedReason,
            ])->save();

            return $openSession->fresh();
        }, 3);
    }

    private function normalizeMethod(string $method): string
    {
        $normalized = strtolower(trim($method));

        return $normalized !== '' ? $normalized : 'document';
    }
}

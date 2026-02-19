<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceCheckinService
{
    /**
     * Resolve a check-in value and register attendance when valid.
     *
     * @return array{
     *     ok: bool,
     *     reason: 'success'|'duplicate_attendance'|'membership_inactive'|'credential_inactive'|'client_inactive'|'not_found'|'validation_error',
     *     message: string,
     *     method: 'rfid'|'qr'|'document'|null,
     *     client: array{
     *         id:int,
     *         full_name:string,
     *         photo_url:?string,
     *         membership_ends_at:?string,
     *         month_visits:?int,
     *         total_visits:int,
     *         last_attendance_date:?string,
     *         last_attendance_time:?string,
     *         gender:'male'|'female'|'neutral'
     *     }|null,
     *     attendance: array{id:int,date:string,time:string}|null,
     *     attempt: array{date:string,time:string}|null
     * }
     */
    public function checkInByValue(int $gymId, int $userId, string $value): array
    {
        $normalizedValue = trim($value);
        $attemptAt = Carbon::now();
        $today = $attemptAt->toDateString();
        $attemptPayload = [
            'date' => $today,
            'time' => $attemptAt->format('H:i:s'),
        ];

        if ($normalizedValue === '') {
            return $this->buildResponse(
                false,
                'Debes ingresar un valor para check-in.',
                null,
                null,
                null,
                $attemptPayload,
                'validation_error'
            );
        }

        $candidate = DB::table('client_credentials as credentials')
            ->join('clients as clients', function ($join): void {
                $join->on('clients.id', '=', 'credentials.client_id')
                    ->on('clients.gym_id', '=', 'credentials.gym_id');
            })
            ->where('credentials.gym_id', $gymId)
            ->where('credentials.value', $normalizedValue)
            ->orderByRaw("CASE WHEN credentials.status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('credentials.id')
            ->select([
                'clients.id as client_id',
                'clients.first_name',
                'clients.last_name',
                'clients.photo_path',
                'clients.gender as client_gender',
                'clients.status as client_status',
                'credentials.id as credential_id',
                'credentials.type as credential_type',
                'credentials.status as credential_status',
            ])
            ->first();

        if (! $candidate) {
            $documentCandidate = DB::table('clients')
                ->where('gym_id', $gymId)
                ->where('document_number', $normalizedValue)
                ->select([
                    'id as client_id',
                    'first_name',
                    'last_name',
                    'photo_path',
                    'gender as client_gender',
                    'status as client_status',
                ])
                ->first();

            if (! $documentCandidate) {
                return $this->buildResponse(
                    ok: false,
                    message: 'No existe un cliente para este valor en el gimnasio.',
                    method: null,
                    client: null,
                    reason: 'not_found'
                );
            }

            $candidate = (object) [
                'client_id' => $documentCandidate->client_id,
                'first_name' => $documentCandidate->first_name,
                'last_name' => $documentCandidate->last_name,
                'photo_path' => $documentCandidate->photo_path,
                'client_gender' => $documentCandidate->client_gender,
                'client_status' => $documentCandidate->client_status,
                'credential_id' => null,
                'credential_type' => null,
                'credential_status' => null,
            ];
        }

        $latestAttendance = $this->latestAttendanceForClient($gymId, (int) $candidate->client_id);
        $totalVisits = $this->countTotalVisits($gymId, (int) $candidate->client_id);

        $clientPayload = [
            'id' => (int) $candidate->client_id,
            'full_name' => trim(((string) $candidate->first_name).' '.((string) $candidate->last_name)),
            'photo_url' => $this->resolvePhotoUrl($candidate->photo_path),
            'membership_ends_at' => null,
            'month_visits' => $this->countMonthVisits($gymId, (int) $candidate->client_id, $today),
            'total_visits' => $totalVisits,
            'last_attendance_date' => $latestAttendance['date'] ?? null,
            'last_attendance_time' => $latestAttendance['time'] ?? null,
            'gender' => $this->normalizeGender($candidate->client_gender ?? null),
        ];

        $method = $candidate->credential_type
            ? (string) $candidate->credential_type
            : 'document';

        if ($candidate->credential_id && $candidate->credential_status === 'inactive') {
            return $this->buildResponse(
                ok: false,
                message: 'Credencial inactiva o no encontrada.',
                method: $method,
                client: $clientPayload,
                attempt: $attemptPayload,
                reason: 'credential_inactive'
            );
        }

        if ($candidate->client_status !== 'active') {
            return $this->buildResponse(
                ok: false,
                message: 'Cliente inactivo. No puede ingresar.',
                method: $method,
                client: $clientPayload,
                attempt: $attemptPayload,
                reason: 'client_inactive'
            );
        }

        $membership = Membership::query()
            ->forGym($gymId)
            ->where('client_id', (int) $candidate->client_id)
            ->activeOn($today)
            ->select(['id', 'ends_at'])
            ->orderByDesc('ends_at')
            ->first();

        if (! $membership) {
            $clientPayload['membership_ends_at'] = $this->latestMembershipEndsAt($gymId, (int) $candidate->client_id);
            // For non-active memberships we show historical visits for business context.
            $clientPayload['month_visits'] = $clientPayload['total_visits'];

            return $this->buildResponse(
                ok: false,
                message: 'Membresia no vigente o inactiva.',
                method: $method,
                client: $clientPayload,
                attempt: null,
                reason: 'membership_inactive'
            );
        }

        $clientPayload['membership_ends_at'] = $membership->ends_at?->toDateString();

        try {
            $attendance = Attendance::query()->create([
                'gym_id' => $gymId,
                'client_id' => (int) $candidate->client_id,
                'credential_id' => $candidate->credential_id ? (int) $candidate->credential_id : null,
                'date' => $today,
                'time' => Carbon::now()->format('H:i:s'),
                'created_by' => $userId,
            ]);
        } catch (QueryException $exception) {
            if ($this->isDuplicateAttendance($exception)) {
                $todayAttendance = $this->todayAttendanceForClient($gymId, (int) $candidate->client_id, $today);

                return $this->buildResponse(
                    ok: false,
                    message: 'Asistencia ya registrada hoy',
                    method: $method,
                    client: $clientPayload,
                    attempt: [
                        'date' => $todayAttendance['date'] ?? $today,
                        'time' => $todayAttendance['time'] ?? ($latestAttendance['time'] ?? $attemptAt->format('H:i:s')),
                    ],
                    reason: 'duplicate_attendance'
                );
            }

            throw $exception;
        }

        return $this->buildResponse(
            ok: true,
            message: 'Check-in registrado correctamente.',
            method: $method,
            client: array_merge($clientPayload, [
                'month_visits' => ($clientPayload['month_visits'] ?? 0) + 1,
                'total_visits' => $totalVisits + 1,
                'last_attendance_date' => $today,
                'last_attendance_time' => (string) $attendance->time,
            ]),
            attendance: [
                'id' => (int) $attendance->id,
                'date' => $today,
                'time' => (string) $attendance->time,
            ],
            attempt: $attemptPayload,
            reason: 'success'
        );
    }

    /**
     * @return array{
     *     ok: bool,
     *     reason: string,
     *     message: string,
     *     method: 'rfid'|'qr'|'document'|null,
     *     client: array{
     *         id:int,
     *         full_name:string,
     *         photo_url:?string,
     *         membership_ends_at:?string,
     *         month_visits:?int,
     *         total_visits:int,
     *         last_attendance_date:?string,
     *         last_attendance_time:?string,
     *         gender:'male'|'female'|'neutral'
     *     }|null,
     *     attendance: array{id:int,date:string,time:string}|null,
     *     attempt: array{date:string,time:string}|null
     * }
     */
    private function buildResponse(
        bool $ok,
        string $message,
        ?string $method,
        ?array $client,
        ?array $attendance = null,
        ?array $attempt = null,
        string $reason = 'validation_error'
    ): array
    {
        return [
            'ok' => $ok,
            'reason' => $reason,
            'message' => $message,
            'method' => $method,
            'client' => $client,
            'attendance' => $attendance,
            'attempt' => $attempt,
        ];
    }

    private function resolvePhotoUrl(?string $photoPath): ?string
    {
        if (! $photoPath) {
            return null;
        }

        $path = trim($photoPath);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['storage/', '/storage/'])) {
            return url('/'.ltrim($path, '/'));
        }

        return Storage::url($path);
    }

    private function isDuplicateAttendance(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $sqlState === '23000' && in_array($driverCode, [1062, 19], true);
    }

    private function countMonthVisits(int $gymId, int $clientId, string $referenceDate): int
    {
        $baseDate = Carbon::parse($referenceDate);

        return Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->whereBetween('date', [
                $baseDate->copy()->startOfMonth()->toDateString(),
                $baseDate->copy()->endOfMonth()->toDateString(),
            ])
            ->count();
    }

    private function countTotalVisits(int $gymId, int $clientId): int
    {
        return Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->count();
    }

    /**
     * @return array{date:string,time:string}|null
     */
    private function latestAttendanceForClient(int $gymId, int $clientId): ?array
    {
        $latest = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->select(['date', 'time'])
            ->first();

        if (! $latest) {
            return null;
        }

        return [
            'date' => (string) ($latest->date?->toDateString() ?? ''),
            'time' => (string) ($latest->time ?? ''),
        ];
    }

    /**
     * @return array{date:string,time:string}|null
     */
    private function todayAttendanceForClient(int $gymId, int $clientId, string $today): ?array
    {
        $todayRow = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->where('date', $today)
            ->select(['date', 'time'])
            ->first();

        if (! $todayRow) {
            return null;
        }

        return [
            'date' => (string) ($todayRow->date?->toDateString() ?? $today),
            'time' => (string) ($todayRow->time ?? ''),
        ];
    }

    private function latestMembershipEndsAt(int $gymId, int $clientId): ?string
    {
        $value = Membership::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('ends_at')
            ->value('ends_at');

        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->toDateString();
    }

    /**
     * @return 'male'|'female'|'neutral'
     */
    private function normalizeGender(mixed $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['male', 'female'], true) ? $normalized : 'neutral';
    }
}

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
     *     message: string,
     *     method: 'rfid'|'qr'|'document'|null,
     *     client: array{id:int,full_name:string,photo_url:?string,membership_ends_at:?string}|null
     * }
     */
    public function checkInByValue(int $gymId, int $userId, string $value): array
    {
        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            return $this->buildResponse(false, 'Debes ingresar un valor para check-in.', null, null);
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
                    'status as client_status',
                ])
                ->first();

            if (! $documentCandidate) {
                return $this->buildResponse(
                    ok: false,
                    message: 'No existe un cliente para este valor en el gimnasio.',
                    method: null,
                    client: null
                );
            }

            $candidate = (object) [
                'client_id' => $documentCandidate->client_id,
                'first_name' => $documentCandidate->first_name,
                'last_name' => $documentCandidate->last_name,
                'photo_path' => $documentCandidate->photo_path,
                'client_status' => $documentCandidate->client_status,
                'credential_id' => null,
                'credential_type' => null,
                'credential_status' => null,
            ];
        }

        $clientPayload = [
            'id' => (int) $candidate->client_id,
            'full_name' => trim(((string) $candidate->first_name).' '.((string) $candidate->last_name)),
            'photo_url' => $this->resolvePhotoUrl($candidate->photo_path),
            'membership_ends_at' => null,
        ];

        $method = $candidate->credential_type
            ? (string) $candidate->credential_type
            : 'document';

        if ($candidate->credential_id && $candidate->credential_status === 'inactive') {
            return $this->buildResponse(
                ok: false,
                message: 'Credencial inactiva o no encontrada.',
                method: $method,
                client: $clientPayload
            );
        }

        if ($candidate->client_status !== 'active') {
            return $this->buildResponse(
                ok: false,
                message: 'Cliente inactivo. No puede ingresar.',
                method: $method,
                client: $clientPayload
            );
        }

        $today = Carbon::today()->toDateString();

        $membership = Membership::query()
            ->forGym($gymId)
            ->where('client_id', (int) $candidate->client_id)
            ->activeOn($today)
            ->select(['id', 'ends_at'])
            ->orderByDesc('ends_at')
            ->first();

        if (! $membership) {
            return $this->buildResponse(
                ok: false,
                message: 'Membresia no vigente o inactiva.',
                method: $method,
                client: $clientPayload
            );
        }

        $clientPayload['membership_ends_at'] = $membership->ends_at?->toDateString();

        try {
            Attendance::query()->create([
                'gym_id' => $gymId,
                'client_id' => (int) $candidate->client_id,
                'credential_id' => $candidate->credential_id ? (int) $candidate->credential_id : null,
                'date' => $today,
                'time' => Carbon::now()->format('H:i:s'),
                'created_by' => $userId,
            ]);
        } catch (QueryException $exception) {
            if ($this->isDuplicateAttendance($exception)) {
                return $this->buildResponse(
                    ok: false,
                    message: 'Asistencia ya registrada hoy',
                    method: $method,
                    client: $clientPayload
                );
            }

            throw $exception;
        }

        return $this->buildResponse(
            ok: true,
            message: 'Check-in registrado correctamente.',
            method: $method,
            client: $clientPayload
        );
    }

    /**
     * @return array{
     *     ok: bool,
     *     message: string,
     *     method: 'rfid'|'qr'|'document'|null,
     *     client: array{id:int,full_name:string,photo_url:?string,membership_ends_at:?string}|null
     * }
     */
    private function buildResponse(bool $ok, string $message, ?string $method, ?array $client): array
    {
        return [
            'ok' => $ok,
            'message' => $message,
            'method' => $method,
            'client' => $client,
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
}

<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientCredential;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceService
{
    public function __construct(
        private readonly MembershipService $membershipService
    ) {
    }

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
            return $this->buildResponse(
                ok: false,
                message: 'Debes ingresar un valor para check-in.',
                method: null,
                client: null
            );
        }

        $method = null;
        $client = null;
        $credentialId = null;

        $credential = ClientCredential::query()
            ->forGym($gymId)
            ->active()
            ->where('value', $normalizedValue)
            ->select(['id', 'gym_id', 'client_id', 'type', 'value', 'status'])
            ->with(['client' => fn ($query) => $query
                ->forGym($gymId)
                ->select(['id', 'gym_id', 'first_name', 'last_name', 'photo_path', 'status'])
            ])
            ->first();

        if ($credential && $credential->client) {
            $client = $credential->client;
            $credentialId = $credential->id;
            $method = $credential->type;
        }

        if (! $client) {
            $inactiveCredential = ClientCredential::query()
                ->forGym($gymId)
                ->where('value', $normalizedValue)
                ->where('status', 'inactive')
                ->select(['id', 'gym_id', 'client_id', 'type', 'value', 'status'])
                ->with(['client' => fn ($query) => $query
                    ->forGym($gymId)
                    ->select(['id', 'gym_id', 'first_name', 'last_name', 'photo_path', 'status'])
                ])
                ->first();

            if ($inactiveCredential) {
                return $this->buildResponse(
                    ok: false,
                    message: 'Credencial inactiva o no encontrada.',
                    method: $inactiveCredential->type,
                    client: $inactiveCredential->client
                        ? $this->buildClientPayload($inactiveCredential->client, null)
                        : null
                );
            }

            $client = Client::query()
                ->forGym($gymId)
                ->where('document_number', $normalizedValue)
                ->select(['id', 'gym_id', 'first_name', 'last_name', 'photo_path', 'status'])
                ->first();

            if ($client) {
                $method = 'document';
            }
        }

        if (! $client) {
            return $this->buildResponse(
                ok: false,
                message: 'No existe un cliente para este valor en el gimnasio.',
                method: null,
                client: null
            );
        }

        if ($client->status !== 'active') {
            return $this->buildResponse(
                ok: false,
                message: 'Cliente inactivo. No puede ingresar.',
                method: $method,
                client: $this->buildClientPayload($client, null)
            );
        }

        $membership = $this->membershipService->getActiveMembership($client);

        if (! $membership) {
            return $this->buildResponse(
                ok: false,
                message: 'Membresia no vigente o inactiva.',
                method: $method,
                client: $this->buildClientPayload($client, null)
            );
        }

        $today = Carbon::today()->toDateString();

        if (Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $client->id)
            ->whereDate('date', $today)
            ->exists()) {
            return $this->buildResponse(
                ok: false,
                message: 'Asistencia ya registrada hoy',
                method: $method,
                client: $this->buildClientPayload($client, $membership)
            );
        }

        try {
            Attendance::query()->create([
                'gym_id' => $gymId,
                'client_id' => $client->id,
                'credential_id' => $credentialId,
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
                    client: $this->buildClientPayload($client, $membership)
                );
            }

            throw $exception;
        }

        return $this->buildResponse(
            ok: true,
            message: 'Check-in registrado correctamente.',
            method: $method,
            client: $this->buildClientPayload($client, $membership)
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

    /**
     * @return array{id:int,full_name:string,photo_url:?string,membership_ends_at:?string}
     */
    private function buildClientPayload(Client $client, ?Membership $membership): array
    {
        return [
            'id' => $client->id,
            'full_name' => $client->full_name,
            'photo_url' => $this->resolvePhotoUrl($client->photo_path),
            'membership_ends_at' => $membership?->ends_at?->toDateString(),
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

        return $sqlState === '23000' && $driverCode === 1062;
    }
}

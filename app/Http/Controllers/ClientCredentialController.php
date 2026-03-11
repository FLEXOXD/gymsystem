<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRfidCredentialRequest;
use App\Models\Client;
use App\Models\ClientCredential;
use App\Support\ActiveGymContext;
use App\Support\ClientAudit;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

class ClientCredentialController extends Controller
{
    /**
     * Store an RFID credential for a client in current gym.
     */
    public function storeRfid(StoreRfidCredentialRequest $request, string $contextGym, int $client): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['rfid' => 'Selecciona una sucursal específica para gestionar credenciales.']);
        }

        $gymId = $this->resolveGymId($request);
        $clientModel = Client::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id'])
            ->findOrFail($client);
        $value = $request->validated('value');

        try {
            ClientCredential::query()->create([
                'gym_id' => $gymId,
                'client_id' => $clientModel->id,
                'type' => 'rfid',
                'value' => $value,
                'status' => 'active',
            ]);
        } catch (QueryException $exception) {
            if ($this->isDuplicateCredential($exception)) {
                return redirect()
                    ->route('clients.show', $clientModel->id)
                    ->withErrors([
                        'rfid' => 'Este UID RFID ya existe en este gimnasio.',
                    ])
                    ->withInput();
            }

            throw $exception;
        }

        $clientModel->update(ClientAudit::managementAttributesFromUser($this->resolveActor($request)));

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'RFID asignado correctamente.');
    }

    /**
     * Generate a QR credential for a client in current gym.
     */
    public function generateQr(Request $request, string $contextGym, int $client): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['qr' => 'Selecciona una sucursal específica para gestionar credenciales.']);
        }

        $gymId = $this->resolveGymId($request);
        $clientModel = Client::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id'])
            ->findOrFail($client);

        $value = $this->generateQrValue($gymId);

        ClientCredential::query()->create([
            'gym_id' => $gymId,
            'client_id' => $clientModel->id,
            'type' => 'qr',
            'value' => $value,
            'status' => 'active',
        ]);

        $clientModel->update(ClientAudit::managementAttributesFromUser($this->resolveActor($request)));

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'QR generado correctamente.')
            ->with('generated_qr_value', $value);
    }

    /**
     * Deactivate a credential from current gym.
     */
    public function deactivate(Request $request, string $contextGym, int $credential): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('clients.index')
                ->withErrors(['credential' => 'Selecciona una sucursal específica para gestionar credenciales.']);
        }

        $gymId = $this->resolveGymId($request);

        $credentialModel = ClientCredential::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'client_id', 'status'])
            ->findOrFail($credential);

        $credentialModel->update([
            'status' => 'inactive',
        ]);

        Client::query()
            ->forGym($gymId)
            ->whereKey((int) $credentialModel->client_id)
            ->update(ClientAudit::managementAttributesFromUser($this->resolveActor($request)));

        return redirect()
            ->route('clients.show', $credentialModel->client_id)
            ->with('status', 'Credencial desactivada.');
    }

    private function generateQrValue(int $gymId): string
    {
        do {
            $value = 'qr_'.Str::uuid()->toString();
            $exists = ClientCredential::query()
                ->forGym($gymId)
                ->where('value', $value)
                ->exists();
        } while ($exists);

        return $value;
    }

    private function isDuplicateCredential(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $sqlState === '23000' && $driverCode === 1062;
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function resolveActor(Request $request): User
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403, 'Usuario no autenticado.');

        return $actor;
    }
}

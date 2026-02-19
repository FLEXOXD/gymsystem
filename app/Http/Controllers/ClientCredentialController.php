<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRfidCredentialRequest;
use App\Models\Client;
use App\Models\ClientCredential;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientCredentialController extends Controller
{
    /**
     * Store an RFID credential for a client in current gym.
     */
    public function storeRfid(StoreRfidCredentialRequest $request, int $client): RedirectResponse
    {
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

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'RFID asignado correctamente.');
    }

    /**
     * Generate a QR credential for a client in current gym.
     */
    public function generateQr(Request $request, int $client): RedirectResponse
    {
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

        return redirect()
            ->route('clients.show', $clientModel->id)
            ->with('status', 'QR generado correctamente.')
            ->with('generated_qr_value', $value);
    }

    /**
     * Deactivate a credential from current gym.
     */
    public function deactivate(Request $request, int $credential): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);

        $credentialModel = ClientCredential::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'client_id', 'status'])
            ->findOrFail($credential);

        $credentialModel->update([
            'status' => 'inactive',
        ]);

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
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}

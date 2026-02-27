<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Support\ActiveGymContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClientCardController extends Controller
{
    /**
     * Show printable card for one client in current gym.
     */
    public function show(Request $request, string $contextGym, int $client): View|RedirectResponse
    {
        $cardData = $this->resolveCardData($request, $client);
        if ($cardData instanceof RedirectResponse) {
            return $cardData;
        }

        return view('clients.card', $cardData);
    }

    /**
     * Stream client card as PDF.
     */
    public function pdf(Request $request, string $contextGym, int $client): Response|RedirectResponse
    {
        $cardData = $this->resolveCardData($request, $client);
        if ($cardData instanceof RedirectResponse) {
            return $cardData;
        }

        $pdf = Pdf::loadView('clients.card-pdf', $cardData)
            ->setPaper('a4', 'portrait');

        $filename = 'card-client-'.$cardData['client']->id.'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Resolve and validate card data in current gym.
     *
     * @return array<string, mixed>|RedirectResponse
     */
    private function resolveCardData(Request $request, int $client): array|RedirectResponse
    {
        $gymId = ActiveGymContext::id($request);
        $gymIds = ActiveGymContext::ids($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $clientModel = Client::query()
            ->forGyms($gymIds)
            ->select([
                'id',
                'gym_id',
                'first_name',
                'last_name',
                'document_number',
                'phone',
                'photo_path',
                'status',
            ])
            ->with([
                'gym:id,name,logo_path',
                'credentials' => fn ($query) => $query
                    ->select(['id', 'gym_id', 'client_id', 'type', 'value', 'status'])
                    ->orderByDesc('id'),
            ])
            ->findOrFail($client);

        $activeQrCredential = $clientModel->credentials
            ->where('type', 'qr')
            ->where('status', 'active')
            ->first();

        if (! $activeQrCredential) {
            return redirect()
                ->route('clients.show', $clientModel->id)
                ->withErrors([
                    'card' => 'El cliente no tiene un QR activo para imprimir.',
                ]);
        }

        $qrSvg = QrCode::format('svg')
            ->size(280)
            ->margin(1)
            ->generate($activeQrCredential->value);
        $qrSvgBase64 = base64_encode($qrSvg);

        return [
            'client' => $clientModel,
            'gym' => $clientModel->gym,
            'qrValue' => $activeQrCredential->value,
            'qrSvg' => $qrSvg,
            'qrSvgBase64' => $qrSvgBase64,
            'logoUrl' => $this->resolvePublicPath($clientModel->gym?->logo_path),
        ];
    }

    private function resolvePublicPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}

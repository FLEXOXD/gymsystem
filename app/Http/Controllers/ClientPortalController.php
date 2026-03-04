<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClientPortalController extends Controller
{
    /**
     * Landing interno para compartir acceso de clientes (PWA).
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Portal cliente solo opera por sede; evita modo global (consolidado).
        if (ActiveGymContext::isGlobal($request)) {
            $contextGymSlug = trim((string) ($request->route('contextGym') ?? ''));
            if ($contextGymSlug !== '') {
                $params = ['contextGym' => $contextGymSlug];
                if (strtolower(trim((string) $request->query('pwa_mode', ''))) === 'standalone') {
                    $params['pwa_mode'] = 'standalone';
                }

                return redirect()->route('client-portal.index', $params);
            }
        }

        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $activeGym = $request->attributes->get('active_gym');
        if (! $activeGym instanceof Gym) {
            $contextGymSlug = trim((string) ($request->route('contextGym') ?? ''));
            if ($contextGymSlug !== '') {
                $activeGym = Gym::query()
                    ->withoutDemoSessions()
                    ->select(['id', 'name', 'slug'])
                    ->whereRaw('LOWER(slug) = ?', [mb_strtolower($contextGymSlug)])
                    ->first();
            }
        }

        $gymSlug = trim((string) ($activeGym?->slug ?? ($request->route('contextGym') ?? '')));
        $gymName = trim((string) ($activeGym?->name ?? 'Gym'));
        abort_if($gymSlug === '', 404, 'No se pudo resolver la sede para el portal cliente.');

        $clientLoginUrl = route('client-mobile.login', ['gymSlug' => $gymSlug]);
        $shareMessage = 'Ingresa a la app de clientes de '.$gymName.': '.$clientLoginUrl;
        $whatsAppShareUrl = 'https://wa.me/?text='.rawurlencode($shareMessage);
        $facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($clientLoginUrl);
        $portalQrSvg = (string) QrCode::format('svg')
            ->size(340)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($clientLoginUrl);

        return view('client-portal.index', [
            'gymName' => $gymName,
            'gymSlug' => $gymSlug,
            'isGlobalScope' => ActiveGymContext::isGlobal($request),
            'clientLoginUrl' => $clientLoginUrl,
            'shareMessage' => $shareMessage,
            'whatsAppShareUrl' => $whatsAppShareUrl,
            'facebookShareUrl' => $facebookShareUrl,
            'portalQrSvg' => $portalQrSvg,
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\PlanAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePwaStandaloneAccessMiddleware
{
    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

    /**
     * Block standalone PWA mode for plans without pwa_install feature.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->gym_id === null) {
            return $next($request);
        }

        $queryMode = strtolower(trim((string) $request->query('pwa_mode', '')));
        $cookieOrHeaderMode = strtolower(trim((string) $request->cookie('gym_pwa_mode', (string) $request->header('X-PWA-Mode', ''))));
        $pwaMode = $queryMode !== '' ? $queryMode : $cookieOrHeaderMode;
        if ($pwaMode !== 'standalone') {
            return $next($request);
        }

        $activeGymId = (int) ($request->attributes->get('active_gym_id') ?? $user->gym_id ?? 0);
        if ($activeGymId > 0 && $this->planAccessService->canForGym($activeGymId, 'pwa_install')) {
            return $next($request);
        }

        $message = 'Tu plan actual no habilita la app instalable (PWA). Sube a plan profesional, premium o sucursales.';
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
                'feature' => 'pwa_install',
            ], 403);
        }

        abort(403, $message);
    }
}

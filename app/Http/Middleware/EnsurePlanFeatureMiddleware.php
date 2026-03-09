<?php

namespace App\Http\Middleware;

use App\Services\PlanAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanFeatureMiddleware
{
    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'No autorizado.');
        }

        // SuperAdmin users are not restricted by gym plan features.
        if ($user->gym_id === null) {
            return $next($request);
        }

        $featureKey = trim($feature);
        if ($featureKey === '') {
            abort(403, 'No se definió la funcionalidad del plan requerida para esta ruta.');
        }

        $activeGymId = (int) ($request->attributes->get('active_gym_id') ?? $user->gym_id ?? 0);
        if ($activeGymId > 0 && $this->planAccessService->canForGym($activeGymId, $featureKey)) {
            return $next($request);
        }

        $planKey = $activeGymId > 0
            ? $this->planAccessService->currentPlanKeyForGym($activeGymId)
            : $this->planAccessService->currentPlanKey($user);
        $message = 'Tu plan actual no incluye este módulo.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
                'plan_key' => $planKey,
                'feature' => $featureKey,
            ], 403);
        }

        abort(403, $message.' Plan actual: '.$planKey.'.');
    }
}

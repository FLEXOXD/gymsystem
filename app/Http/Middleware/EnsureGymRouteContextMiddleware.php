<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGymRouteContextMiddleware
{
    /**
     * Ensure the URL gym slug belongs to the authenticated gym user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->gym_id) {
            abort(403, 'Solo usuarios de gimnasio pueden acceder a esta ruta.');
        }

        $currentGymSlug = trim((string) ($user->gym?->slug ?? ''));
        $requestedGymSlug = trim((string) $request->route('contextGym'));

        if ($currentGymSlug === '' || $requestedGymSlug === '') {
            abort(403, 'No se pudo determinar el gimnasio de la ruta.');
        }

        if (strcasecmp($currentGymSlug, $requestedGymSlug) !== 0) {
            abort(403, 'No autorizado para acceder a otro gimnasio.');
        }

        return $next($request);
    }
}

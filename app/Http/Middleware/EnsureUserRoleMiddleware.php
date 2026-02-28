<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRoleMiddleware
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(403, 'No autorizado.');
        }

        if (! $user->isActiveAccount()) {
            abort(403, 'Tu usuario esta desactivado. Contacta al administrador del gimnasio.');
        }

        // SuperAdmin access is controlled via dedicated middleware/routes.
        if ($user->gym_id === null) {
            abort(403, 'Solo usuarios de gimnasio pueden acceder a este modulo.');
        }

        $normalizedAllowed = collect($allowedRoles)
            ->map(static fn (string $role): string => strtolower(trim($role)))
            ->filter(static fn (string $role): bool => $role !== '')
            ->values()
            ->all();

        if ($normalizedAllowed === []) {
            abort(403, 'No se definieron roles permitidos para esta ruta.');
        }

        if (! $user->hasRole(...$normalizedAllowed)) {
            abort(403, 'No tienes permisos para este modulo.');
        }

        return $next($request);
    }
}

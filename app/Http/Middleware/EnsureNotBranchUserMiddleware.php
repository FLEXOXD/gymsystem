<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBranchUserMiddleware
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $scope = 'default'): Response
    {
        $user = $request->user();
        if (! $user || $user->gym_id === null) {
            return $next($request);
        }

        $isBranchUser = (bool) $request->attributes->get('gym_context_is_branch', false);
        if (! $isBranchUser) {
            return $next($request);
        }

        $message = match (trim($scope)) {
            'manage_plans' => 'La sucursal secundaria solo puede ver planes.',
            'manage_promotions' => 'La sucursal secundaria solo puede ver promociones.',
            'manage_cash' => 'La sucursal secundaria no puede abrir ni cerrar caja.',
            'manage_branches' => 'Solo la sede principal puede usar el modulo de sucursales.',
            'export_reports' => 'La sucursal secundaria no puede exportar reportes.',
            default => 'Accion no autorizada para sucursal secundaria.',
        };

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
                'reason' => 'branch_read_only',
                'scope' => trim($scope),
            ], 403);
        }

        if ($request->isMethod('GET') && trim($scope) === 'manage_branches') {
            $contextGym = trim((string) ($request->route('contextGym') ?? ''));
            if ($contextGym !== '') {
                return redirect()
                    ->route('panel.index', ['contextGym' => $contextGym])
                    ->with('error', $message);
            }
        }

        abort(403, $message);
    }
}

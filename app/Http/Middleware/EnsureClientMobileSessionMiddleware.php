<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientMobileSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $session = (array) $request->session()->get('client_mobile', []);
        $clientId = (int) ($session['client_id'] ?? 0);
        $gymId = (int) ($session['gym_id'] ?? 0);

        if ($clientId <= 0 || $gymId <= 0) {
            $message = 'Inicia sesion para continuar.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'reason' => 'session_invalid',
                    'message' => $message,
                ], 401);
            }

            return redirect()->route('client-mobile.login', [
                'gymSlug' => (string) $request->route('gymSlug'),
            ])->withErrors([
                'mobile_login' => $message,
            ]);
        }

        return $next($request);
    }
}

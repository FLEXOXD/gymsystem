<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetGymTimezoneMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $gym = $user?->gym;
        $timezone = $gym?->timezone;
        $language = strtolower((string) ($gym?->language_code ?? 'es'));
        $language = str_replace('_', '-', $language);
        if ($language === 'pt-br') {
            $language = 'pt';
        }

        if (is_string($timezone) && in_array($timezone, timezone_identifiers_list(), true)) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        if (in_array($language, ['es', 'en', 'pt'], true)) {
            config(['app.locale' => $language]);
            app()->setLocale($language);
        }

        if ($user && $user->gym_id) {
            $gymSlug = trim((string) ($gym?->slug ?? ''));
            if ($gymSlug !== '') {
                URL::defaults(['contextGym' => $gymSlug]);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Providers;

use App\Support\Currency;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            $request = request();
            $host = strtolower((string) $request->getHost());
            $localHosts = ['127.0.0.1', 'localhost', '::1', '[::1]'];

            $forwardedHostRaw = (string) $request->headers->get('x-forwarded-host', '');
            $forwardedHost = strtolower(trim(explode(',', $forwardedHostRaw)[0] ?? ''));
            $forwardedProtoRaw = (string) $request->headers->get('x-forwarded-proto', '');
            $forwardedProto = strtolower(trim(explode(',', $forwardedProtoRaw)[0] ?? ''));
            $cfVisitor = strtolower((string) $request->headers->get('cf-visitor', ''));

            if ($forwardedProto === 'https' || str_contains($cfVisitor, 'https')) {
                URL::forceScheme('https');
            }
            if ($forwardedHost !== '') {
                $scheme = ($forwardedProto === 'https' || str_contains($cfVisitor, 'https')) ? 'https' : 'http';
                URL::forceRootUrl($scheme.'://'.$forwardedHost);
            }

            $hasCloudflareHeaders = $request->headers->has('cf-ray') || $request->headers->has('cf-visitor');
            $isForwardedExternal = $forwardedHost !== '' && !in_array($forwardedHost, $localHosts, true);
            $isDirectExternal = !in_array($host, $localHosts, true);
            $isHttpsClient = $request->isSecure()
                || $forwardedProto === 'https'
                || str_contains($cfVisitor, 'https');
            $forceBuildAssets = filter_var((string) env('VITE_FORCE_BUILD', 'true'), FILTER_VALIDATE_BOOL);

            $hotFile = public_path('hot');
            $hotUrl = is_file($hotFile) ? trim((string) file_get_contents($hotFile)) : '';
            $hotIsHttp = str_starts_with(strtolower($hotUrl), 'http://');
            $hotWouldBreakOnHttps = $isHttpsClient && $hotIsHttp;

            if ($forceBuildAssets || $isDirectExternal || $isForwardedExternal || $hasCloudflareHeaders || $hotWouldBreakOnHttps) {
                // Evita que dispositivos externos (ej: iPhone por tunnel) usen Vite HMR local.
                Vite::useHotFile(public_path('hot-disabled'));
            }
        }

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email', '');

            return Limit::perMinute(6)
                ->by(strtolower($email).'|'.$request->ip());
        });

        RateLimiter::for('checkin', function (Request $request) {
            $gymId = (string) ($request->user()?->gym_id ?? 'guest');

            return Limit::perMinute(120)
                ->by($gymId.'|'.$request->ip())
                ->response(function () {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Demasiadas solicitudes. Intente nuevamente en unos segundos.',
                        'method' => null,
                        'client' => null,
                    ], 429);
                });
        });

        View::composer('*', function ($view): void {
            $currencyCode = Currency::normalizeCode(auth()->user()?->gym?->currency_code);

            $view->with('appCurrencyCode', $currencyCode);
            $view->with('appCurrencySymbol', Currency::symbol($currencyCode));
        });
    }
}

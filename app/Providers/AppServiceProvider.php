<?php

namespace App\Providers;

use App\Support\Currency;
use App\Support\TestingFilesystem;
use App\Support\WindowsSafeFilesystem;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningUnitTests()) {
            $this->app->singleton('files', static fn () => new TestingFilesystem());
        } elseif (PHP_OS_FAMILY === 'Windows') {
            $this->app->singleton('files', static fn () => new WindowsSafeFilesystem());
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! app()->runningInConsole()) {
            $request = request();
            $host = strtolower((string) $request->getHost());
            $originIsLocal = $this->isLocalOrPrivateHost($host);

            $forwardedHostRaw = (string) $request->headers->get('x-forwarded-host', '');
            $forwardedHost = $this->normalizeForwardedHost($forwardedHostRaw);
            $forwardedProtoRaw = (string) $request->headers->get('x-forwarded-proto', '');
            $forwardedProto = strtolower(trim(explode(',', $forwardedProtoRaw)[0] ?? ''));
            $cfVisitor = strtolower((string) $request->headers->get('cf-visitor', ''));

            if ($forwardedProto === 'https' || str_contains($cfVisitor, 'https')) {
                URL::forceScheme('https');
            }

            if ($originIsLocal && $forwardedHost !== '') {
                $scheme = ($forwardedProto === 'https' || str_contains($cfVisitor, 'https')) ? 'https' : 'http';
                URL::forceRootUrl($scheme.'://'.$forwardedHost);
            }

            $hasCloudflareHeaders = $request->headers->has('cf-ray') || $request->headers->has('cf-visitor');
            $isForwardedExternal = $forwardedHost !== '' && ! $this->isLocalOrPrivateHost($forwardedHost);
            $isDirectExternal = ! $originIsLocal;
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

        RateLimiter::for('demo-request', function (Request $request) {
            $fingerprint = hash(
                'sha256',
                strtolower((string) $request->ip()).'|'.substr(strtolower((string) $request->userAgent()), 0, 180)
            );

            return Limit::perHour(3)
                ->by($fingerprint)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = max(1, (int) ($headers['Retry-After'] ?? 3600));

                    return redirect()
                        ->route('landing')
                        ->with('error', 'Alcanzaste el máximo de 3 intentos de demo. Espera para volver a intentarlo.')
                        ->with('demo_limit_modal', [
                            'retry_after_seconds' => $retryAfter,
                        ]);
                });
        });

        View::composer('*', function ($view): void {
            $currencyCode = Currency::normalizeCode(auth()->user()?->gym?->currency_code);

            $view->with('appCurrencyCode', $currencyCode);
            $view->with('appCurrencySymbol', Currency::symbol($currencyCode));
        });
    }

    private function normalizeForwardedHost(string $headerValue): string
    {
        $candidate = strtolower(trim(explode(',', $headerValue)[0] ?? ''));
        if ($candidate === '') {
            return '';
        }

        if (
            str_contains($candidate, '://')
            || str_contains($candidate, '/')
            || str_contains($candidate, '\\')
            || str_contains($candidate, '@')
            || preg_match('/\s/u', $candidate) === 1
        ) {
            return '';
        }

        $hostPart = $candidate;
        if (str_starts_with($hostPart, '[')) {
            $end = strpos($hostPart, ']');
            if ($end === false) {
                return '';
            }

            $portPart = substr($hostPart, $end + 1);
            if ($portPart !== '' && preg_match('/^:\d{1,5}$/', $portPart) !== 1) {
                return '';
            }

            $hostPart = substr($hostPart, 1, $end - 1);
        } else {
            $segments = explode(':', $hostPart);
            if (count($segments) > 2) {
                return '';
            }

            if (count($segments) === 2) {
                [$hostPart, $port] = $segments;
                if ($hostPart === '' || preg_match('/^\d{1,5}$/', $port) !== 1) {
                    return '';
                }
            }
        }

        if ($hostPart === '') {
            return '';
        }

        if (
            preg_match('/^[a-z0-9.-]+$/', $hostPart) !== 1
            && filter_var($hostPart, FILTER_VALIDATE_IP) === false
        ) {
            return '';
        }

        return $candidate;
    }

    private function isLocalOrPrivateHost(string $host): bool
    {
        $normalized = strtolower(trim($host));
        if ($normalized === '') {
            return false;
        }

        if (str_starts_with($normalized, '[') && str_contains($normalized, ']')) {
            $normalized = trim((string) strstr($normalized, ']', true), '[]');
        } elseif (substr_count($normalized, ':') === 1) {
            $normalized = (string) strstr($normalized, ':', true);
        }

        if (in_array($normalized, ['127.0.0.1', 'localhost', '::1'], true)) {
            return true;
        }

        if (filter_var($normalized, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        return ! filter_var(
            $normalized,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}

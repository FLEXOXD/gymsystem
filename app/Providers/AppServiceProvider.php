<?php

namespace App\Providers;

use App\Support\Currency;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

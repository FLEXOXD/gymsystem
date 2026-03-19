<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SetBrowserPermissionsPolicyMiddleware::class);

        $middleware->alias([
            'check.subscription' => \App\Http\Middleware\CheckSubscriptionMiddleware::class,
            'plan.feature' => \App\Http\Middleware\EnsurePlanFeatureMiddleware::class,
            'not.branch' => \App\Http\Middleware\EnsureNotBranchUserMiddleware::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdminMiddleware::class,
            'role' => \App\Http\Middleware\EnsureUserRoleMiddleware::class,
            'demo.session' => \App\Http\Middleware\EnsureDemoSessionMiddleware::class,
            'gym.timezone' => \App\Http\Middleware\SetGymTimezoneMiddleware::class,
            'gym.route' => \App\Http\Middleware\EnsureGymRouteContextMiddleware::class,
            'pwa.standalone.access' => \App\Http\Middleware\EnsurePwaStandaloneAccessMiddleware::class,
            'owner.activity' => \App\Http\Middleware\TrackGymOwnerActivityMiddleware::class,
            'client.mobile.session' => \App\Http\Middleware\EnsureClientMobileSessionMiddleware::class,
            'no.history' => \App\Http\Middleware\PreventBackHistoryMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ThrottleRequestsException|TooManyRequestsHttpException $exception, Request $request) {
            $headers = $exception->getHeaders();
            $retryAfter = (int) ($headers['Retry-After'] ?? 60);
            $retryAfter = max(1, $retryAfter);

            $message = $retryAfter === 1
                ? 'Demasiados intentos. Espera 1 segundo y vuelve a intentar.'
                : 'Demasiados intentos. Espera '.$retryAfter.' segundos y vuelve a intentar.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'reason' => 'too_many_attempts',
                    'message' => $message,
                    'retry_after_seconds' => $retryAfter,
                ], 429, $headers);
            }

            return back()
                ->withInput($request->except('password'))
                ->withErrors(['throttle' => $message]);
        });

        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->is('logout')) {
                return redirect()
                    ->route('login')
                    ->withHeaders([
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
                        'Pragma' => 'no-cache',
                        'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                    ]);
            }

            return null;
        });
    })->create();


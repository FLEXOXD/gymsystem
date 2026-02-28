<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionMiddleware
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        view()->share('subscription_grace', false);
        view()->share('subscription_grace_days', 3);

        if ($request->routeIs('subscription.expired')) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if (! $user->gym_id) {
            return $next($request);
        }

        $gymId = (int) $user->gym_id;

        $this->subscriptionService->ensureSubscription($gymId);
        $subscription = $this->subscriptionService->checkStatus($gymId);

        if (! $subscription || $subscription->status === 'suspended') {
            return redirect()->route('subscription.expired');
        }

        if ($subscription->status === 'grace') {
            view()->share('subscription_grace', true);
            view()->share('subscription_grace_days', (int) $subscription->grace_days);
        }

        return $next($request);
    }
}

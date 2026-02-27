<?php

namespace App\Http\Middleware;

use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Services\PlanAccessService;
use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionMiddleware
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly PlanAccessService $planAccessService
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
        $isBranchGym = GymBranchLink::query()
            ->where('branch_gym_id', $gymId)
            ->exists();
        $canUseMultiBranch = $this->planAccessService->can($user, 'multi_branch') && ! $isBranchGym;
        $isGlobalScope = strtolower(trim((string) $request->query('scope', ''))) === 'global';
        if ($isGlobalScope && $canUseMultiBranch) {
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

        $routeGymSlug = trim((string) ($request->route('contextGym') ?? ''));
        if ($routeGymSlug !== '') {
            $requestedGym = Gym::query()
                ->withoutDemoSessions()
                ->select(['id', 'slug'])
                ->whereRaw('LOWER(slug) = ?', [mb_strtolower($routeGymSlug)])
                ->first();

            if ($requestedGym) {
                $requestedGymId = (int) $requestedGym->id;
                if ($requestedGymId === $gymId) {
                    $gymId = $requestedGymId;
                } elseif ($canUseMultiBranch) {
                    $isLinkedBranch = GymBranchLink::query()
                        ->where('hub_gym_id', $gymId)
                        ->where('branch_gym_id', $requestedGymId)
                        ->exists();
                    if ($isLinkedBranch) {
                        $gymId = $requestedGymId;
                    }
                }
            }
        }

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

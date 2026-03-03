<?php

namespace App\Http\Middleware;

use App\Models\GymBranchLink;
use App\Services\PlanAccessService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetGymTimezoneMiddleware
{
    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $userGymId = (int) ($user?->gym_id ?? 0);
        $routeGymSlug = trim((string) ($request->route('contextGym') ?? ''));
        $isGlobalScope = strtolower(trim((string) $request->query('scope', ''))) === 'global';
        $isBranchGym = $userGymId > 0
            ? GymBranchLink::query()->where('branch_gym_id', $userGymId)->exists()
            : false;
        $isCashier = $user ? (bool) $user->isCashier() : false;
        $canUseMultiBranch = $user && $userGymId > 0
            ? ! $isCashier && $this->planAccessService->can($user, 'multi_branch') && ! $isBranchGym
            : false;
        $activeGym = $request->attributes->get('active_gym');
        $gym = $activeGym instanceof \App\Models\Gym ? $activeGym : $user?->gym;

        if (
            ! $activeGym instanceof \App\Models\Gym
            && $user
            && $userGymId > 0
            && $routeGymSlug !== ''
            && (! $isGlobalScope || ! $canUseMultiBranch)
        ) {
            $requestedGym = \App\Models\Gym::query()
                ->withoutDemoSessions()
                ->select(['id', 'slug', 'timezone', 'language_code'])
                ->whereRaw('LOWER(slug) = ?', [mb_strtolower($routeGymSlug)])
                ->first();
            if ($requestedGym) {
                $requestedGymId = (int) $requestedGym->id;
                if ($requestedGymId === $userGymId) {
                    $gym = $requestedGym;
                } elseif ($canUseMultiBranch) {
                    $isLinkedBranch = GymBranchLink::query()
                        ->where('hub_gym_id', $userGymId)
                        ->where('branch_gym_id', $requestedGymId)
                        ->exists();
                    if ($isLinkedBranch) {
                        $gym = $requestedGym;
                    }
                }
            }
        }

        $timezone = $gym?->timezone;
        if (! (is_string($timezone) && in_array($timezone, timezone_identifiers_list(), true))) {
            $superAdminTimezone = $user && $userGymId === 0 ? trim((string) ($user->timezone ?? '')) : '';
            if ($superAdminTimezone !== '' && in_array($superAdminTimezone, timezone_identifiers_list(), true)) {
                $timezone = $superAdminTimezone;
            }
        }

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

        if ($user && $userGymId > 0) {
            $effectiveGlobalScope = $isGlobalScope && $canUseMultiBranch;
            $gymSlug = $effectiveGlobalScope
                ? trim((string) ($user?->gym?->slug ?? ''))
                : ($routeGymSlug !== '' ? $routeGymSlug : trim((string) ($gym?->slug ?? '')));
            if ($gymSlug !== '') {
                $defaults = ['contextGym' => $gymSlug];
                if ($effectiveGlobalScope) {
                    $defaults['scope'] = 'global';
                }
                URL::defaults($defaults);
            }
        }

        return $next($request);
    }
}

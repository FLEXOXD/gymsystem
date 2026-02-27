<?php

namespace App\Http\Middleware;

use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Services\PlanAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGymRouteContextMiddleware
{
    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

    /**
     * Ensure the URL gym slug belongs to the authenticated gym user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->gym_id) {
            abort(403, 'Solo usuarios de gimnasio pueden acceder a esta ruta.');
        }

        $userGymId = (int) ($user->gym_id ?? 0);
        $currentGymSlug = trim((string) ($user->gym?->slug ?? ''));
        $requestedGymSlug = trim((string) $request->route('contextGym'));

        if ($currentGymSlug === '' || $requestedGymSlug === '') {
            abort(403, 'No se pudo determinar el gimnasio de la ruta.');
        }

        $requestedGym = Gym::query()
            ->withoutDemoSessions()
            ->select([
                'id',
                'slug',
                'name',
                'address',
                'address_state',
                'address_city',
                'address_line',
                'timezone',
                'currency_code',
                'language_code',
                'logo_path',
                'avatar_male_path',
                'avatar_female_path',
                'avatar_neutral_path',
            ])
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower($requestedGymSlug)])
            ->first();

        if (! $requestedGym) {
            abort(404, 'No existe el gimnasio solicitado.');
        }

        $requestedGymId = (int) $requestedGym->id;
        $canAccessRequestedGym = $requestedGymId === $userGymId;
        $isBranchGym = GymBranchLink::query()
            ->where('branch_gym_id', $userGymId)
            ->exists();
        $canUseMultiBranch = $this->planAccessService->can($user, 'multi_branch') && ! $isBranchGym;

        if (! $canAccessRequestedGym) {
            if (! $canUseMultiBranch) {
                abort(403, 'No autorizado para acceder a otro gimnasio.');
            }

            $isLinkedBranch = GymBranchLink::query()
                ->where('hub_gym_id', $userGymId)
                ->where('branch_gym_id', $requestedGymId)
                ->exists();

            if (! $isLinkedBranch) {
                abort(403, 'No autorizado para acceder a otro gimnasio.');
            }
        }

        $globalRequested = strtolower(trim((string) $request->query('scope', ''))) === 'global';
        $activeGymIds = [$requestedGymId];
        $activeGym = $requestedGym;
        $activeGymId = $requestedGymId;
        $activeGymSlug = (string) $requestedGym->slug;

        if ($globalRequested && $canUseMultiBranch) {
            $linkedBranchIds = GymBranchLink::query()
                ->where('hub_gym_id', $userGymId)
                ->pluck('branch_gym_id')
                ->map(static fn ($id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->values()
                ->all();

            $activeGymIds = array_values(array_unique(array_merge([$userGymId], $linkedBranchIds)));

            $hubGym = Gym::query()
                ->withoutDemoSessions()
                ->select([
                    'id',
                    'slug',
                    'name',
                    'address',
                    'address_state',
                    'address_city',
                    'address_line',
                    'timezone',
                    'currency_code',
                    'language_code',
                    'logo_path',
                    'avatar_male_path',
                    'avatar_female_path',
                    'avatar_neutral_path',
                ])
                ->find($userGymId);

            if ($hubGym) {
                $activeGym = $hubGym;
                $activeGymId = (int) $hubGym->id;
                $activeGymSlug = (string) $hubGym->slug;
            } else {
                $activeGymId = $userGymId;
                $activeGymSlug = $currentGymSlug;
            }
        }

        $request->attributes->set('active_gym_id', $activeGymId);
        $request->attributes->set('active_gym_ids', $activeGymIds);
        $request->attributes->set('active_gym_is_global', $globalRequested && $canUseMultiBranch);
        $request->attributes->set('active_gym_slug', $activeGymSlug);
        $request->attributes->set('active_gym_name', (string) ($activeGym?->name ?? $requestedGym->name));
        $request->attributes->set('active_gym_address', (string) ($activeGym?->address ?? $requestedGym->address ?? ''));
        $request->attributes->set('active_gym', $activeGym);
        $request->attributes->set('hub_gym_id', $userGymId);
        $request->attributes->set('hub_gym_slug', $currentGymSlug);
        $request->attributes->set('gym_context_is_branch', $isBranchGym);
        $request->attributes->set('gym_context_is_hub', ! $isBranchGym);
        $request->attributes->set('gym_context_can_use_multibranch', $canUseMultiBranch);

        return $next($request);
    }
}

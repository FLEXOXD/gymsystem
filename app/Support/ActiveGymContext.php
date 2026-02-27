<?php

namespace App\Support;

use Illuminate\Http\Request;

class ActiveGymContext
{
    public static function id(Request $request): int
    {
        $activeGymId = (int) ($request->attributes->get('active_gym_id') ?? 0);
        if ($activeGymId > 0) {
            return $activeGymId;
        }

        return (int) ($request->user()?->gym_id ?? 0);
    }

    /**
     * @return array<int, int>
     */
    public static function ids(Request $request): array
    {
        $activeGymIds = $request->attributes->get('active_gym_ids');
        if (is_array($activeGymIds)) {
            $ids = collect($activeGymIds)
                ->map(static fn ($id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0)
                ->values()
                ->all();

            if ($ids !== []) {
                return $ids;
            }
        }

        $id = self::id($request);

        return $id > 0 ? [$id] : [];
    }

    public static function isGlobal(Request $request): bool
    {
        if ($request->attributes->has('active_gym_is_global')) {
            return (bool) $request->attributes->get('active_gym_is_global');
        }

        return strtolower(trim((string) $request->query('scope', ''))) === 'global';
    }

    public static function scope(Request $request): string
    {
        return self::isGlobal($request) ? 'global' : 'branch';
    }

    public static function slug(Request $request): string
    {
        $slug = trim((string) ($request->attributes->get('active_gym_slug') ?? ''));
        if ($slug !== '') {
            return $slug;
        }

        $routeSlug = trim((string) ($request->route('contextGym') ?? ''));
        if ($routeSlug !== '') {
            return $routeSlug;
        }

        return trim((string) ($request->user()?->gym?->slug ?? ''));
    }
}

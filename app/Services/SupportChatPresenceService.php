<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SupportChatPresenceService
{
    private const CACHE_KEY = 'support_chat:superadmin_presence';

    public function touchSuperAdmin(User $user): void
    {
        $ttlSeconds = $this->ttlSeconds();
        Cache::put(
            self::CACHE_KEY,
            [
                'user_id' => (int) $user->id,
                'name' => trim((string) $user->name),
                'touched_at' => now()->toIso8601String(),
            ],
            now()->addSeconds($ttlSeconds)
        );
    }

    public function isSuperAdminOnline(): bool
    {
        return $this->activeRepresentative() !== null;
    }

    /**
     * @return array{user_id:int,name:string,touched_at:string}|null
     */
    public function activeRepresentative(): ?array
    {
        $payload = Cache::get(self::CACHE_KEY);
        if (! is_array($payload)) {
            return null;
        }

        $userId = (int) ($payload['user_id'] ?? 0);
        $touchedAtRaw = trim((string) ($payload['touched_at'] ?? ''));
        if ($userId <= 0 || $touchedAtRaw === '') {
            Cache::forget(self::CACHE_KEY);

            return null;
        }

        try {
            $touchedAt = Carbon::parse($touchedAtRaw);
        } catch (Throwable $exception) {
            Cache::forget(self::CACHE_KEY);

            return null;
        }

        if ($touchedAt->lt(now()->subSeconds($this->ttlSeconds()))) {
            Cache::forget(self::CACHE_KEY);

            return null;
        }

        return [
            'user_id' => $userId,
            'name' => trim((string) ($payload['name'] ?? 'SuperAdmin')),
            'touched_at' => $touchedAtRaw,
        ];
    }

    public function clearForUser(?User $user): void
    {
        if (! $user instanceof User || ! $user->hasRole(User::ROLE_SUPERADMIN)) {
            return;
        }

        $active = $this->activeRepresentative();
        if ($active === null) {
            Cache::forget(self::CACHE_KEY);

            return;
        }

        if ((int) ($active['user_id'] ?? 0) === (int) $user->id) {
            Cache::forget(self::CACHE_KEY);
        }
    }

    private function ttlSeconds(): int
    {
        return max(30, (int) config('support_chat.agent_presence_ttl_seconds', 45));
    }
}

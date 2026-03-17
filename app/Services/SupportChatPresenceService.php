<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SupportChatPresenceService
{
    private const CACHE_KEY = 'support_chat:superadmin_presence';

    public function touchSuperAdmin(User $user): void
    {
        $ttlSeconds = max(30, (int) config('support_chat.agent_presence_ttl_seconds', 90));
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
        return Cache::has(self::CACHE_KEY);
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

        return [
            'user_id' => (int) ($payload['user_id'] ?? 0),
            'name' => trim((string) ($payload['name'] ?? 'SuperAdmin')),
            'touched_at' => trim((string) ($payload['touched_at'] ?? '')),
        ];
    }
}


<?php

namespace App\Services;

use App\Models\RemoteScanEvent;
use App\Models\RemoteScanSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RemoteScanService
{
    public function createSession(
        int $gymId,
        User $actor,
        string $context,
        int $ttlMinutes = 15,
        bool $forceNew = false,
        ?Carbon $expiresAt = null
    ): RemoteScanSession
    {
        if (! $forceNew) {
            $activeSession = RemoteScanSession::query()
                ->where('gym_id', $gymId)
                ->where('created_by', (int) $actor->id)
                ->where('context', $context)
                ->open()
                ->notExpired()
                ->latest('id')
                ->first();

            if ($activeSession instanceof RemoteScanSession) {
                if ($expiresAt instanceof Carbon && $activeSession->expires_at?->lt($expiresAt)) {
                    $activeSession->update([
                        'expires_at' => $expiresAt->copy(),
                    ]);
                    $activeSession->refresh();
                }

                return $activeSession;
            }
        }

        RemoteScanSession::query()
            ->where('gym_id', $gymId)
            ->where('created_by', (int) $actor->id)
            ->where('context', $context)
            ->where('status', 'open')
            ->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);

        $resolvedExpiresAt = $expiresAt?->copy() ?? now()->addMinutes(max(1, $ttlMinutes));

        return RemoteScanSession::query()->create([
            'gym_id' => $gymId,
            'created_by' => (int) $actor->id,
            'context' => $context,
            'channel_token' => (string) Str::uuid(),
            'status' => 'open',
            'expires_at' => $resolvedExpiresAt,
        ]);
    }

    public function pushCode(RemoteScanSession $session, string $code, string $source = 'camera', array $meta = []): RemoteScanEvent
    {
        return $session->events()->create([
            'code' => strtoupper(trim($code)),
            'source' => $source,
            'meta' => $meta !== [] ? $meta : null,
        ]);
    }

    public function close(RemoteScanSession $session): void
    {
        if ($session->status === 'closed') {
            return;
        }

        $session->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }
}

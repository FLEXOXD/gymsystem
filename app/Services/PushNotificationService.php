<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Collection;

class PushNotificationService
{
    public function __construct(
        private readonly WebPushService $webPushService
    ) {
    }

    /**
     * @return array{sent:int,failed:int,skipped:int}
     */
    public function sendToUser(User $user, array $payload): array
    {
        $subscriptions = PushSubscription::query()
            ->active()
            ->where('user_id', (int) $user->id)
            ->orderByDesc('id')
            ->get();

        return $this->sendToSubscriptions($subscriptions, $payload);
    }

    /**
     * @param  list<string>  $roles
     * @return array{sent:int,failed:int,skipped:int}
     */
    public function sendToGymUsers(int $gymId, array $payload, array $roles = []): array
    {
        if ($gymId <= 0) {
            return ['sent' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $query = User::query()->where('gym_id', $gymId);

        $normalizedRoles = collect($roles)
            ->map(static fn (string $role): string => strtolower(trim($role)))
            ->filter(static fn (string $role): bool => $role !== '')
            ->values();

        if ($normalizedRoles->isNotEmpty()) {
            $query->where(function ($builder) use ($normalizedRoles): void {
                $builder->whereIn('role', $normalizedRoles->all());
                if ($normalizedRoles->contains('owner')) {
                    $builder->orWhereNull('role');
                }
            });
        }

        $users = $query->get()->filter(static function (User $user): bool {
            return method_exists($user, 'isActiveAccount') ? $user->isActiveAccount() : true;
        });

        if ($users->isEmpty()) {
            return ['sent' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $subscriptions = PushSubscription::query()
            ->active()
            ->whereIn('user_id', $users->pluck('id')->map(static fn ($id): int => (int) $id))
            ->orderByDesc('id')
            ->get();

        return $this->sendToSubscriptions($subscriptions, $payload);
    }

    /**
     * @param  Collection<int, PushSubscription>  $subscriptions
     * @return array{sent:int,failed:int,skipped:int}
     */
    public function sendToSubscriptions(Collection $subscriptions, array $payload): array
    {
        $normalizedPayload = $this->normalizePayload($payload);
        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($subscriptions as $subscription) {
            if (! $subscription instanceof PushSubscription || $subscription->revoked_at !== null) {
                $skipped++;
                continue;
            }

            if (! $this->webPushService->isConfigured()) {
                $skipped++;
                continue;
            }

            if ($this->webPushService->sendToSubscription($subscription, $normalizedPayload)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload): array
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $body = trim((string) ($payload['body'] ?? ''));
        $url = trim((string) ($payload['url'] ?? '/app'));
        $tag = trim((string) ($payload['tag'] ?? 'gymsystem-general'));
        $icon = trim((string) ($payload['icon'] ?? '/pwa/icon-maskable.png'));
        $badge = trim((string) ($payload['badge'] ?? '/pwa/icon-maskable.png'));
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        return [
            'title' => $title !== '' ? $title : 'GymSystem',
            'body' => $body !== '' ? $body : 'Tienes una nueva notificacion.',
            'icon' => $icon,
            'badge' => $badge,
            'tag' => $tag,
            'url' => $url !== '' ? $url : '/app',
            'data' => $data,
            'renotify' => (bool) ($payload['renotify'] ?? false),
            'requireInteraction' => (bool) ($payload['requireInteraction'] ?? false),
        ];
    }
}


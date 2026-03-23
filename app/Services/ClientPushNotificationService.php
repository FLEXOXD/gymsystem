<?php

namespace App\Services;

use App\Models\ClientPushSubscription;
use Illuminate\Support\Collection;

class ClientPushNotificationService
{
    public function __construct(
        private readonly WebPushService $webPushService
    ) {
    }

    /**
     * @return array{sent:int,failed:int,skipped:int}
     */
    public function sendToClient(int $gymId, int $clientId, array $payload): array
    {
        if ($gymId <= 0 || $clientId <= 0) {
            return ['sent' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $subscriptions = ClientPushSubscription::query()
            ->active()
            ->where('gym_id', $gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('id')
            ->get();

        return $this->sendToSubscriptions($subscriptions, $payload);
    }

    /**
     * @param Collection<int, ClientPushSubscription> $subscriptions
     * @return array{sent:int,failed:int,skipped:int}
     */
    public function sendToSubscriptions(Collection $subscriptions, array $payload): array
    {
        $normalizedPayload = $this->normalizePayload($payload);
        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($subscriptions as $subscription) {
            if (! $subscription instanceof ClientPushSubscription || $subscription->revoked_at !== null) {
                $skipped++;
                continue;
            }

            if (! $this->webPushService->isConfigured()) {
                $skipped++;
                continue;
            }

            if ($this->webPushService->sendToSubscription($subscription, $normalizedPayload)) {
                $subscription->forceFill(['last_used_at' => now()])->save();
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
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload): array
    {
        $title = trim((string) ($payload['title'] ?? ''));
        $body = trim((string) ($payload['body'] ?? ''));
        $url = trim((string) ($payload['url'] ?? '/app'));
        $tag = trim((string) ($payload['tag'] ?? 'gymsystem-client'));
        $icon = trim((string) ($payload['icon'] ?? '/pwa/icon-maskable.png'));
        $badge = trim((string) ($payload['badge'] ?? '/pwa/icon-maskable.png'));
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        return [
            'title' => $title !== '' ? $title : 'FlexGym',
            'body' => $body !== '' ? $body : 'Tienes una actualizacion en tu progreso.',
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

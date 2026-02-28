<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebPushService
{
    public function isConfigured(): bool
    {
        if (! (bool) config('services.webpush.enabled', false)) {
            return false;
        }

        $subject = trim((string) config('services.webpush.vapid.subject', ''));
        $publicKey = trim((string) config('services.webpush.vapid.public_key', ''));
        $privateKey = trim((string) config('services.webpush.vapid.private_key', ''));

        if ($subject === '' || $publicKey === '' || $privateKey === '') {
            return false;
        }

        return class_exists(\Minishlink\WebPush\WebPush::class)
            && class_exists(\Minishlink\WebPush\Subscription::class);
    }

    public function sendToSubscription(PushSubscription $subscription, array $payload): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        if ($subscription->revoked_at !== null) {
            return false;
        }

        try {
            $webPushClass = \Minishlink\WebPush\WebPush::class;
            $subscriptionClass = \Minishlink\WebPush\Subscription::class;

            $webPush = new $webPushClass([
                'VAPID' => [
                    'subject' => (string) config('services.webpush.vapid.subject'),
                    'publicKey' => (string) config('services.webpush.vapid.public_key'),
                    'privateKey' => (string) config('services.webpush.vapid.private_key'),
                ],
            ], [
                'TTL' => 60,
                'urgency' => 'high',
                'topic' => 'gymsystem-pwa',
            ]);

            if (method_exists($webPush, 'setReuseVAPIDHeaders')) {
                $webPush->setReuseVAPIDHeaders(true);
            }

            $target = $subscriptionClass::create([
                'endpoint' => (string) $subscription->endpoint,
                'publicKey' => (string) $subscription->public_key,
                'authToken' => (string) $subscription->auth_token,
                'contentEncoding' => (string) ($subscription->content_encoding ?: 'aesgcm'),
            ]);

            $report = $webPush->sendOneNotification(
                $target,
                json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ['urgency' => 'high']
            );

            $isSuccess = method_exists($report, 'isSuccess') ? (bool) $report->isSuccess() : false;
            if ($isSuccess) {
                $subscription->forceFill([
                    'last_used_at' => now(),
                ])->save();

                return true;
            }

            $responseStatus = null;
            if (method_exists($report, 'getResponse')) {
                $response = $report->getResponse();
                $responseStatus = method_exists($response, 'getStatusCode') ? (int) $response->getStatusCode() : null;
            }

            $expiredByProvider = method_exists($report, 'isSubscriptionExpired')
                ? (bool) $report->isSubscriptionExpired()
                : false;

            if ($expiredByProvider || in_array($responseStatus, [404, 410], true)) {
                $subscription->forceFill([
                    'revoked_at' => now(),
                ])->save();
            }

            Log::warning('push.delivery.failed', [
                'subscription_id' => (int) $subscription->id,
                'endpoint_hash' => (string) $subscription->endpoint_hash,
                'status_code' => $responseStatus,
                'reason' => method_exists($report, 'getReason') ? (string) $report->getReason() : 'unknown',
            ]);
        } catch (Throwable $exception) {
            Log::warning('push.delivery.exception', [
                'subscription_id' => (int) $subscription->id,
                'endpoint_hash' => (string) $subscription->endpoint_hash,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }
}


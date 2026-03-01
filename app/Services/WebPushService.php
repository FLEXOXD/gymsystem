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
            $this->bootstrapOpenSslEnvironment();

            $webPushClass = \Minishlink\WebPush\WebPush::class;
            $subscriptionClass = \Minishlink\WebPush\Subscription::class;
            $proxy = config('services.webpush.proxy');
            $clientOptions = [
                // Force direct connection by default to avoid inherited broken proxies.
                'proxy' => is_string($proxy) && trim($proxy) !== '' ? trim($proxy) : '',
            ];

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
            ], 30, $clientOptions);

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

            $isSuccess = is_object($report) && method_exists($report, 'isSuccess')
                ? (bool) $report->isSuccess()
                : false;
            if ($isSuccess) {
                $subscription->forceFill([
                    'last_used_at' => now(),
                ])->save();

                return true;
            }

            $responseStatus = null;
            if (is_object($report) && method_exists($report, 'getResponse')) {
                $response = $report->getResponse();
                $responseStatus = is_object($response) && method_exists($response, 'getStatusCode')
                    ? (int) $response->getStatusCode()
                    : null;
            }

            $expiredByProvider = is_object($report) && method_exists($report, 'isSubscriptionExpired')
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
                'reason' => is_object($report) && method_exists($report, 'getReason')
                    ? (string) $report->getReason()
                    : 'unknown',
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

    private function bootstrapOpenSslEnvironment(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        $configuredConf = trim((string) config('services.webpush.openssl.conf', ''));
        $currentConf = trim((string) (getenv('OPENSSL_CONF') ?: ''));
        if ($currentConf === '' || ! is_file($currentConf)) {
            $confCandidate = $configuredConf !== '' ? $configuredConf : $this->detectWindowsOpenSslConf();
            if ($confCandidate !== '') {
                $this->setProcessEnv('OPENSSL_CONF', $confCandidate);
            }
        }

        $configuredRand = trim((string) config('services.webpush.openssl.rand_file', ''));
        $currentRand = trim((string) (getenv('RANDFILE') ?: ''));
        if ($currentRand === '') {
            $randPath = $configuredRand !== '' ? $configuredRand : storage_path('app/openssl/.rnd');
            $randDir = dirname($randPath);
            if (! is_dir($randDir)) {
                @mkdir($randDir, 0775, true);
            }
            if (! is_file($randPath)) {
                @touch($randPath);
            }
            $this->setProcessEnv('RANDFILE', $randPath);
        }
    }

    private function detectWindowsOpenSslConf(): string
    {
        $candidates = [];

        $phpBinaryDir = dirname((string) PHP_BINARY);
        if ($phpBinaryDir !== '' && $phpBinaryDir !== '.') {
            $candidates[] = $phpBinaryDir.DIRECTORY_SEPARATOR.'extras'.DIRECTORY_SEPARATOR.'ssl'.DIRECTORY_SEPARATOR.'openssl.cnf';
            $candidates[] = $phpBinaryDir.DIRECTORY_SEPARATOR.'openssl.cnf';
        }

        $loadedIni = php_ini_loaded_file();
        if (is_string($loadedIni) && $loadedIni !== '') {
            $iniDir = dirname($loadedIni);
            $candidates[] = $iniDir.DIRECTORY_SEPARATOR.'extras'.DIRECTORY_SEPARATOR.'ssl'.DIRECTORY_SEPARATOR.'openssl.cnf';
            $candidates[] = $iniDir.DIRECTORY_SEPARATOR.'openssl.cnf';
        }

        foreach ($candidates as $candidate) {
            if ($candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    private function setProcessEnv(string $key, string $value): void
    {
        if ($key === '' || $value === '') {
            return;
        }

        putenv($key.'='.$value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

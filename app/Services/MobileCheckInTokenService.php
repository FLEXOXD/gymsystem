<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MobileCheckInTokenService
{
    private const CACHE_PREFIX = 'mobile_checkin_token:';
    private const CURRENT_TOKEN_PREFIX = 'mobile_checkin_current:';
    private const CONSUMED_EVENT_PREFIX = 'mobile_checkin_consumed:';
    private const CONSUMED_MARKER_PREFIX = 'mobile_checkin_consumed_marker:';
    private const MAX_TTL_SECONDS = 2592000; // 30 days

    public function generate(int $gymId, int $issuedBy, int $ttlSeconds = 20): array
    {
        $ttl = max(10, min(self::MAX_TTL_SECONDS, $ttlSeconds));
        $token = strtoupper(Str::random(20));
        $issuedAtTs = now()->timestamp;
        $expiresAtTs = now()->addSeconds($ttl)->timestamp;

        $payload = [
            'token' => $token,
            'gym_id' => $gymId,
            'issued_by' => $issuedBy,
            'issued_at_ts' => $issuedAtTs,
            'expires_at_ts' => $expiresAtTs,
            'ttl_seconds' => $ttl,
        ];

        Cache::put($this->cacheKey($token), $payload, now()->addSeconds($ttl + 30));
        Cache::put($this->currentTokenKey($gymId), [
            'token' => $token,
            'expires_at_ts' => $expiresAtTs,
            'issued_at_ts' => $issuedAtTs,
        ], now()->addSeconds($ttl + 30));

        return $payload;
    }

    public function current(int $gymId): ?array
    {
        if ($gymId <= 0) {
            return null;
        }

        $current = Cache::get($this->currentTokenKey($gymId));
        if (! is_array($current)) {
            return null;
        }

        $token = strtoupper(trim((string) ($current['token'] ?? '')));
        if ($token === '') {
            Cache::forget($this->currentTokenKey($gymId));

            return null;
        }

        $payload = Cache::get($this->cacheKey($token));
        if (! is_array($payload)) {
            Cache::forget($this->currentTokenKey($gymId));

            return null;
        }

        $expiresAtTs = (int) ($payload['expires_at_ts'] ?? 0);
        if ($expiresAtTs <= 0 || now()->timestamp > $expiresAtTs) {
            Cache::forget($this->cacheKey($token));
            Cache::forget($this->currentTokenKey($gymId));
            Cache::forget($this->consumedMarkerKey($token));

            return null;
        }

        if (Cache::has($this->consumedMarkerKey($token))) {
            Cache::forget($this->cacheKey($token));
            Cache::forget($this->currentTokenKey($gymId));

            return null;
        }

        return $payload;
    }

    public function buildQrPayload(string $token): string
    {
        return 'GYMSYS-MOBILE|'.trim($token);
    }

    public function extractToken(string $raw): string
    {
        $value = trim($raw);
        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $query = parse_url($value, PHP_URL_QUERY);
            if (is_string($query) && $query !== '') {
                parse_str($query, $params);
                foreach (['token', 'code', 'value', 'qr'] as $key) {
                    $candidate = trim((string) ($params[$key] ?? ''));
                    if ($candidate !== '') {
                        return strtoupper($candidate);
                    }
                }
            }
        }

        if (str_contains($value, '|')) {
            $parts = array_values(array_filter(array_map(static fn ($p): string => trim((string) $p), explode('|', $value))));
            if ($parts !== []) {
                return strtoupper((string) end($parts));
            }
        }

        return strtoupper($value);
    }

    public function consume(string $token, int $expectedGymId): array
    {
        $resolved = strtoupper(trim($token));
        if ($resolved === '') {
            return ['ok' => false, 'reason' => 'token_empty', 'message' => 'Token de ingreso vacio.'];
        }

        $payload = Cache::get($this->cacheKey($resolved));
        if (! is_array($payload)) {
            return ['ok' => false, 'reason' => 'token_invalid', 'message' => 'QR invalido o ya utilizado.'];
        }

        if ((int) ($payload['gym_id'] ?? 0) !== $expectedGymId) {
            return ['ok' => false, 'reason' => 'token_gym_mismatch', 'message' => 'El QR no corresponde a este gimnasio.'];
        }

        $expiresAtTs = (int) ($payload['expires_at_ts'] ?? 0);
        if ($expiresAtTs <= 0 || now()->timestamp > $expiresAtTs) {
            Cache::forget($this->cacheKey($resolved));
            $current = Cache::get($this->currentTokenKey($expectedGymId));
            if (is_array($current) && strtoupper((string) ($current['token'] ?? '')) === $resolved) {
                Cache::forget($this->currentTokenKey($expectedGymId));
            }
            Cache::forget($this->consumedMarkerKey($resolved));

            return ['ok' => false, 'reason' => 'token_expired', 'message' => 'El QR ya expiro. Solicita uno nuevo.'];
        }

        $markerTtlSeconds = max(30, ($expiresAtTs - now()->timestamp) + 30);
        $consumedMarked = Cache::add(
            $this->consumedMarkerKey($resolved),
            ['consumed_at_ts' => now()->timestamp],
            now()->addSeconds($markerTtlSeconds)
        );

        if (! $consumedMarked) {
            return ['ok' => false, 'reason' => 'token_invalid', 'message' => 'QR invalido o ya utilizado.'];
        }

        Cache::forget($this->cacheKey($resolved));

        $gymId = (int) ($payload['gym_id'] ?? $expectedGymId);
        $consumedEvent = [
            'token' => $resolved,
            'gym_id' => $gymId,
            'consumed_at_ms' => (int) floor(microtime(true) * 1000),
        ];
        Cache::put($this->consumedEventKey($gymId), $consumedEvent, now()->addMinutes(10));

        $current = Cache::get($this->currentTokenKey($gymId));
        if (is_array($current) && strtoupper((string) ($current['token'] ?? '')) === $resolved) {
            Cache::forget($this->currentTokenKey($gymId));
        }

        return ['ok' => true, 'payload' => $payload];
    }

    public function latestConsumedEvent(int $gymId): ?array
    {
        $value = Cache::get($this->consumedEventKey($gymId));

        return is_array($value) ? $value : null;
    }

    private function cacheKey(string $token): string
    {
        return self::CACHE_PREFIX.$token;
    }

    private function currentTokenKey(int $gymId): string
    {
        return self::CURRENT_TOKEN_PREFIX.$gymId;
    }

    private function consumedEventKey(int $gymId): string
    {
        return self::CONSUMED_EVENT_PREFIX.$gymId;
    }

    private function consumedMarkerKey(string $token): string
    {
        return self::CONSUMED_MARKER_PREFIX.$token;
    }
}

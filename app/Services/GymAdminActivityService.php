<?php

namespace App\Services;

use App\Models\GymAdminActivityState;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GymAdminActivityService
{
    public const ONLINE_WINDOW_SECONDS = 300;

    public function touch(Request $request, ?User $user = null, array $context = []): ?GymAdminActivityState
    {
        $resolvedUser = $user ?? $request->user();
        if (! $resolvedUser instanceof User || $resolvedUser->gym_id === null || ! $resolvedUser->isOwner()) {
            return null;
        }

        static $supportsStateTable = null;
        if ($supportsStateTable === null) {
            $supportsStateTable = Schema::hasTable('gym_admin_activity_states');
        }

        if (! $supportsStateTable) {
            return null;
        }

        try {
            $now = $context['occurred_at'] ?? now('UTC');
            if (! $now instanceof CarbonInterface) {
                $now = Carbon::parse((string) $now, 'UTC');
            }

            $state = GymAdminActivityState::query()->firstOrNew([
                'gym_id' => (int) $resolvedUser->gym_id,
            ]);

            $state->forceFill([
                'user_id' => (int) $resolvedUser->id,
                'gym_name' => $this->trimToLength((string) ($resolvedUser->gym?->name ?? ''), 160),
                'user_name' => $this->trimToLength((string) ($resolvedUser->name ?? ''), 160),
                'user_email' => $this->trimToLength((string) ($resolvedUser->email ?? ''), 255),
                'last_activity_at' => $now,
                'last_activity_signal' => $this->normalizeSignal((string) ($context['signal'] ?? $request->input('signal', 'activity'))),
                'last_channel' => $this->resolveChannel(
                    $request,
                    array_key_exists('channel', $context) ? (string) $context['channel'] : null
                ),
                'last_route_name' => $this->trimToLength(
                    (string) ($context['route_name'] ?? ($request->route()?->getName() ?? '')),
                    120
                ),
                'last_path' => $this->trimToLength(
                    (string) ($context['path'] ?? $request->input('path', (string) $request->getRequestUri())),
                    255
                ),
                'last_ip_address' => $this->trimToLength((string) ($request->ip() ?? ''), 45),
                'last_user_agent' => $this->trimToLength((string) ($request->userAgent() ?? ''), 1024),
                'last_via_remember' => array_key_exists('via_remember', $context)
                    ? (bool) $context['via_remember']
                    : Auth::viaRemember(),
            ]);

            if (($context['mark_login'] ?? false) === true) {
                $state->last_login_at = $now;
            }

            $state->save();

            return $state;
        } catch (\Throwable $exception) {
            Log::warning('No se pudo actualizar gym_admin_activity_states.', [
                'user_id' => $resolvedUser->id,
                'gym_id' => $resolvedUser->gym_id,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function isOnline(?CarbonInterface $lastActivityAt): bool
    {
        if (! $lastActivityAt instanceof CarbonInterface) {
            return false;
        }

        return $lastActivityAt->greaterThanOrEqualTo(now('UTC')->subSeconds(self::ONLINE_WINDOW_SECONDS));
    }

    public function channelLabel(?string $channel): string
    {
        return $channel === 'app_instalada' ? 'App instalada' : 'Web';
    }

    private function resolveChannel(Request $request, ?string $explicitChannel = null): string
    {
        $normalizedExplicit = trim(mb_strtolower((string) $explicitChannel));
        if (in_array($normalizedExplicit, ['web', 'app_instalada'], true)) {
            return $normalizedExplicit;
        }

        $pwaMode = trim(mb_strtolower((string) (
            $request->input(
                'pwa_mode',
                $request->query(
                    'pwa_mode',
                    $request->cookie('gym_pwa_mode', (string) $request->header('X-PWA-Mode', ''))
                )
            )
        )));

        return $pwaMode === 'standalone' ? 'app_instalada' : 'web';
    }

    private function normalizeSignal(string $signal): string
    {
        $normalized = trim(mb_strtolower($signal));
        if ($normalized === '') {
            return 'activity';
        }

        $normalized = preg_replace('/[^a-z0-9]+/u', '_', $normalized) ?? 'activity';
        $normalized = trim($normalized, '_');

        return $this->trimToLength($normalized !== '' ? $normalized : 'activity', 40) ?? 'activity';
    }

    private function trimToLength(string $value, int $maxLength): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return mb_substr($trimmed, 0, $maxLength);
    }
}

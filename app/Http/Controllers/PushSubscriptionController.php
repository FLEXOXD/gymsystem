<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Models\User;
use App\Services\PushNotificationService;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PushSubscriptionController extends Controller
{
    public function status(Request $request, WebPushService $webPushService): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, 'No autorizado.');

        $activeCount = PushSubscription::query()
            ->active()
            ->where('user_id', (int) $user->id)
            ->count();

        return response()->json([
            'ok' => true,
            'active_subscriptions' => $activeCount,
            'webpush_ready' => $webPushService->isConfigured(),
            'has_vapid_public_key' => trim((string) config('services.webpush.vapid.public_key', '')) !== '',
        ]);
    }

    public function subscribe(Request $request, WebPushService $webPushService): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, 'No autorizado.');
        if ($this->isDemoUser($user)) {
            return response()->json([
                'ok' => false,
                'message' => 'Las notificaciones push no están disponibles en la cuenta demo.',
            ], 403);
        }

        $data = $request->validate([
            'subscription' => ['required', 'array'],
            'subscription.endpoint' => ['required', 'string', 'max:4096'],
            'subscription.keys' => ['required', 'array'],
            'subscription.keys.p256dh' => ['required', 'string', 'max:2048'],
            'subscription.keys.auth' => ['required', 'string', 'max:1024'],
            'encoding' => ['nullable', 'string', 'max:32'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $subscriptionData = (array) $data['subscription'];
        $endpoint = trim((string) ($subscriptionData['endpoint'] ?? ''));
        if ($endpoint === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Endpoint de push invalido.',
            ], 422);
        }

        $subscription = PushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => hash('sha256', $endpoint)],
            [
                'user_id' => (int) $user->id,
                'gym_id' => $user->gym_id !== null ? (int) $user->gym_id : null,
                'endpoint' => $endpoint,
                'public_key' => (string) (($subscriptionData['keys']['p256dh'] ?? '')),
                'auth_token' => (string) (($subscriptionData['keys']['auth'] ?? '')),
                'content_encoding' => trim((string) ($data['encoding'] ?? '')) !== ''
                    ? trim((string) $data['encoding'])
                    : 'aesgcm',
                'user_agent' => mb_substr((string) ($request->userAgent() ?? ''), 0, 1024),
                'device_name' => isset($data['device_name']) && trim((string) $data['device_name']) !== ''
                    ? trim((string) $data['device_name'])
                    : null,
                'revoked_at' => null,
            ]
        );

        return response()->json([
            'ok' => true,
            'subscription_id' => (int) $subscription->id,
            'active_subscriptions' => PushSubscription::query()
                ->active()
                ->where('user_id', (int) $user->id)
                ->count(),
            'webpush_ready' => $webPushService->isConfigured(),
            'message' => 'Notificaciones activadas para este dispositivo.',
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, 'No autorizado.');

        $data = $request->validate([
            'endpoint' => ['nullable', 'string', 'max:4096'],
        ]);

        $query = PushSubscription::query()
            ->active()
            ->where('user_id', (int) $user->id);

        $endpoint = trim((string) ($data['endpoint'] ?? ''));
        if ($endpoint !== '') {
            $query->where('endpoint_hash', hash('sha256', $endpoint));
        }

        $updated = $query->update([
            'revoked_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'revoked' => $updated,
            'active_subscriptions' => PushSubscription::query()
                ->active()
                ->where('user_id', (int) $user->id)
                ->count(),
            'message' => $updated > 0
                ? 'Notificaciones desactivadas para este dispositivo.'
                : 'No se encontró una suscripción activa para revocar.',
        ]);
    }

    public function test(Request $request, PushNotificationService $pushNotificationService): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, 'No autorizado.');
        if ($this->isDemoUser($user)) {
            return response()->json([
                'ok' => false,
                'message' => 'Las notificaciones push no están disponibles en la cuenta demo.',
            ], 403);
        }

        $activeSubscriptions = PushSubscription::query()
            ->active()
            ->where('user_id', (int) $user->id)
            ->count();

        if ($activeSubscriptions <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'No hay dispositivos suscritos para este usuario.',
            ], 422);
        }

        $payload = [
            'title' => 'GymSystem',
            'body' => 'Prueba de notificacion push activada correctamente.',
            'tag' => 'gymsystem-test-user-'.(int) $user->id,
            'url' => $this->resolveDefaultUrl($user),
            'data' => [
                'kind' => 'push_test',
                'sent_at' => now()->toIso8601String(),
            ],
        ];

        $result = $pushNotificationService->sendToUser($user, $payload);
        if ((int) ($result['sent'] ?? 0) <= 0) {
            return response()->json([
                'ok' => false,
                'result' => $result,
                'message' => 'No se pudo entregar la notificacion de prueba. Verifica llaves VAPID y worker de cola.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'result' => $result,
            'message' => 'Prueba de notificacion enviada.',
        ]);
    }

    private function resolveDefaultUrl($user): string
    {
        $gymSlug = trim((string) ($user?->gym?->slug ?? ''));
        if ($gymSlug !== '' && Route::has('panel.index')) {
            return route('panel.index', [
                'contextGym' => $gymSlug,
                'pwa_mode' => 'standalone',
            ]);
        }

        if (Route::has('superadmin.dashboard')) {
            return route('superadmin.dashboard');
        }

        return route('app.entry');
    }

    private function isDemoUser(User $user): bool
    {
        return $user->demoSession()->active()->exists();
    }
}

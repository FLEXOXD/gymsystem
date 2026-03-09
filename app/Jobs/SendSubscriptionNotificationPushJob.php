<?php

namespace App\Jobs;

use App\Models\SubscriptionNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Route;

class SendSubscriptionNotificationPushJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        private readonly int $notificationId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(PushNotificationService $pushNotificationService): void
    {
        $notification = SubscriptionNotification::query()
            ->with([
                'gym:id,name,slug',
                'subscription:id,gym_id,plan_name,ends_at',
            ])
            ->find($this->notificationId);

        if (! $notification || (int) ($notification->gym_id ?? 0) <= 0) {
            return;
        }

        $gymSlug = trim((string) ($notification->gym?->slug ?? ''));
        $targetUrl = '/app';
        if ($gymSlug !== '' && Route::has('panel.index')) {
            $targetUrl = route('panel.index', [
                'contextGym' => $gymSlug,
                'pwa_mode' => 'standalone',
            ]);
        } elseif (Route::has('app.entry')) {
            $targetUrl = route('app.entry');
        }

        $title = 'Suscripción GymSystem';
        $planName = trim((string) ($notification->subscription?->plan_name ?? ''));
        if ($planName !== '') {
            $title .= ' - '.$planName;
        }

        $pushNotificationService->sendToGymUsers((int) $notification->gym_id, [
            'title' => $title,
            'body' => (string) ($notification->message_snapshot ?? 'Tienes un recordatorio de suscripción pendiente.'),
            'tag' => 'subscription-notification-'.(int) $notification->id,
            'url' => $targetUrl,
            'data' => [
                'kind' => 'subscription_notification',
                'subscription_notification_id' => (int) $notification->id,
                'type' => (string) ($notification->type ?? ''),
                'scheduled_for' => $notification->scheduled_for?->toDateString(),
            ],
            'renotify' => true,
        ], ['owner']);
    }
}

<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\SubscriptionNotification;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SubscriptionNotificationService
{
    public const TYPES = [
        'expires_7',
        'expires_3',
        'expires_1',
        'grace_1',
        'grace_2',
        'grace_3',
    ];

    public function __construct(
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Build final message snapshot for a notification.
     */
    public function buildMessage(string $type, Gym $gym, Subscription $subscription): string
    {
        $endsAt = Carbon::parse($subscription->ends_at)->toDateString();
        $plan = $subscription->plan_name;
        $price = Currency::format((float) $subscription->price, $gym->currency_code);

        return match ($type) {
            'expires_7' => "Hola {$gym->name}, su plan {$plan} vence en 7 dias ({$endsAt}). Monto: {$price}. Favor renovar a tiempo.",
            'expires_3' => "Hola {$gym->name}, recordatorio: su plan {$plan} vence en 3 dias ({$endsAt}). Monto: {$price}.",
            'expires_1' => "Hola {$gym->name}, su suscripcion {$plan} vence manana ({$endsAt}). Evite suspension renovando hoy.",
            'grace_1' => "Hola {$gym->name}, su suscripcion vencio y hoy es dia 1 de gracia. Renueve para evitar suspension.",
            'grace_2' => "Hola {$gym->name}, hoy es dia 2 de gracia. Renueve hoy para mantener el acceso.",
            'grace_3' => "Hola {$gym->name}, ultimo dia de gracia. Si no renueva hoy, su cuenta sera suspendida.",
            default => "Recordatorio de suscripcion para {$gym->name}.",
        };
    }

    /**
     * Generate pending notifications due for a date.
     */
    public function generateDueNotifications(null|string|Carbon $date = null): int
    {
        $targetDate = $this->normalizeDate($date);
        $created = 0;

        Subscription::query()
            ->with('gym')
            ->orderBy('id')
            ->chunkById(200, function (Collection $subscriptions) use ($targetDate, &$created): void {
                foreach ($subscriptions as $subscription) {
                    $sync = $this->subscriptionService->checkStatus((int) $subscription->gym_id);
                    if (! $sync || $sync->status === 'suspended') {
                        continue;
                    }

                    $gym = $subscription->gym;
                    if (! $gym) {
                        continue;
                    }

                    foreach ($this->typesDueForDate($targetDate, $sync) as $type) {
                        $message = $this->buildMessage($type, $gym, $sync);

                        $notification = SubscriptionNotification::query()->firstOrCreate(
                            [
                                'gym_id' => $gym->id,
                                'subscription_id' => $sync->id,
                                'type' => $type,
                                'scheduled_for' => $targetDate->toDateString(),
                            ],
                            [
                                'channel' => 'internal',
                                'status' => 'pending',
                                'message_snapshot' => $message,
                            ]
                        );

                        if ($notification->wasRecentlyCreated) {
                            $created++;
                        }
                    }
                }
            });

        return $created;
    }

    /**
     * Mark notification as sent.
     */
    public function markAsSent(int $notificationId, int $userId): SubscriptionNotification
    {
        $notification = SubscriptionNotification::query()->findOrFail($notificationId);

        if ($notification->status !== 'sent') {
            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'created_by' => $userId,
            ]);
        }

        return $notification->fresh();
    }

    /**
     * Mark notification as skipped.
     */
    public function markAsSkipped(int $notificationId, int $userId): SubscriptionNotification
    {
        $notification = SubscriptionNotification::query()->findOrFail($notificationId);

        if ($notification->status !== 'skipped') {
            $notification->update([
                'status' => 'skipped',
                'created_by' => $userId,
            ]);
        }

        return $notification->fresh();
    }

    /**
     * Resolve notification types that are due on a date for a subscription.
     *
     * @return list<string>
     */
    private function typesDueForDate(Carbon $date, Subscription $subscription): array
    {
        $endsAt = Carbon::parse($subscription->ends_at)->startOfDay();
        $types = [];

        if ($endsAt->equalTo($date->copy()->addDays(7))) {
            $types[] = 'expires_7';
        }
        if ($endsAt->equalTo($date->copy()->addDays(3))) {
            $types[] = 'expires_3';
        }
        if ($endsAt->equalTo($date->copy()->addDays(1))) {
            $types[] = 'expires_1';
        }
        if ($date->equalTo($endsAt->copy()->addDay())) {
            $types[] = 'grace_1';
        }
        if ($date->equalTo($endsAt->copy()->addDays(2))) {
            $types[] = 'grace_2';
        }
        if ($date->equalTo($endsAt->copy()->addDays(3))) {
            $types[] = 'grace_3';
        }

        return $types;
    }

    private function normalizeDate(null|string|Carbon $date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date->copy()->startOfDay();
        }

        if (is_string($date) && $date !== '') {
            return Carbon::parse($date)->startOfDay();
        }

        return Carbon::today();
    }
}

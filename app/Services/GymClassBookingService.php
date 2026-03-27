<?php

namespace App\Services;

use App\Models\Client;
use App\Models\GymClass;
use App\Models\GymClassReservation;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;

class GymClassBookingService
{
    public function __construct(
        private readonly ClientPushNotificationService $clientPushNotificationService
    ) {
    }

    /**
     * @return array{ok:bool,status?:string,message:string,promoted_client_id?:int|null}
     */
    public function reserveForClient(GymClass $gymClass, Client $client): array
    {
        $notificationPayloads = [];
        $result = DB::transaction(function () use ($gymClass, $client, &$notificationPayloads): array {
            $lockedClass = GymClass::query()
                ->with('gym:id,name,slug')
                ->lockForUpdate()
                ->findOrFail((int) $gymClass->id);

            Client::query()
                ->whereKey((int) $client->id)
                ->lockForUpdate()
                ->first();

            if ((int) $lockedClass->gym_id !== (int) $client->gym_id) {
                return [
                    'ok' => false,
                    'message' => 'La clase no pertenece a tu gimnasio.',
                ];
            }

            if ((string) $lockedClass->status !== GymClass::STATUS_SCHEDULED) {
                return [
                    'ok' => false,
                    'message' => 'Esta clase no está disponible para reservar.',
                ];
            }

            if ($lockedClass->ends_at !== null && $lockedClass->ends_at->lte(now())) {
                return [
                    'ok' => false,
                    'message' => 'La clase ya finalizó.',
                ];
            }

            if (! $this->clientHasActiveMembership($lockedClass, $client)) {
                return [
                    'ok' => false,
                    'message' => 'Necesitas una membresía activa para reservar esta clase.',
                ];
            }

            $reservation = GymClassReservation::query()
                ->where('gym_class_id', (int) $lockedClass->id)
                ->where('client_id', (int) $client->id)
                ->lockForUpdate()
                ->first();

            if ($reservation && in_array((string) $reservation->status, [
                GymClassReservation::STATUS_RESERVED,
                GymClassReservation::STATUS_ATTENDED,
            ], true)) {
                return [
                    'ok' => true,
                    'status' => (string) $reservation->status,
                    'message' => 'Ya tienes una reserva activa para esta clase.',
                ];
            }

            $overlappingReservation = $this->findOverlappingReservationForClientId($lockedClass, (int) $client->id);
            if ($overlappingReservation) {
                return [
                    'ok' => false,
                    'message' => $this->buildScheduleConflictMessage($overlappingReservation),
                ];
            }

            $hasCapacity = $this->occupiedSeats($lockedClass) < max(1, (int) $lockedClass->capacity);
            $targetStatus = $hasCapacity
                ? GymClassReservation::STATUS_RESERVED
                : ($lockedClass->allow_waitlist ? GymClassReservation::STATUS_WAITLIST : null);

            if ($targetStatus === null) {
                return [
                    'ok' => false,
                    'message' => 'La clase ya no tiene cupos disponibles.',
                ];
            }

            $payload = [
                'gym_id' => (int) $lockedClass->gym_id,
                'gym_class_id' => (int) $lockedClass->id,
                'client_id' => (int) $client->id,
                'status' => $targetStatus,
                'reserved_at' => $targetStatus === GymClassReservation::STATUS_RESERVED ? now() : null,
                'waitlisted_at' => $targetStatus === GymClassReservation::STATUS_WAITLIST ? now() : null,
                'promoted_at' => null,
                'cancelled_at' => null,
                'attended_at' => null,
            ];

            if ($reservation) {
                $reservation->update($payload);
            } else {
                $reservation = GymClassReservation::query()->create($payload);
            }

            $notificationPayloads[] = [
                'gym_id' => (int) $lockedClass->gym_id,
                'client_id' => (int) $client->id,
                'payload' => $this->buildClientPayload(
                    gymClass: $lockedClass,
                    title: $targetStatus === GymClassReservation::STATUS_RESERVED ? 'Reserva confirmada' : 'Lista de espera',
                    body: $targetStatus === GymClassReservation::STATUS_RESERVED
                        ? 'Tu cupo para '.$lockedClass->name.' ya quedó confirmado.'
                        : 'Te agregamos a la lista de espera de '.$lockedClass->name.'.'
                ),
            ];

            return [
                'ok' => true,
                'status' => $targetStatus,
                'message' => $targetStatus === GymClassReservation::STATUS_RESERVED
                    ? 'Reserva confirmada correctamente.'
                    : 'Te agregamos a la lista de espera.',
            ];
        });

        $this->dispatchClientNotifications($notificationPayloads);

        return $result;
    }

    /**
     * @return array{ok:bool,message:string,promoted_client_id?:int|null}
     */
    public function cancelForClient(GymClass $gymClass, Client $client): array
    {
        return $this->cancelReservation(
            gymClassId: (int) $gymClass->id,
            clientId: (int) $client->id,
            actorLabel: 'Tu reserva fue cancelada.',
            isAdminAction: false
        );
    }

    /**
     * @return array{ok:bool,message:string,promoted_client_id?:int|null}
     */
    public function cancelReservationByAdmin(GymClassReservation $reservation): array
    {
        return $this->cancelReservation(
            gymClassId: (int) $reservation->gym_class_id,
            clientId: (int) $reservation->client_id,
            actorLabel: 'El gimnasio canceló tu reserva.',
            isAdminAction: true
        );
    }

    /**
     * @return array{ok:bool,message:string}
     */
    public function markAttendance(GymClassReservation $reservation): array
    {
        $updated = GymClassReservation::query()
            ->whereKey((int) $reservation->id)
            ->where('status', GymClassReservation::STATUS_RESERVED)
            ->update([
                'status' => GymClassReservation::STATUS_ATTENDED,
                'attended_at' => now(),
                'cancelled_at' => null,
            ]);

        if ($updated === 0) {
            return [
                'ok' => false,
                'message' => 'Solo se puede marcar asistencia sobre una reserva confirmada.',
            ];
        }

        return [
            'ok' => true,
            'message' => 'Asistencia marcada correctamente.',
        ];
    }

    /**
     * @return array{sent:int,failed:int,skipped:int}
     */
    public function notifyParticipants(GymClass $gymClass, string $message): array
    {
        $classModel = GymClass::query()
            ->with([
                'gym:id,name,slug',
                'reservations' => static function ($query): void {
                    $query->whereIn('status', [
                        GymClassReservation::STATUS_RESERVED,
                        GymClassReservation::STATUS_WAITLIST,
                    ])->select(['id', 'gym_id', 'gym_class_id', 'client_id', 'status']);
                },
            ])
            ->findOrFail((int) $gymClass->id);

        $summary = ['sent' => 0, 'failed' => 0, 'skipped' => 0];
        foreach ($classModel->reservations as $reservation) {
            $result = $this->clientPushNotificationService->sendToClient(
                (int) $classModel->gym_id,
                (int) $reservation->client_id,
                $this->buildClientPayload(
                    gymClass: $classModel,
                    title: 'Aviso de clase',
                    body: $message
                )
            );

            $summary['sent'] += (int) ($result['sent'] ?? 0);
            $summary['failed'] += (int) ($result['failed'] ?? 0);
            $summary['skipped'] += (int) ($result['skipped'] ?? 0);
        }

        return $summary;
    }

    /**
     * @return array{ok:bool,message:string,promoted_client_id?:int|null}
     */
    private function cancelReservation(int $gymClassId, int $clientId, string $actorLabel, bool $isAdminAction): array
    {
        $notificationPayloads = [];
        $promotedClientId = null;

        $result = DB::transaction(function () use ($gymClassId, $clientId, $actorLabel, $isAdminAction, &$notificationPayloads, &$promotedClientId): array {
            $gymClass = GymClass::query()
                ->with('gym:id,name,slug')
                ->lockForUpdate()
                ->findOrFail($gymClassId);

            $reservation = GymClassReservation::query()
                ->where('gym_class_id', $gymClassId)
                ->where('client_id', $clientId)
                ->lockForUpdate()
                ->first();

            if (! $reservation || (string) $reservation->status === GymClassReservation::STATUS_CANCELLED) {
                return [
                    'ok' => false,
                    'message' => 'No encontramos una reserva activa para cancelar.',
                ];
            }

            if ((string) $reservation->status === GymClassReservation::STATUS_ATTENDED) {
                return [
                    'ok' => false,
                    'message' => 'La asistencia ya fue marcada y no se puede cancelar.',
                ];
            }

            $releasedSeat = (string) $reservation->status === GymClassReservation::STATUS_RESERVED;

            $reservation->update([
                'status' => GymClassReservation::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            $notificationPayloads[] = [
                'gym_id' => (int) $gymClass->gym_id,
                'client_id' => $clientId,
                'payload' => $this->buildClientPayload(
                    gymClass: $gymClass,
                    title: $isAdminAction ? 'Reserva cancelada por el gym' : 'Reserva cancelada',
                    body: $actorLabel
                ),
            ];

            if ($releasedSeat) {
                $promotedReservation = $this->promoteWaitlistReservation($gymClass);
                if ($promotedReservation) {
                    $promotedClientId = (int) $promotedReservation->client_id;
                    $notificationPayloads[] = [
                        'gym_id' => (int) $gymClass->gym_id,
                        'client_id' => (int) $promotedReservation->client_id,
                        'payload' => $this->buildClientPayload(
                            gymClass: $gymClass,
                            title: 'Cupo liberado',
                            body: 'Ya tienes un cupo confirmado para '.$gymClass->name.'.'
                        ),
                    ];
                }
            }

            return [
                'ok' => true,
                'message' => 'Reserva cancelada correctamente.',
                'promoted_client_id' => $promotedClientId,
            ];
        });

        $this->dispatchClientNotifications($notificationPayloads);

        return $result;
    }

    private function promoteWaitlistReservation(GymClass $gymClass): ?GymClassReservation
    {
        if ($this->occupiedSeats($gymClass) >= max(1, (int) $gymClass->capacity)) {
            return null;
        }

        $waitlistReservations = GymClassReservation::query()
            ->where('gym_class_id', (int) $gymClass->id)
            ->where('status', GymClassReservation::STATUS_WAITLIST)
            ->orderBy('waitlisted_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($waitlistReservations as $reservation) {
            Client::query()
                ->whereKey((int) $reservation->client_id)
                ->lockForUpdate()
                ->first();

            $hasConflict = $this->findOverlappingReservationForClientId($gymClass, (int) $reservation->client_id) !== null;
            if ($hasConflict) {
                continue;
            }

            $reservation->update([
                'status' => GymClassReservation::STATUS_RESERVED,
                'reserved_at' => now(),
                'promoted_at' => now(),
                'cancelled_at' => null,
            ]);

            return $reservation->fresh();
        }

        return null;
    }

    private function occupiedSeats(GymClass $gymClass): int
    {
        return GymClassReservation::query()
            ->where('gym_class_id', (int) $gymClass->id)
            ->whereIn('status', [
                GymClassReservation::STATUS_RESERVED,
                GymClassReservation::STATUS_ATTENDED,
            ])
            ->count();
    }

    private function clientHasActiveMembership(GymClass $gymClass, Client $client): bool
    {
        $sessionDate = $gymClass->starts_at !== null
            ? $gymClass->starts_at->toDateString()
            : now()->toDateString();

        return Membership::query()
            ->forGym((int) $gymClass->gym_id)
            ->where('client_id', (int) $client->id)
            ->activeOn($sessionDate)
            ->exists();
    }

    private function findOverlappingReservationForClientId(GymClass $gymClass, int $clientId): ?GymClassReservation
    {
        if ($gymClass->starts_at === null || $gymClass->ends_at === null) {
            return null;
        }

        return GymClassReservation::query()
            ->where('gym_id', (int) $gymClass->gym_id)
            ->where('client_id', $clientId)
            ->whereIn('status', [
                GymClassReservation::STATUS_RESERVED,
                GymClassReservation::STATUS_WAITLIST,
            ])
            ->where('gym_class_id', '!=', (int) $gymClass->id)
            ->whereHas('gymClass', function ($query) use ($gymClass): void {
                $query->where('status', GymClass::STATUS_SCHEDULED)
                    ->whereDate('starts_at', '<=', $gymClass->ends_at->toDateString())
                    ->whereDate('ends_at', '>=', $gymClass->starts_at->toDateString());
            })
            ->with([
                'gymClass:id,gym_id,name,active_weekdays,starts_at,ends_at,status',
            ])
            ->orderByRaw(
                "CASE status
                    WHEN '".GymClassReservation::STATUS_RESERVED."' THEN 0
                    WHEN '".GymClassReservation::STATUS_WAITLIST."' THEN 1
                    ELSE 2
                 END"
            )
            ->orderBy('reserved_at')
            ->orderBy('waitlisted_at')
            ->get()
            ->first(static function (GymClassReservation $reservation) use ($gymClass): bool {
                return $reservation->gymClass instanceof GymClass
                    && $reservation->gymClass->overlapsSchedule($gymClass);
            });
    }

    private function buildScheduleConflictMessage(GymClassReservation $reservation): string
    {
        $reservationClass = $reservation->gymClass;
        $className = trim((string) ($reservationClass?->name ?? 'otra clase'));
        $statusLabel = (string) $reservation->status === GymClassReservation::STATUS_WAITLIST
            ? 'una reserva en lista de espera'
            : 'una reserva confirmada';
        $scheduleLabel = $this->buildScheduleSummary($reservationClass);

        return 'Ya tienes '.$statusLabel.' para '.$className.' '.$scheduleLabel.'. Esta nueva clase coincide en ese horario, asi que debes elegir otro turno o cancelar la reserva anterior.';
    }

    private function buildScheduleSummary(?GymClass $gymClass): string
    {
        if (! $gymClass?->starts_at || ! $gymClass->ends_at) {
            return 'en otro horario';
        }

        $startDateLabel = $gymClass->starts_at->format('d/m');
        $endDateLabel = $gymClass->ends_at->format('d/m');
        $startTimeLabel = $gymClass->starts_at->format('H:i');
        $endTimeLabel = $gymClass->ends_at->format('H:i');

        if ($gymClass->starts_at->isSameDay($gymClass->ends_at)) {
            return 'el '.$startDateLabel.' de '.$startTimeLabel.' a '.$endTimeLabel;
        }

        $weekdaySummary = $gymClass->usesAllWeekdays()
            ? ''
            : ' ('.$gymClass->activeWeekdaysLabel().')';

        return 'del '.$startDateLabel.' al '.$endDateLabel.$weekdaySummary.', de '.$startTimeLabel.' a '.$endTimeLabel;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildClientPayload(GymClass $gymClass, string $title, string $body): array
    {
        $gymSlug = trim((string) ($gymClass->gym?->slug ?? ''));
        $url = $gymSlug !== ''
            ? route('client-mobile.app', ['gymSlug' => $gymSlug, 'screen' => 'classes'])
            : '/';

        return [
            'title' => $title,
            'body' => $body,
            'tag' => 'gym-class-'.$gymClass->id,
            'url' => $url,
            'data' => [
                'kind' => 'gym_class',
                'class_id' => (int) $gymClass->id,
            ],
        ];
    }

    /**
     * @param  array<int, array{gym_id:int,client_id:int,payload:array<string,mixed>}>  $notificationPayloads
     */
    private function dispatchClientNotifications(array $notificationPayloads): void
    {
        foreach ($notificationPayloads as $notification) {
            $this->clientPushNotificationService->sendToClient(
                (int) ($notification['gym_id'] ?? 0),
                (int) ($notification['client_id'] ?? 0),
                (array) ($notification['payload'] ?? [])
            );
        }
    }
}

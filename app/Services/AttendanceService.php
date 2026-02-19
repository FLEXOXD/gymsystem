<?php

namespace App\Services;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceCheckinService $checkinService
    ) {
    }

    /**
     * Backward-compatible wrapper for the optimized check-in service.
     *
     * @return array{
     *     ok: bool,
     *     message: string,
     *     method: 'rfid'|'qr'|'document'|null,
     *     client: array{id:int,full_name:string,photo_url:?string,membership_ends_at:?string,month_visits:?int,gender:'male'|'female'|'neutral'}|null,
     *     attendance: array{id:int,date:string,time:string}|null,
     *     attempt: array{date:string,time:string}
     * }
     */
    public function checkInByValue(int $gymId, int $userId, string $value): array
    {
        return $this->checkinService->checkInByValue($gymId, $userId, $value);
    }
}

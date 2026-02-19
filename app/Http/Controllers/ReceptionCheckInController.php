<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Gym;
use App\Models\Membership;
use App\Http\Requests\ReceptionCheckInRequest;
use App\Services\AttendanceCheckinService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DateTimeInterface;
use Throwable;

class ReceptionCheckInController extends Controller
{
    /**
     * Show reception mode page.
     */
    public function index(Request $request): View
    {
        $gymId = (int) ($request->user()?->gym_id ?? 0);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');
        $gym = $request->user()?->gym;
        $gymName = trim((string) ($gym?->name ?? 'Gym'));

        $recentAttendances = $this->recentAttendancesForGym($gymId);

        return view('reception.index', [
            'recentAttendances' => $recentAttendances,
            'syncGymId' => $gymId,
            'syncGymName' => $gymName !== '' ? $gymName : 'Gym',
            'gymAvatarUrls' => $this->gymAvatarUrls($gym),
        ]);
    }

    /**
     * Show monitor display screen with only result and recent entries.
     */
    public function display(Request $request): View
    {
        $gymId = (int) ($request->user()?->gym_id ?? 0);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');
        $gym = $request->user()?->gym;
        $gymName = trim((string) ($gym?->name ?? 'Gym'));

        $recentAttendances = $this->recentAttendancesForGym($gymId);

        return view('reception.display', [
            'recentAttendances' => $recentAttendances,
            'latestResult' => $this->latestResultForGym($gymId),
            'syncGymId' => $gymId,
            'syncGymName' => $gymName !== '' ? $gymName : 'Gym',
            'gymAvatarUrls' => $this->gymAvatarUrls($gym),
        ]);
    }

    /**
     * Unified endpoint for RFID/QR/document check-in.
     */
    public function store(
        ReceptionCheckInRequest $request,
        AttendanceCheckinService $attendanceService
    ): JsonResponse {
        $userId = (int) $request->user()->id;
        $gymId = $request->user()?->gym_id;

        if (! $gymId) {
            $payload = [
                'ok' => false,
                'reason' => 'missing_gym',
                'message' => 'El usuario autenticado no tiene gym_id asignado.',
                'method' => null,
                'client' => null,
                'attendance' => null,
                'attempt' => [
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ],
            ];

            Log::warning('reception.check_in.failed', [
                'gym_id' => null,
                'user_id' => $userId,
                'method' => null,
                'reason' => 'missing_gym_id',
            ]);

            return response()->json($payload, 422);
        }

        try {
            $result = $attendanceService->checkInByValue(
                (int) $gymId,
                $userId,
                $request->validated('value')
            );
        } catch (Throwable $exception) {
            report($exception);

            $payload = [
                'ok' => false,
                'reason' => 'internal_error',
                'message' => 'No se pudo registrar el ingreso por un error interno.',
                'method' => null,
                'client' => null,
                'attendance' => null,
                'attempt' => [
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ],
            ];

            Log::warning('reception.check_in.failed', [
                'gym_id' => (int) $gymId,
                'user_id' => $userId,
                'method' => null,
                'reason' => 'internal_exception',
            ]);

            return response()->json($payload, 500);
        }

        if ($result['ok']) {
            Log::info('reception.check_in.success', [
                'gym_id' => (int) $gymId,
                'user_id' => $userId,
                'method' => $result['method'],
            ]);
        } else {
            Log::warning('reception.check_in.failed', [
                'gym_id' => (int) $gymId,
                'user_id' => $userId,
                'method' => $result['method'],
                'reason' => $result['message'],
            ]);
        }

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    private function recentAttendancesForGym(int $gymId)
    {
        return Attendance::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'client_id', 'credential_id', 'date', 'time'])
            ->with(['client:id,first_name,last_name,photo_path', 'credential:id,type'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    }

    /**
     * @return array{
     *     ok: bool,
     *     reason: string,
     *     message: string,
     *     method: 'rfid'|'qr'|'document'|null,
     *     client: array{
     *         id:int,
     *         full_name:string,
     *         photo_url:?string,
     *         membership_ends_at:?string,
     *         month_visits:?int,
     *         total_visits:int,
     *         last_attendance_date:?string,
     *         last_attendance_time:?string,
     *         gender:'male'|'female'|'neutral'
     *     }|null,
     *     attendance: array{id:int,date:string,time:string}|null,
     *     attempt: array{date:string,time:string}
     * }|null
     */
    private function latestResultForGym(int $gymId): ?array
    {
        $latest = Attendance::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'client_id', 'credential_id', 'date', 'time'])
            ->with(['client:id,first_name,last_name,photo_path,gender', 'credential:id,type'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            return null;
        }

        $method = $latest->credential?->type ? (string) $latest->credential->type : 'document';
        $membershipEndsAt = Membership::query()
            ->forGym($gymId)
            ->where('client_id', (int) $latest->client_id)
            ->orderByDesc('ends_at')
            ->value('ends_at');
        $attendanceDate = (string) ($latest->date?->toDateString() ?? now()->toDateString());

        return [
            'ok' => true,
            'reason' => 'success',
            'message' => 'Check-in registrado correctamente.',
            'method' => $method,
            'client' => [
                'id' => (int) $latest->client_id,
                'full_name' => (string) ($latest->client?->full_name ?? '-'),
                'photo_url' => $this->resolvePhotoUrl($latest->client?->photo_path),
                'membership_ends_at' => $this->normalizeMembershipEndsAt($membershipEndsAt),
                'month_visits' => $this->countMonthVisits((int) $latest->gym_id, (int) $latest->client_id, $attendanceDate),
                'total_visits' => Attendance::query()
                    ->forGym((int) $latest->gym_id)
                    ->where('client_id', (int) $latest->client_id)
                    ->count(),
                'last_attendance_date' => $attendanceDate,
                'last_attendance_time' => (string) ($latest->time ?? ''),
                'gender' => $this->normalizeGender($latest->client?->gender),
            ],
            'attendance' => [
                'id' => (int) $latest->id,
                'date' => $attendanceDate,
                'time' => (string) ($latest->time ?? ''),
            ],
            'attempt' => [
                'date' => $attendanceDate,
                'time' => (string) ($latest->time ?? ''),
            ],
        ];
    }

    private function resolvePhotoUrl(?string $photoPath): ?string
    {
        if (! $photoPath) {
            return null;
        }

        $path = trim($photoPath);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['storage/', '/storage/'])) {
            return url('/'.ltrim($path, '/'));
        }

        return Storage::url($path);
    }

    /**
     * @return array{male:?string,female:?string,neutral:?string}
     */
    private function gymAvatarUrls(?Gym $gym): array
    {
        return [
            'male' => $this->resolvePhotoUrl($gym?->avatar_male_path),
            'female' => $this->resolvePhotoUrl($gym?->avatar_female_path),
            'neutral' => $this->resolvePhotoUrl($gym?->avatar_neutral_path),
        ];
    }

    private function normalizeMembershipEndsAt(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        return null;
    }

    /**
     * @return 'male'|'female'|'neutral'
     */
    private function normalizeGender(mixed $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['male', 'female'], true) ? $normalized : 'neutral';
    }

    private function countMonthVisits(int $gymId, int $clientId, string $referenceDate): int
    {
        $baseDate = \Carbon\Carbon::parse($referenceDate);

        return Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->whereBetween('date', [
                $baseDate->copy()->startOfMonth()->toDateString(),
                $baseDate->copy()->endOfMonth()->toDateString(),
            ])
            ->count();
    }
}

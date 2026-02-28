<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Http\Requests\ReceptionCheckInRequest;
use App\Services\AttendanceCheckinService;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        $gymId = ActiveGymContext::id($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');
        $gym = $request->attributes->get('active_gym');
        if (! $gym instanceof Gym) {
            $gym = $request->user()?->gym;
        }
        $gymName = trim((string) ($gym?->name ?? 'Gym'));

        if ($isGlobalScope) {
            $hubGymId = (int) ($request->attributes->get('hub_gym_id') ?? $request->user()?->gym_id ?? 0);
            $scopeBranchCount = collect($gymIds)
                ->map(static fn ($id): int => (int) $id)
                ->filter(static fn (int $id): bool => $id > 0 && $id !== $hubGymId)
                ->unique()
                ->count();

            $recentAttendances = Attendance::query()
                ->forGyms($gymIds)
                ->select(['id', 'gym_id', 'client_id', 'credential_id', 'date', 'time'])
                ->with(['gym:id,name,slug', 'client:id,first_name,last_name,photo_path', 'credential:id,type'])
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('id')
                ->limit(20)
                ->get();

            $attendanceHistoryStart = now()->subMonthsNoOverflow(2)->toDateString();
            $attendanceHistoryBaseQuery = Attendance::query()
                ->forGyms($gymIds)
                ->whereDate('date', '>=', $attendanceHistoryStart);
            $attendanceHistoryTotal = (clone $attendanceHistoryBaseQuery)->count();
            $attendanceHistoryLimit = 3000;
            $attendanceHistory = (clone $attendanceHistoryBaseQuery)
                ->select(['id', 'gym_id', 'client_id', 'credential_id', 'date', 'time', 'created_by'])
                ->with(['gym:id,name,slug', 'client:id,first_name,last_name', 'credential:id,type', 'createdBy:id,name'])
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->orderByDesc('id')
                ->limit($attendanceHistoryLimit)
                ->get();

            return view('reception.global', [
                'recentAttendances' => $recentAttendances,
                'attendanceHistory' => $attendanceHistory,
                'attendanceHistoryStart' => $attendanceHistoryStart,
                'attendanceHistoryTotal' => $attendanceHistoryTotal,
                'attendanceHistoryTruncated' => $attendanceHistoryTotal > $attendanceHistoryLimit,
                'scopeGymIds' => $gymIds,
                'scopeGymCount' => count($gymIds),
                'scopeBranchCount' => $scopeBranchCount,
                'scopeGymName' => $gymName !== '' ? $gymName : 'Gym',
            ]);
        }

        $recentAttendances = $this->recentAttendancesForGym($gymId);
        $attendanceHistoryStart = now()->subMonthsNoOverflow(2)->toDateString();
        $attendanceHistoryBaseQuery = Attendance::query()
            ->forGym($gymId)
            ->whereDate('date', '>=', $attendanceHistoryStart);
        $attendanceHistoryTotal = (clone $attendanceHistoryBaseQuery)->count();
        $attendanceHistoryLimit = 2000;
        $attendanceHistory = (clone $attendanceHistoryBaseQuery)
            ->select(['id', 'gym_id', 'client_id', 'credential_id', 'date', 'time', 'created_by'])
            ->with(['client:id,first_name,last_name', 'credential:id,type', 'createdBy:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->limit($attendanceHistoryLimit)
            ->get();

        return view('reception.index', [
            'recentAttendances' => $recentAttendances,
            'attendanceHistory' => $attendanceHistory,
            'attendanceHistoryStart' => $attendanceHistoryStart,
            'attendanceHistoryTotal' => $attendanceHistoryTotal,
            'attendanceHistoryTruncated' => $attendanceHistoryTotal > $attendanceHistoryLimit,
            'syncGymId' => $gymId,
            'syncGymName' => $gymName !== '' ? $gymName : 'Gym',
            'gymAvatarUrls' => $this->gymAvatarUrls($request, $gym),
        ]);
    }

    /**
     * Show monitor display screen with only result and recent entries.
     */
    public function display(Request $request): View
    {
        $gymId = ActiveGymContext::id($request);
        if (ActiveGymContext::isGlobal($request)) {
            return $this->index($request);
        }
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');
        $gym = $request->attributes->get('active_gym');
        if (! $gym instanceof Gym) {
            $gym = $request->user()?->gym;
        }
        $gymName = trim((string) ($gym?->name ?? 'Gym'));

        $recentAttendances = $this->recentAttendancesForGym($gymId);
        $latestSyncEvent = $this->latestSyncEventForGym($gymId);
        $latestSyncPayload = is_array($latestSyncEvent['payload'] ?? null)
            ? $latestSyncEvent['payload']
            : null;

        return view('reception.display', [
            'recentAttendances' => $recentAttendances,
            'latestResult' => $latestSyncPayload ?? $this->latestResultForGym($gymId),
            'latestSyncEventId' => (string) ($latestSyncEvent['id'] ?? ''),
            'latestSyncEventPublishedAt' => (int) ($latestSyncEvent['published_at_ms'] ?? 0),
            'syncGymId' => $gymId,
            'syncGymName' => $gymName !== '' ? $gymName : 'Gym',
            'gymAvatarUrls' => $this->gymAvatarUrls($request, $gym),
        ]);
    }

    /**
     * Get latest cross-device sync event for display polling.
     */
    public function syncLatest(Request $request): JsonResponse
    {
        $gymId = ActiveGymContext::id($request);
        if (ActiveGymContext::isGlobal($request)) {
            return response()->json(['event' => null]);
        }
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $after = (int) $request->integer('after', 0);
        $event = $this->latestSyncEventForGym($gymId);

        if (! $event) {
            return response()->json(['event' => null]);
        }

        $publishedAt = (int) ($event['published_at_ms'] ?? 0);
        if ($after > 0 && $publishedAt <= $after) {
            return response()->json(['event' => null]);
        }

        return response()->json(['event' => $event]);
    }

    /**
     * Unified endpoint for RFID/QR/document check-in.
     */
    public function store(
        ReceptionCheckInRequest $request,
        AttendanceCheckinService $attendanceService
    ): JsonResponse {
        $userId = (int) $request->user()->id;
        $gymId = ActiveGymContext::id($request);
        if (ActiveGymContext::isGlobal($request)) {
            return response()->json([
                'ok' => false,
                'reason' => 'global_scope_blocked',
                'message' => 'Selecciona una sucursal especifica para registrar check-in.',
            ], 409);
        }

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
                $gymId,
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
                'gym_id' => $gymId,
                'user_id' => $userId,
                'method' => null,
                'reason' => 'internal_exception',
            ]);

            $this->publishSyncEventForGym($gymId, $payload);

            return response()->json($payload, 500);
        }

        if ($result['ok']) {
            Log::info('reception.check_in.success', [
                'gym_id' => $gymId,
                'user_id' => $userId,
                'method' => $result['method'],
            ]);
        } else {
            Log::warning('reception.check_in.failed', [
                'gym_id' => $gymId,
                'user_id' => $userId,
                'method' => $result['method'],
                'reason' => $result['message'],
            ]);
        }

        $this->publishSyncEventForGym($gymId, $result);

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

        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1) {
            return null;
        }

        $normalized = str_replace('\\', '/', ltrim($path, '/'));

        $publicStorageMarker = '/storage/app/public/';
        $markerPos = strpos($normalized, $publicStorageMarker);
        if ($markerPos !== false) {
            $normalized = substr($normalized, $markerPos + strlen($publicStorageMarker));
        }

        if (Str::startsWith($normalized, 'public/')) {
            $normalized = substr($normalized, strlen('public/'));
        }

        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $normalized = ltrim($normalized, '/');
        if ($normalized === '' || str_contains($normalized, '..')) {
            return null;
        }

        return asset('storage/'.$normalized);
    }

    /**
     * @return array{male:?string,female:?string,neutral:?string}
     */
    private function gymAvatarUrls(Request $request, ?Gym $gym): array
    {
        $avatarUrls = [
            'male' => $this->resolvePhotoUrl($gym?->avatar_male_path),
            'female' => $this->resolvePhotoUrl($gym?->avatar_female_path),
            'neutral' => $this->resolvePhotoUrl($gym?->avatar_neutral_path),
        ];

        $missingKeys = array_filter(
            ['male', 'female', 'neutral'],
            static fn (string $key): bool => trim((string) ($avatarUrls[$key] ?? '')) === ''
        );

        if ($missingKeys === []) {
            return $avatarUrls;
        }

        $activeGymId = (int) ($gym?->id ?? ActiveGymContext::id($request));
        if ($activeGymId <= 0) {
            return $avatarUrls;
        }

        $hubGymId = 0;
        $hubFromContext = (int) ($request->attributes->get('hub_gym_id') ?? 0);
        if ($hubFromContext > 0 && $hubFromContext !== $activeGymId) {
            $isLinkedToHub = GymBranchLink::query()
                ->where('hub_gym_id', $hubFromContext)
                ->where('branch_gym_id', $activeGymId)
                ->where('status', 'active')
                ->exists();

            if ($isLinkedToHub) {
                $hubGymId = $hubFromContext;
            }
        }

        if ($hubGymId <= 0) {
            $hubGymId = (int) GymBranchLink::query()
                ->where('branch_gym_id', $activeGymId)
                ->where('status', 'active')
                ->value('hub_gym_id');
        }

        if ($hubGymId <= 0 || $hubGymId === $activeGymId) {
            return $avatarUrls;
        }

        $hubGym = Gym::query()
            ->select(['id', 'avatar_male_path', 'avatar_female_path', 'avatar_neutral_path'])
            ->find($hubGymId);

        if (! $hubGym) {
            return $avatarUrls;
        }

        $hubAvatarUrls = [
            'male' => $this->resolvePhotoUrl($hubGym->avatar_male_path),
            'female' => $this->resolvePhotoUrl($hubGym->avatar_female_path),
            'neutral' => $this->resolvePhotoUrl($hubGym->avatar_neutral_path),
        ];

        foreach ($missingKeys as $missingKey) {
            if (trim((string) ($hubAvatarUrls[$missingKey] ?? '')) !== '') {
                $avatarUrls[$missingKey] = $hubAvatarUrls[$missingKey];
            }
        }

        return $avatarUrls;
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

    private function publishSyncEventForGym(int $gymId, array $payload): void
    {
        if ($gymId <= 0) {
            return;
        }

        Cache::put($this->syncCacheKey($gymId), [
            'id' => (string) Str::ulid(),
            'type' => 'checkin',
            'source' => 'server',
            'payload' => $payload,
            'published_at_ms' => (int) round(microtime(true) * 1000),
        ], now()->addHours(12));
    }

    private function latestSyncEventForGym(int $gymId): ?array
    {
        if ($gymId <= 0) {
            return null;
        }

        $event = Cache::get($this->syncCacheKey($gymId));

        if (! is_array($event) || ! is_array($event['payload'] ?? null)) {
            return null;
        }

        return $event;
    }

    private function syncCacheKey(int $gymId): string
    {
        return 'reception:sync:gym:'.$gymId.':latest';
    }
}

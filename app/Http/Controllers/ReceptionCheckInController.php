<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Membership;
use App\Http\Requests\ReceptionCheckInRequest;
use App\Services\AttendanceCheckinService;
use App\Services\MobileCheckInTokenService;
use App\Services\PresenceSessionService;
use App\Services\PlanAccessService;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DateTimeInterface;
use Throwable;

class ReceptionCheckInController extends Controller
{
    public function __construct(
        private readonly PlanAccessService $planAccessService
    ) {
    }

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
        $latestSyncEvent = $this->latestSyncEventForGym($gymId);
        $latestSyncPayload = is_array($latestSyncEvent['payload'] ?? null)
            ? $latestSyncEvent['payload']
            : null;
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
            'latestResult' => $latestSyncPayload ?? $this->latestResultForGym($gymId),
            'latestSyncEventId' => (string) ($latestSyncEvent['id'] ?? ''),
            'latestSyncEventPublishedAt' => (int) ($latestSyncEvent['published_at_ms'] ?? 0),
            'gymAvatarUrls' => $this->gymAvatarUrls($request, $gym),
            'canManageClientAccounts' => $this->planAccessService->canForGym((int) $gymId, 'client_accounts'),
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
            'canManageClientAccounts' => $this->planAccessService->canForGym((int) $gymId, 'client_accounts'),
        ]);
    }

    public function mobileDisplay(Request $request): RedirectResponse
    {
        $routeParams = array_filter([
            'contextGym' => (string) ($request->route('contextGym') ?? ''),
            'scope' => $request->query('scope'),
            'pwa_mode' => $request->query('pwa_mode'),
            'open_mobile_scanner' => 1,
        ], static fn ($value): bool => $value !== null && $value !== '');

        return redirect()->route('reception.index', $routeParams);
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

    public function mobileQr(Request $request, MobileCheckInTokenService $mobileCheckInTokenService): JsonResponse
    {
        $gymId = ActiveGymContext::id($request);
        if (ActiveGymContext::isGlobal($request)) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.reception.select_branch_generate_mobile_qr'),
            ], 409);
        }

        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $data = $request->validate([
            'rotate_seconds' => ['nullable', 'integer', 'min:10', 'max:2592000'],
            'force' => ['nullable', 'boolean'],
        ]);

        $rotateSeconds = (int) ($data['rotate_seconds'] ?? 20);
        $force = (bool) ($data['force'] ?? false);
        $generated = null;
        if (! $force) {
            $generated = $mobileCheckInTokenService->current((int) $gymId);
        }

        if (! is_array($generated)) {
            $generated = $mobileCheckInTokenService->generate(
                gymId: (int) $gymId,
                issuedBy: (int) $request->user()->id,
                ttlSeconds: $rotateSeconds
            );
        }

        $qrPayload = $mobileCheckInTokenService->buildQrPayload((string) ($generated['token'] ?? ''));
        $qrSvg = QrCode::format('svg')
            ->size(260)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($qrPayload);

        return response()->json([
            'ok' => true,
            'token' => (string) ($generated['token'] ?? ''),
            'qr_payload' => $qrPayload,
            'qr_svg' => (string) $qrSvg,
            'rotate_seconds' => (int) ($generated['ttl_seconds'] ?? $rotateSeconds),
            'expires_at_ts' => (int) ($generated['expires_at_ts'] ?? 0),
            'issued_at_ts' => (int) ($generated['issued_at_ts'] ?? now()->timestamp),
            'server_time_ts' => now()->timestamp,
        ]);
    }

    public function mobileQrStatus(Request $request, MobileCheckInTokenService $mobileCheckInTokenService): JsonResponse
    {
        $gymId = ActiveGymContext::id($request);
        if (ActiveGymContext::isGlobal($request)) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.reception.select_branch_status_mobile_qr'),
            ], 409);
        }

        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $event = $mobileCheckInTokenService->latestConsumedEvent((int) $gymId);

        return response()->json([
            'ok' => true,
            'consumed' => $event ? [
                'token' => (string) ($event['token'] ?? ''),
                'consumed_at_ms' => (int) ($event['consumed_at_ms'] ?? 0),
            ] : null,
            'server_time_ts' => now()->timestamp,
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
        $gymId = ActiveGymContext::id($request);
        if (ActiveGymContext::isGlobal($request)) {
            return response()->json([
                'ok' => false,
                'reason' => 'global_scope_blocked',
                'message' => __('messages.reception.select_branch_check_in'),
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
                'event_type' => 'checkin',
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
                'message' => __('messages.reception.checkin_internal_error'),
                'method' => null,
                'client' => null,
                'attendance' => null,
                'attempt' => [
                    'date' => now()->toDateString(),
                    'time' => now()->format('H:i:s'),
                ],
                'event_type' => 'checkin',
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

    /**
     * Unified endpoint for RFID/QR/document check-out.
     */
    public function checkOut(
        ReceptionCheckInRequest $request,
        PresenceSessionService $presenceSessionService
    ): JsonResponse {
        $userId = (int) $request->user()->id;
        $gymId = ActiveGymContext::id($request);
        $attempt = $this->attemptPayload();

        if (ActiveGymContext::isGlobal($request)) {
            $payload = [
                'ok' => false,
                'reason' => 'global_scope_blocked',
                'message' => __('messages.reception.select_branch_check_out'),
                'method' => null,
                'client' => null,
                'attendance' => null,
                'attempt' => $attempt,
                'event_type' => 'checkout',
            ];

            return response()->json($payload, 409);
        }

        if (! $gymId) {
            $payload = [
                'ok' => false,
                'reason' => 'missing_gym',
                'message' => 'El usuario autenticado no tiene gym_id asignado.',
                'method' => null,
                'client' => null,
                'attendance' => null,
                'attempt' => $attempt,
                'event_type' => 'checkout',
            ];

            Log::warning('reception.check_out.failed', [
                'gym_id' => null,
                'user_id' => $userId,
                'reason' => 'missing_gym_id',
            ]);

            return response()->json($payload, 422);
        }

        try {
            $candidate = $this->resolveClientCandidateByValue($gymId, (string) $request->validated('value'));
            if (! $candidate) {
                $payload = [
                    'ok' => false,
                    'reason' => 'not_found',
                    'message' => __('messages.reception.client_not_found_for_value'),
                    'method' => null,
                    'client' => null,
                    'attendance' => null,
                    'attempt' => $attempt,
                    'event_type' => 'checkout',
                ];

                Log::warning('reception.check_out.failed', [
                    'gym_id' => $gymId,
                    'user_id' => $userId,
                    'reason' => 'not_found',
                ]);

                $this->publishSyncEventForGym($gymId, $payload, 'checkout');

                return response()->json($payload, 422);
            }

            $clientPayload = $this->buildClientPayloadForResult($gymId, $candidate);
            $method = (string) $candidate['method'];
            $closedSession = $presenceSessionService->registerCheckOut(
                gymId: $gymId,
                clientId: (int) $candidate['client_id'],
                checkOutBy: $userId,
                checkOutMethod: $method,
                reason: 'manual'
            );

            if (! $closedSession) {
                $payload = [
                    'ok' => false,
                    'reason' => 'not_inside',
                    'message' => __('messages.reception.client_not_inside'),
                    'method' => $method,
                    'client' => $clientPayload,
                    'attendance' => null,
                    'attempt' => $attempt,
                    'event_type' => 'checkout',
                ];

                Log::warning('reception.check_out.failed', [
                    'gym_id' => $gymId,
                    'user_id' => $userId,
                    'client_id' => (int) $candidate['client_id'],
                    'method' => $method,
                    'reason' => 'not_inside',
                ]);

                $this->publishSyncEventForGym($gymId, $payload, 'checkout');

                return response()->json($payload, 422);
            }

            $payload = [
                'ok' => true,
                'reason' => 'checkout_success',
                'message' => __('messages.reception.checkout_success'),
                'method' => $method,
                'client' => $clientPayload,
                'attendance' => null,
                'attempt' => $attempt,
                'event_type' => 'checkout',
            ];

            Log::info('reception.check_out.success', [
                'gym_id' => $gymId,
                'user_id' => $userId,
                'client_id' => (int) $candidate['client_id'],
                'method' => $method,
            ]);

            $this->publishSyncEventForGym($gymId, $payload, 'checkout');

            return response()->json($payload);
        } catch (Throwable $exception) {
            report($exception);

            $payload = [
                'ok' => false,
                'reason' => 'internal_error',
                'message' => __('messages.reception.checkout_internal_error'),
                'method' => null,
                'client' => null,
                'attendance' => null,
                'attempt' => $attempt,
                'event_type' => 'checkout',
            ];

            Log::warning('reception.check_out.failed', [
                'gym_id' => $gymId,
                'user_id' => $userId,
                'reason' => 'internal_exception',
            ]);

            $this->publishSyncEventForGym($gymId, $payload, 'checkout');

            return response()->json($payload, 500);
        }
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
            'message' => __('messages.reception.checkin_success'),
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
            'event_type' => 'checkin',
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

    /**
     * @return array{date:string,time:string}
     */
    private function attemptPayload(): array
    {
        return [
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
        ];
    }

    /**
     * @return array{
     *     client_id:int,
     *     full_name:string,
     *     photo_path:?string,
     *     gender:string,
     *     method:string
     * }|null
     */
    private function resolveClientCandidateByValue(int $gymId, string $value): ?array
    {
        $normalizedValue = trim($value);
        if ($normalizedValue === '') {
            return null;
        }

        $candidate = DB::table('client_credentials as credentials')
            ->join('clients as clients', function ($join): void {
                $join->on('clients.id', '=', 'credentials.client_id')
                    ->on('clients.gym_id', '=', 'credentials.gym_id');
            })
            ->where('credentials.gym_id', $gymId)
            ->where('credentials.value', $normalizedValue)
            ->orderByRaw("CASE WHEN credentials.status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('credentials.id')
            ->select([
                'clients.id as client_id',
                'clients.first_name',
                'clients.last_name',
                'clients.photo_path',
                'clients.gender as client_gender',
                'credentials.type as credential_type',
            ])
            ->first();

        if ($candidate) {
            return [
                'client_id' => (int) $candidate->client_id,
                'full_name' => trim(((string) $candidate->first_name).' '.((string) $candidate->last_name)),
                'photo_path' => $candidate->photo_path ? (string) $candidate->photo_path : null,
                'gender' => (string) ($candidate->client_gender ?? ''),
                'method' => $candidate->credential_type
                    ? (string) $candidate->credential_type
                    : 'document',
            ];
        }

        $documentCandidate = DB::table('clients')
            ->where('gym_id', $gymId)
            ->where('document_number', $normalizedValue)
            ->select([
                'id as client_id',
                'first_name',
                'last_name',
                'photo_path',
                'gender as client_gender',
            ])
            ->first();

        if (! $documentCandidate) {
            return null;
        }

        return [
            'client_id' => (int) $documentCandidate->client_id,
            'full_name' => trim(((string) $documentCandidate->first_name).' '.((string) $documentCandidate->last_name)),
            'photo_path' => $documentCandidate->photo_path ? (string) $documentCandidate->photo_path : null,
            'gender' => (string) ($documentCandidate->client_gender ?? ''),
            'method' => 'document',
        ];
    }

    /**
     * @param  array{
     *     client_id:int,
     *     full_name:string,
     *     photo_path:?string,
     *     gender:string
     * }  $candidate
     * @return array{
     *     id:int,
     *     full_name:string,
     *     photo_url:?string,
     *     membership_ends_at:?string,
     *     month_visits:int,
     *     total_visits:int,
     *     last_attendance_date:?string,
     *     last_attendance_time:?string,
     *     gender:'male'|'female'|'neutral'
     * }
     */
    private function buildClientPayloadForResult(int $gymId, array $candidate): array
    {
        $clientId = (int) $candidate['client_id'];
        $latestAttendance = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first(['date', 'time']);
        $referenceDate = (string) ($latestAttendance?->date?->toDateString() ?? now()->toDateString());
        $membershipEndsAt = Membership::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('ends_at')
            ->value('ends_at');

        return [
            'id' => $clientId,
            'full_name' => (string) ($candidate['full_name'] ?: '-'),
            'photo_url' => $this->resolvePhotoUrl($candidate['photo_path'] ?? null),
            'membership_ends_at' => $this->normalizeMembershipEndsAt($membershipEndsAt),
            'month_visits' => $this->countMonthVisits($gymId, $clientId, $referenceDate),
            'total_visits' => Attendance::query()
                ->forGym($gymId)
                ->where('client_id', $clientId)
                ->count(),
            'last_attendance_date' => $latestAttendance?->date?->toDateString(),
            'last_attendance_time' => (string) ($latestAttendance?->time ?? ''),
            'gender' => $this->normalizeGender($candidate['gender'] ?? null),
        ];
    }

    private function publishSyncEventForGym(int $gymId, array $payload, string $eventType = 'checkin'): void
    {
        if ($gymId <= 0) {
            return;
        }

        $normalizedType = strtolower(trim($eventType));
        if (! in_array($normalizedType, ['checkin', 'checkout', 'mobile_qr_refresh', 'mobile_qr_state'], true)) {
            $normalizedType = 'checkin';
        }

        Cache::put($this->syncCacheKey($gymId), [
            'id' => (string) Str::ulid(),
            'type' => $normalizedType,
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

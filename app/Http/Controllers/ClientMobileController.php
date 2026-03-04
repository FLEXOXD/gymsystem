<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\PresenceSession;
use App\Models\User;
use App\Services\AttendanceCheckinService;
use App\Services\MobileCheckInTokenService;
use App\Services\PlanAccessService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientMobileController extends Controller
{
    public function __construct(
        private readonly PlanAccessService $planAccessService,
        private readonly AttendanceCheckinService $attendanceCheckinService,
        private readonly MobileCheckInTokenService $mobileCheckInTokenService
    ) {
    }

    public function login(Request $request, string $gymSlug): View|RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $session = (array) $request->session()->get('client_mobile', []);
        if ((int) ($session['client_id'] ?? 0) > 0 && (int) ($session['gym_id'] ?? 0) === (int) $gym->id) {
            return redirect()->route('client-mobile.app', ['gymSlug' => $gym->slug]);
        }

        return view('client-mobile.login', ['gym' => $gym]);
    }

    public function authenticate(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'max:120'],
        ]);

        $username = mb_strtolower(trim((string) $data['username']));
        $client = Client::query()
            ->forGym((int) $gym->id)
            ->whereRaw('LOWER(app_username) = ?', [$username])
            ->first(['id', 'gym_id', 'status', 'app_password']);

        if (! $client || trim((string) ($client->app_password ?? '')) === '' || ! Hash::check((string) $data['password'], (string) $client->app_password)) {
            return back()->withErrors([
                'mobile_login' => __('messages.client_mobile.invalid_credentials'),
            ])->withInput($request->only('username'));
        }

        if ((string) ($client->status ?? 'inactive') !== 'active') {
            return back()->withErrors([
                'mobile_login' => __('messages.client_mobile.inactive_profile'),
            ])->withInput($request->only('username'));
        }

        $request->session()->regenerate();
        $request->session()->put('client_mobile', [
            'client_id' => (int) $client->id,
            'gym_id' => (int) $gym->id,
            'login_at' => now()->toIso8601String(),
        ]);

        return redirect()->route('client-mobile.app', ['gymSlug' => $gym->slug]);
    }

    public function logout(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);

        $request->session()->forget('client_mobile');
        $request->session()->regenerateToken();

        return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
    }

    public function app(Request $request, string $gymSlug): View|RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
        }

        return view('client-mobile.app', [
            'gym' => $gym,
            'client' => $client,
            'progress' => $this->progressPayload((int) $gym->id, (int) $client->id, (string) ($gym->timezone ?? '')),
        ]);
    }

    public function progress(Request $request, string $gymSlug): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        return response()->json([
            'ok' => true,
            'progress' => $this->progressPayload((int) $gym->id, (int) $client->id, (string) ($gym->timezone ?? '')),
        ]);
    }

    public function checkIn(Request $request, string $gymSlug): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        $data = $request->validate([
            'token' => ['required', 'string', 'max:300'],
        ]);

        $token = $this->mobileCheckInTokenService->extractToken((string) $data['token']);
        $consumed = $this->mobileCheckInTokenService->consume($token, (int) $gym->id);
        if (! (bool) ($consumed['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'reason' => (string) ($consumed['reason'] ?? 'token_invalid'),
                'message' => (string) ($consumed['message'] ?? __('messages.client_mobile.invalid_qr')),
            ], 422);
        }

        $operatorId = $this->resolveMobileOperatorUserId((int) $gym->id);
        if ($operatorId <= 0) {
            return response()->json([
                'ok' => false,
                'reason' => 'operator_missing',
                'message' => __('messages.client_mobile.operator_missing'),
            ], 422);
        }

        $result = $this->attendanceCheckinService->checkInByValue(
            (int) $gym->id,
            $operatorId,
            (string) $client->document_number
        );

        $result['method'] = 'mobile_proximity';
        $this->publishReceptionSync((int) $gym->id, $result);

        return response()->json([
            ...$result,
            'progress' => $this->progressPayload((int) $gym->id, (int) $client->id, (string) ($gym->timezone ?? '')),
        ], (bool) ($result['ok'] ?? false) ? 200 : 422);
    }

    private function resolveGymBySlug(string $gymSlug): Gym
    {
        return Gym::query()
            ->withoutDemoSessions()
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower(trim($gymSlug))])
            ->firstOrFail(['id', 'name', 'slug', 'timezone']);
    }

    private function abortIfFeatureUnavailable(int $gymId): void
    {
        abort_if(! $this->planAccessService->canForGym($gymId, 'client_accounts'), 403, 'Tu plan actual no incluye acceso cliente PWA.');
    }

    private function resolveSessionClient(Request $request, Gym $gym): ?Client
    {
        $session = (array) $request->session()->get('client_mobile', []);
        $clientId = (int) ($session['client_id'] ?? 0);
        $sessionGymId = (int) ($session['gym_id'] ?? 0);

        if ($clientId <= 0 || $sessionGymId !== (int) $gym->id) {
            return null;
        }

        return Client::query()
            ->forGym((int) $gym->id)
            ->where('status', 'active')
            ->find($clientId, ['id', 'gym_id', 'first_name', 'last_name', 'document_number', 'photo_path', 'status']);
    }

    private function resolveMobileOperatorUserId(int $gymId): int
    {
        $user = User::query()
            ->where('gym_id', $gymId)
            ->where(function ($query): void {
                $query->whereNull('role')
                    ->orWhereIn('role', [User::ROLE_OWNER, User::ROLE_CASHIER]);
            })
            ->where(function ($query): void {
                $query->whereNull('is_active')
                    ->orWhere('is_active', true);
            })
            ->orderByRaw("CASE WHEN role = 'owner' OR role IS NULL THEN 0 WHEN role = 'cashier' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->first(['id']);

        return (int) ($user?->id ?? 0);
    }

    private function progressPayload(int $gymId, int $clientId, string $gymTimezone = ''): array
    {
        $timezone = $this->resolveTimezone($gymTimezone);
        $nowAtGym = Carbon::now($timezone);
        $today = $nowAtGym->toDateString();
        $monthStart = $nowAtGym->copy()->startOfMonth()->toDateString();
        $monthEnd = $nowAtGym->copy()->endOfMonth()->toDateString();

        $latestMembership = Membership::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('ends_at')
            ->orderByDesc('id')
            ->first(['id', 'starts_at', 'ends_at', 'status']);

        $monthVisits = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();

        $totalVisits = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->count();

        $liveClientsCount = PresenceSession::query()
            ->where('gym_id', $gymId)
            ->open()
            ->count();

        $lastAttendance = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first(['date', 'time']);

        $daysRemaining = null;
        if ($latestMembership?->ends_at) {
            $daysRemaining = max(0, Carbon::parse((string) $latestMembership->ends_at, $timezone)->startOfDay()->diffInDays($nowAtGym->copy()->startOfDay(), false) * -1);
        }

        return [
            'membership_status' => (string) ($latestMembership?->status ?? 'inactive'),
            'membership_ends_at' => $latestMembership?->ends_at?->toDateString(),
            'days_remaining' => $daysRemaining,
            'month_visits' => (int) $monthVisits,
            'total_visits' => (int) $totalVisits,
            'live_clients_count' => (int) $liveClientsCount,
            'live_window_label' => 'En vivo',
            'last_attendance_date' => $lastAttendance?->date?->toDateString(),
            'last_attendance_time' => (string) ($lastAttendance?->time ?? ''),
            'today' => $today,
        ];
    }

    private function resolveTimezone(string $timezone): string
    {
        $candidate = trim($timezone);
        if ($candidate !== '' && in_array($candidate, timezone_identifiers_list(), true)) {
            return $candidate;
        }

        return config('app.timezone', 'UTC');
    }

    private function publishReceptionSync(int $gymId, array $payload): void
    {
        if ($gymId <= 0) {
            return;
        }

        $eventType = strtolower(trim((string) ($payload['event_type'] ?? 'checkin')));
        if ($eventType === '') {
            $eventType = 'checkin';
        }

        Cache::put('reception:sync:gym:'.$gymId.':latest', [
            'id' => (string) Str::ulid(),
            'type' => $eventType,
            'source' => 'mobile-client',
            'payload' => $payload,
            'published_at_ms' => (int) round(microtime(true) * 1000),
        ], now()->addHours(12));
    }
}

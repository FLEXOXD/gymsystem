<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\DemoSession;
use App\Models\GymAdminLoginEvent;
use App\Models\GymBranchLink;
use App\Models\User;
use App\Services\DemoSessionService;
use App\Services\GymAdminActivityService;
use App\Services\PlanAccessService;
use App\Services\SupportChatPresenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private readonly PlanAccessService $planAccessService,
        private readonly SupportChatPresenceService $supportChatPresenceService,
        private readonly GymAdminActivityService $gymAdminActivityService,
    ) {
    }

    /**
     * Show login form.
     */
    public function create(): View
    {
        return view('auth.login', $this->buildLoginBranding());
    }

    /**
     * Handle login.
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Credenciales incorrectas.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();
        if ($user && ! $user->isActiveAccount()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Tu usuario está desactivado. Contacta al administrador del gimnasio.',
            ]);
        }

        $this->touchLastLoginAt($user);
        $this->recordGymAdminLoginEvent($request, $user);

        if ($user?->gym_id === null) {
            if (Route::has('superadmin.dashboard')) {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect('/superadmin/dashboard');
        }

        $gymSlug = trim((string) ($user?->gym?->slug ?? ''));
        if ($gymSlug === '') {
            return redirect()->route('login')->withErrors([
                'email' => 'Tu usuario no tiene un gimnasio válido asignado.',
            ]);
        }
        $gymId = (int) ($user?->gym_id ?? 0);
        $isBranchGym = $gymId > 0
            ? GymBranchLink::query()->where('branch_gym_id', $gymId)->exists()
            : false;
        $hasLinkedBranches = $gymId > 0
            ? GymBranchLink::query()->where('hub_gym_id', $gymId)->exists()
            : false;
        $isCashier = (bool) ($user?->isCashier());
        $shouldUseGlobalContext = $gymId > 0
            && ! $isCashier
            && ! $isBranchGym
            && $hasLinkedBranches
            && $this->planAccessService->can($user, 'multi_branch');
        $isStandalonePwaMode = strtolower(trim((string) $request->input('pwa_mode', ''))) === 'standalone';

        if (Route::has('panel.index')) {
            return redirect()->route('panel.index', [
                'contextGym' => $gymSlug,
            ] + ($shouldUseGlobalContext ? ['scope' => 'global'] : []) + ($isStandalonePwaMode ? ['pwa_mode' => 'standalone'] : []));
        }

        $query = [];
        if ($shouldUseGlobalContext) {
            $query['scope'] = 'global';
        }
        if ($isStandalonePwaMode) {
            $query['pwa_mode'] = 'standalone';
        }

        return redirect('/'.$gymSlug.'/panel'.(empty($query) ? '' : '?'.http_build_query($query)));
    }

    /**
     * Handle logout.
     */
    public function destroy(Request $request, DemoSessionService $demoSessionService): RedirectResponse
    {
        $authUser = $request->user();
        $userId = (int) ($request->user()?->id ?? 0);
        $demoSession = null;
        if ($userId > 0) {
            $demoSession = DemoSession::query()
                ->where('user_id', $userId)
                ->first();
        }

        Auth::logout();
        $this->supportChatPresenceService->clearForUser($authUser);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($demoSession) {
            try {
                $demoSessionService->terminateSession($demoSession);
            } catch (\Throwable $exception) {
                Log::warning('No se pudo cerrar completamente la sesión demo en logout.', [
                    'user_id' => $userId,
                    'demo_session_id' => $demoSession->id,
                    'error' => $exception->getMessage(),
                ]);
            }

            return $this->applyNoHistoryRedirectHeaders(redirect()->route('landing'));
        }

        return $this->applyNoHistoryRedirectHeaders(redirect()->route('login'));
    }

    /**
     * Redirect stale /logout history entries back to login cleanly.
     */
    public function redirectAfterLogout(): RedirectResponse
    {
        return $this->applyNoHistoryRedirectHeaders(redirect()->route('login'));
    }

    /**
     * End active demo session immediately and clear demo data.
     */
    public function endDemo(Request $request, DemoSessionService $demoSessionService): RedirectResponse|JsonResponse
    {
        $authUser = $request->user();
        $userId = (int) ($request->user()?->id ?? 0);
        $demoSession = $this->resolveDemoSessionForUser($userId);
        $ended = false;

        if ($demoSession) {
            try {
                $demoSessionService->terminateSession($demoSession);
                $ended = true;
            } catch (\Throwable $exception) {
                Log::warning('No se pudo finalizar completamente la sesión demo.', [
                    'user_id' => $userId,
                    'demo_session_id' => $demoSession->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        Auth::logout();
        $this->supportChatPresenceService->clearForUser($authUser);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ended' => $ended,
            ]);
        }

        return redirect()
            ->route('landing')
            ->with('status', 'Demo finalizada. Tus datos temporales fueron eliminados.');
    }

    private function applyNoHistoryRedirectHeaders(RedirectResponse $response): RedirectResponse
    {
        return $response->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
            'Pragma' => 'no-cache',
            'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
        ]);
    }

    /**
     * Update last login timestamp only when schema supports it.
     */
    private function touchLastLoginAt(?User $user): void
    {
        if (! $user) {
            return;
        }

        static $hasLastLoginAtColumn = null;
        if ($hasLastLoginAtColumn === null) {
            $hasLastLoginAtColumn = Schema::hasColumn('users', 'last_login_at');
        }

        if (! $hasLastLoginAtColumn) {
            return;
        }

        try {
            $user->forceFill([
                'last_login_at' => now('UTC'),
            ])->save();
        } catch (\Throwable $exception) {
            Log::warning('No se pudo guardar last_login_at durante login.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Keep an audit trail of successful owner logins for SuperAdmin.
     */
    private function recordGymAdminLoginEvent(Request $request, ?User $user): void
    {
        if (! $user || (int) ($user->gym_id ?? 0) <= 0 || ! $user->isOwner()) {
            return;
        }

        static $supportsLoginAuditTable = null;
        if ($supportsLoginAuditTable === null) {
            $supportsLoginAuditTable = Schema::hasTable('gym_admin_login_events');
        }

        if (! $supportsLoginAuditTable) {
            return;
        }

        try {
            GymAdminLoginEvent::query()->create([
                'gym_id' => (int) $user->gym_id,
                'user_id' => (int) $user->id,
                'gym_name' => trim((string) ($user->gym?->name ?? '')) ?: null,
                'user_name' => trim((string) ($user->name ?? '')) ?: null,
                'user_email' => trim((string) ($user->email ?? '')) ?: null,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) ($request->userAgent() ?? ''), 0, 1024),
                'logged_in_at' => now('UTC'),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('No se pudo guardar gym_admin_login_events durante login.', [
                'user_id' => $user->id,
                'gym_id' => $user->gym_id,
                'error' => $exception->getMessage(),
            ]);
        }

        $this->gymAdminActivityService->touch($request, $user, [
            'signal' => 'login_manual',
            'mark_login' => true,
            'route_name' => 'login',
            'path' => '/login',
            'via_remember' => false,
        ]);
    }

    private function resolveDemoSessionForUser(int $userId): ?DemoSession
    {
        if ($userId <= 0) {
            return null;
        }

        return DemoSession::query()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Resolve branding data for login page (SuperAdmin support logos).
     *
     * @return array{loginBranding: array{logo_light_url:string,logo_dark_url:string}}
     */
    private function buildLoginBranding(): array
    {
        $default = [
            'logo_light_url' => '',
            'logo_dark_url' => '',
        ];

        $superAdmin = User::query()
            ->whereNull('gym_id')
            ->orderBy('id')
            ->first();

        if (! $superAdmin) {
            return ['loginBranding' => $default];
        }

        $logoLightUrl = '';
        $logoDarkUrl = '';

        if (Schema::hasColumns('users', ['support_contact_logo_light_path', 'support_contact_logo_dark_path'])) {
            $logoLightUrl = $this->resolvePublicAssetUrl((string) ($superAdmin->support_contact_logo_light_path ?? ''));
            $logoDarkUrl = $this->resolvePublicAssetUrl((string) ($superAdmin->support_contact_logo_dark_path ?? ''));
        }

        if ($logoLightUrl === '' && $logoDarkUrl === '' && Schema::hasColumn('users', 'support_contact_logo_path')) {
            $legacy = $this->resolvePublicAssetUrl((string) ($superAdmin->support_contact_logo_path ?? ''));
            $logoLightUrl = $legacy;
            $logoDarkUrl = $legacy;
        }

        return [
            'loginBranding' => [
                'logo_light_url' => $logoLightUrl,
                'logo_dark_url' => $logoDarkUrl,
            ],
        ];
    }

    /**
     * Convert a public disk path to a browser URL if file exists.
     */
    private function resolvePublicAssetUrl(?string $path): string
    {
        $assetPath = trim((string) $path);
        if ($assetPath === '') {
            return '';
        }
        if (str_starts_with($assetPath, 'http://') || str_starts_with($assetPath, 'https://')) {
            return $assetPath;
        }

        $relativePath = ltrim($assetPath, '/');
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, 8);
        }
        if ($relativePath === '' || str_contains($relativePath, '..')) {
            return '';
        }
        if (! Storage::disk('public')->exists($relativePath)) {
            return '';
        }

        return asset('storage/'.$relativePath);
    }
}

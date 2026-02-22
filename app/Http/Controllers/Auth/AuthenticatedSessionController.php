<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
        $this->touchLastLoginAt($user);

        if ($user?->gym_id === null) {
            if (Route::has('superadmin.dashboard')) {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect('/superadmin/dashboard');
        }

        $gymSlug = trim((string) ($user?->gym?->slug ?? ''));
        if ($gymSlug === '') {
            return redirect()->route('login')->withErrors([
                'email' => 'Tu usuario no tiene un gimnasio valido asignado.',
            ]);
        }

        if (Route::has('panel.index')) {
            return redirect()->route('panel.index', ['contextGym' => $gymSlug]);
        }

        return redirect('/'.$gymSlug.'/panel');
    }

    /**
     * Handle logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
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

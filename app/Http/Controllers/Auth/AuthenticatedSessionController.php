<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login form.
     */
    public function create(): View
    {
        return view('auth.login');
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

        if ($user?->gym_id === null) {
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->route('reception.index');
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
}

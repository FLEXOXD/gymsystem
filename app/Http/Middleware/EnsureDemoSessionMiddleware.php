<?php

namespace App\Http\Middleware;

use App\Models\DemoSession;
use App\Services\DemoSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoSessionMiddleware
{
    public function __construct(
        private readonly DemoSessionService $demoSessionService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        view()->share('demo_mode', false);
        view()->share('demo_session_token', null);
        view()->share('demo_expires_at', null);
        view()->share('demo_expires_at_iso', null);
        view()->share('demo_server_now_iso', null);
        view()->share('demo_guide_steps', []);

        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $demoSession = DemoSession::query()
            ->where('user_id', $user->id)
            ->first();

        if (! $demoSession) {
            return $next($request);
        }

        if ($demoSession->expires_at->isPast()) {
            $this->demoSessionService->terminateSession($demoSession);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('landing')
                ->with('status', 'La demo temporal finalizo. Puedes solicitar una nueva cuando quieras.');
        }

        $this->demoSessionService->touchActivity($demoSession);

        $gymSlug = trim((string) ($user->gym?->slug ?? ''));
        view()->share('demo_mode', true);
        view()->share('demo_session_token', $demoSession->token);
        view()->share('demo_expires_at', $demoSession->expires_at);
        view()->share('demo_expires_at_iso', $demoSession->expires_at->toIso8601String());
        view()->share('demo_server_now_iso', now()->toIso8601String());
        view()->share(
            'demo_guide_steps',
            $gymSlug !== '' ? $this->demoSessionService->guideSteps($gymSlug) : []
        );

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\GymAdminActivityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackGymOwnerActivityMiddleware
{
    public function __construct(
        private readonly GymAdminActivityService $activityService
    ) {
    }

    /**
     * Track full-page owner visits so remembered sessions and PWA launches count as usage.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        if (! $user || $user->gym_id === null || ! $user->isOwner()) {
            return $response;
        }

        if (! in_array(strtoupper($request->method()), ['GET', 'HEAD'], true)) {
            return $response;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $contentType = mb_strtolower(trim((string) $response->headers->get('content-type', '')));
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $this->activityService->touch($request, $user, [
            'signal' => Auth::viaRemember() ? 'sesion_recordada' : 'page_visit',
            'route_name' => (string) ($request->route()?->getName() ?? ''),
            'path' => (string) $request->getRequestUri(),
            'via_remember' => Auth::viaRemember(),
        ]);

        return $response;
    }
}

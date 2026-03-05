<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBrowserPermissionsPolicyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $path = '/'.ltrim((string) $request->path(), '/');
        if (! str_starts_with($path, '/cliente/')) {
            return $response;
        }

        // Allow camera/microphone/notifications-related capabilities for client mobile app.
        // If web server injects a stricter policy later, that server policy must also be updated.
        $response->headers->set(
            'Permissions-Policy',
            'camera=(self), microphone=(self), geolocation=(self), fullscreen=(self), display-capture=(self)'
        );
        $response->headers->set(
            'Feature-Policy',
            "camera 'self'; microphone 'self'; geolocation 'self'"
        );

        return $response;
    }
}


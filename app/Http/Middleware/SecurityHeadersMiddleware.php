<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=(), payment=()');

        // API-only CSP: no resources should be loaded from API responses
        if ($request->is('api/*')) {
            $response->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'");
        }

        if (config('app.env') === 'production') {
            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
            }
            // Remove fingerprinting headers in production
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
        }

        return $response;
    }
}

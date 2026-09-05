<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestIdMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $incoming = trim((string) $request->header('X-Request-Id'));
        $requestId = $incoming !== '' && Str::isUuid($incoming) ? $incoming : (string) Str::uuid();

        $request->attributes->set('request_id', $requestId);
        $startedAt = microtime(true);
        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);
        $status = $response->getStatusCode();
        $payload = method_exists($response, 'getData') ? $response->getData(true) : [];
        $route = $request->route();
        $routeName = is_object($route) ? $route->getName() : null;
        $routeIdentifier = is_string($routeName) && $routeName !== '' && ! Str::startsWith($routeName, 'generated::')
            ? $routeName
            : (is_object($route) ? $route->uri() : $request->path());
        $context = [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => $routeIdentifier,
            'status' => $status,
            'status_class' => intdiv($status, 100).'xx',
            'actor_id' => $request->user('api')?->getAuthIdentifier(),
            'error_code' => is_array($payload) ? data_get($payload, 'errors.code') : null,
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
        ];
        Log::info('api.request', $context);

        $telemetryChannel = config('observability.api_request_channel');
        if (is_string($telemetryChannel) && trim($telemetryChannel) !== '') {
            try {
                Log::channel($telemetryChannel)->info('api.request', $context);
            } catch (\Throwable $exception) {
                Log::warning('api.request telemetry channel unavailable', [
                    'channel' => $telemetryChannel,
                    'exception' => $exception::class,
                ]);
            }
        }

        return $response;
    }
}

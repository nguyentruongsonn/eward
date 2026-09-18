<?php

use App\Exceptions\ApiException;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->validateCsrfTokens(except: ['webhook/email-reply']);
        $middleware->api(prepend: [
            \App\Http\Middleware\ResetApiGuard::class,
            \App\Http\Middleware\RequestIdMiddleware::class,
            \App\Http\Middleware\ForceJsonApi::class,
        ]);
        $middleware->alias([
            'appointment' => \App\Http\Middleware\RequireAppointmentStaff::class,
            'staff' => \App\Http\Middleware\RequireStaff::class,
            'administrator' => \App\Http\Middleware\RequireAdministrator::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ApiException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    $exception->getMessage(),
                    $exception->errorCode,
                    $exception->status,
                    $exception->details,
                    $request,
                );
            }
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::validation($exception->errors(), $request);
            }
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Bạn cần đăng nhập để tiếp tục.', 'AUTHENTICATION_REQUIRED', 401, [], $request);
            }
        });

        $exceptions->render(function (JWTException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Token không hợp lệ hoặc đã hết hạn.', 'AUTH_TOKEN_INVALID', 401, [], $request);
            }
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Bạn không có quyền thực hiện thao tác này.', 'FORBIDDEN', 403, [], $request);
            }
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Không tìm thấy tài nguyên.', 'NOT_FOUND', 404, [], $request);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($request->is('api/*') && $exception->getStatusCode() >= 400) {
                $status = $exception->getStatusCode();
                $code = match ($status) {
                    401 => 'AUTHENTICATION_REQUIRED',
                    403 => 'FORBIDDEN',
                    404 => 'NOT_FOUND',
                    429 => 'RATE_LIMITED',
                    default => 'HTTP_ERROR',
                };

                return ApiResponse::error($exception->getMessage() ?: 'Yêu cầu không thể xử lý.', $code, $status, [], $request);
            }

            // The legacy browser routes have been retired. Treat method probes
            // for those former form endpoints as a JSON not-found response too,
            // rather than exposing Laravel's HTML 405 page.
            if (in_array($exception->getStatusCode(), [404, 405], true)) {
                return ApiResponse::error('Không tìm thấy tài nguyên.', 'NOT_FOUND', 404, [], $request);
            }
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($request->is('api/*')) {
                report($exception);

                return ApiResponse::error('Hệ thống không thể xử lý yêu cầu.', 'INTERNAL_SERVER_ERROR', 500, [], $request);
            }
        });
    })->create();

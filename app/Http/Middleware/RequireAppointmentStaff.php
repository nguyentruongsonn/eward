<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAppointmentStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api') ?? $request->user();
        if (! $user || ! (Role::normalize($user->vaiTro)?->canAccessAppointments() ?? false)) {
            return ApiResponse::error('Bạn không có quyền quản trị.', 'FORBIDDEN', 403, [], $request);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api');
        if (! $user || ! (Role::normalize($user->vaiTro)?->isAdministrator() ?? false)) {
            return ApiResponse::error('Chỉ quản trị viên mới được thực hiện thao tác này.', 'FORBIDDEN', 403, [], $request);
        }

        return $next($request);
    }
}

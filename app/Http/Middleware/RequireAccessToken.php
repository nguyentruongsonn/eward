<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAccessToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $tokenType = auth('api')->payload()->get('token_type');

        if ($tokenType !== null && $tokenType !== 'access') {
            return ApiResponse::error('Endpoint này yêu cầu access token.', 'ACCESS_TOKEN_REQUIRED', 401, [], $request);
        }

        return $next($request);
    }
}

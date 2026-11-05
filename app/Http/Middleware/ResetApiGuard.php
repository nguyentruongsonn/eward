<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Manager;

class ResetApiGuard
{
    public function __construct(
        private readonly AuthManager $auth,
        private readonly JWT $jwt,
        private readonly Manager $manager,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $this->manager->setRefreshFlow(false);
        $this->auth->forgetGuards();
        $this->jwt->unsetToken();

        return $next($request);
    }
}

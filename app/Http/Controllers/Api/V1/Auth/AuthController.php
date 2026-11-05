<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResendOtpRequest;
use App\Http\Requests\Api\V1\Auth\VerifyOtpRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Nguoi;
use App\Services\Auth\AuthService;
use App\Services\Auth\OtpService;
use App\Support\ApiResponse;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\Providers\JWT as JWTProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Manager as JWTManager;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly OtpService $otpService,
        private readonly JWTProvider $jwtProvider,
        private readonly JWTManager $jwtManager,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $email = $data['email'];
        $throttleKey = $this->throttleKey('register', $email, $request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return ApiResponse::error('Bạn đã yêu cầu quá nhiều mã OTP. Vui lòng thử lại sau.', 'OTP_RATE_LIMITED', 429, [], $request);
        }
        RateLimiter::hit($throttleKey, 60);
        $payload = $data;
        unset($payload['password_confirmation']);
        $this->otpService->issue($email, $payload);

        return ApiResponse::success([
            'email' => $email,
            'verification_required' => true,
            'expires_in' => 600,
        ], 'Mã OTP đã được gửi.', 202, $request);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();
        $pending = $this->otpService->pending($email);

        if (! is_array($pending)) {
            return ApiResponse::error('Yêu cầu đăng ký không tồn tại hoặc đã hết hạn.', 'OTP_EXPIRED', 410, [], $request);
        }

        if (($pending['attempts'] ?? 0) >= 5) {
            $this->otpService->forget($email);

            return ApiResponse::error('Bạn đã nhập sai OTP quá số lần cho phép.', 'OTP_LOCKED', 429, [], $request);
        }

        if (! Hash::check($request->string('code')->toString(), $pending['otp_hash'])) {
            $pending['attempts'] = (int) ($pending['attempts'] ?? 0) + 1;
            $this->otpService->saveAttempts($email, $pending);

            return ApiResponse::error('Mã OTP không đúng.', 'OTP_INVALID', 422, [], $request);
        }

        try {
            $user = $this->authService->createCitizen($pending['payload']);
        } catch (\Throwable $exception) {
            report($exception);

            return ApiResponse::error('Không thể tạo tài khoản với thông tin này.', 'REGISTRATION_CONFLICT', 409, [], $request);
        }

        $this->otpService->forget($email);
        RateLimiter::clear($this->throttleKey('register', $email, $request));
        app(AuditLogger::class)->log('auth.register_verified', [
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'request_id' => $request->attributes->get('request_id'),
        ]);

        return $this->tokenResponse($user, 'Đăng ký thành công.', 201, $request);
    }

    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $email = $request->string('email')->toString();
        $throttleKey = $this->throttleKey('register', $email, $request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return ApiResponse::error('Bạn đã yêu cầu quá nhiều mã OTP. Vui lòng thử lại sau.', 'OTP_RATE_LIMITED', 429, [], $request);
        }

        try {
            $this->otpService->resend($email);
        } catch (\App\Exceptions\ApiException $exception) {
            return ApiResponse::error($exception->getMessage(), $exception->errorCode, $exception->status, [], $request);
        }
        RateLimiter::hit($throttleKey, 60);

        return ApiResponse::success([
            'email' => $email,
            'verification_required' => true,
            'expires_in' => 600,
        ], 'Mã OTP mới đã được gửi.', 202, $request);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $throttleKey = $this->throttleKey('login', $request->string('email')->toString(), $request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return ApiResponse::error('Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau.', 'AUTH_RATE_LIMITED', 429, [], $request);
        }

        $token = auth('api')
            ->claims(['token_type' => 'access'])
            ->setTTL((int) config('jwt.ttl', 60))
            ->attempt($request->only('email', 'password'));
        if (! $token) {
            RateLimiter::hit($throttleKey, 60);

            return ApiResponse::error('Email hoặc mật khẩu không đúng.', 'AUTH_INVALID_CREDENTIALS', 401, [], $request);
        }

        RateLimiter::clear($throttleKey);
        /** @var Nguoi $user */
        $user = auth('api')->user();
        auth('api')->logout();

        $this->otpService->issueLogin($user);
        app(AuditLogger::class)->log('auth.login_challenge', [
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'request_id' => $request->attributes->get('request_id'),
        ]);

        return ApiResponse::success([
            'email' => $user->email,
            'verification_required' => true,
            'expires_in' => 600,
        ], 'Mã xác thực đăng nhập đã được gửi tới email của bạn.', 202, $request);
    }

    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ], [
            'email.required' => 'Vui lòng cung cấp email.',
            'code.required' => 'Vui lòng nhập mã OTP.',
            'code.size' => 'Mã OTP gồm 6 chữ số.',
        ]);

        try {
            $user = $this->otpService->verifyLogin($data['email'], $data['code']);
        } catch (\App\Exceptions\ApiException $e) {
            return ApiResponse::error($e->getMessage(), $e->errorCode, $e->status, [], $request);
        }

        app(AuditLogger::class)->log('auth.login', [
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'request_id' => $request->attributes->get('request_id'),
        ]);

        return $this->tokenResponse($user, 'Đăng nhập thành công.', 200, $request);
    }

    public function resendLoginOtp(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $throttleKey = $this->throttleKey('login_otp', $data['email'], $request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return ApiResponse::error('Bạn đã yêu cầu gửi lại OTP quá nhiều lần. Vui lòng thử lại sau.', 'OTP_RATE_LIMITED', 429, [], $request);
        }

        try {
            $this->otpService->resendLogin($data['email']);
        } catch (\App\Exceptions\ApiException $e) {
            return ApiResponse::error($e->getMessage(), $e->errorCode, $e->status, [], $request);
        }
        RateLimiter::hit($throttleKey, 60);

        return ApiResponse::success([
            'email' => $data['email'],
            'verification_required' => true,
            'expires_in' => 600,
        ], 'Mã OTP mới đã được gửi.', 202, $request);
    }

    public function refresh(Request $request): JsonResponse
    {
        $token = trim((string) $request->input('refresh_token', ''));
        if ($token === '') {
            $token = (string) ($request->bearerToken() ?? '');
        }

        if ($token === '') {
            return ApiResponse::error('Vui lòng cung cấp refresh token.', 'REFRESH_TOKEN_REQUIRED', 401, [], $request);
        }

        try {
            $claims = $this->jwtProvider->decode($token);
            $tokenType = $claims['token_type'] ?? null;

            if ($tokenType !== null && $tokenType !== 'refresh') {
                return ApiResponse::error('Endpoint này chỉ chấp nhận refresh token.', 'REFRESH_TOKEN_REQUIRED', 401, [], $request);
            }

            if ($tokenType === null) {
                $rotatedLegacyToken = auth('api')
                    ->claims(['token_type' => 'refresh'])
                    ->setTTL((int) config('jwt.refresh_ttl', 20160))
                    ->setToken($token)
                    ->refresh();
                $user = auth('api')->setToken($rotatedLegacyToken)->user();
                auth('api')->setToken($rotatedLegacyToken)->logout();
            } else {
                auth('api')->setToken($token)->getPayload();
                $user = auth('api')->setToken($token)->user();
                auth('api')->setToken($token)->logout();
            }

            if (! $user instanceof Nguoi) {
                return ApiResponse::error('Không tìm thấy tài khoản của refresh token.', 'TOKEN_INVALID', 401, [], $request);
            }

            $this->jwtManager->setRefreshFlow(false);

            return $this->tokenResponse($user, 'Token đã được làm mới.', 200, $request);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception) {
            return ApiResponse::error('Refresh token đã hết hạn. Vui lòng đăng nhập lại.', 'REFRESH_TOKEN_EXPIRED', 401, [], $request);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $exception) {
            return ApiResponse::error('Token không hợp lệ hoặc đã bị thu hồi.', 'TOKEN_INVALID', 401, [], $request);
        } finally {
            $this->jwtManager->setRefreshFlow(false);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $accessToken = (string) ($request->bearerToken() ?? '');
        $refreshToken = trim((string) $request->input('refresh_token', ''));

        if ($refreshToken !== '') {
            try {
                $claims = $this->jwtProvider->decode($refreshToken);
                $tokenType = $claims['token_type'] ?? null;
                $sameUser = (string) ($claims['sub'] ?? '') === (string) $user->getJWTIdentifier();

                if (($tokenType === null || $tokenType === 'refresh') && $sameUser && ($accessToken === '' || ! hash_equals($accessToken, $refreshToken))) {
                    JWTAuth::setToken($refreshToken)->invalidate();
                }
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $exception) {
            }
        }

        auth('api')->setToken($accessToken)->logout();

        return ApiResponse::success(null, 'Đăng xuất thành công.', 200, $request);
    }

    public function me(): JsonResponse
    {
        return ApiResponse::success(new UserResource(auth('api')->user()));
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        /** @var Nguoi|null $user */
        $user = auth('api')->user();
        if (! $user) {
            return ApiResponse::error('Chưa đăng nhập.', 'UNAUTHORIZED', 401, [], $request);
        }

        if (! Hash::check($data['current_password'], (string) $user->password)) {
            return ApiResponse::error('Mật khẩu hiện tại không chính xác.', 'INVALID_PASSWORD', 422, [], $request);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        app(AuditLogger::class)->log('auth.password_changed', [
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'request_id' => $request->attributes->get('request_id'),
        ]);

        return ApiResponse::success(null, 'Đổi mật khẩu thành công.', 200, $request);
    }

    private function tokenResponse(
        Nguoi $user,
        string $message,
        int $status = 200,
        ?Request $request = null,
        ?string $refreshToken = null,
    ): JsonResponse {
        $accessToken = auth('api')
            ->claims(['token_type' => 'access'])
            ->setTTL((int) config('jwt.ttl', 60))
            ->login($user);
        $refreshToken ??= auth('api')
            ->claims(['token_type' => 'refresh'])
            ->setTTL((int) config('jwt.refresh_ttl', 20160))
            ->login($user);

        return ApiResponse::success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => (int) config('jwt.ttl', 20) * 60,
            'refresh_expires_in' => (int) config('jwt.refresh_ttl', 10080) * 60,
            'user' => $user ? new UserResource($user) : null,
        ], $message, $status, $request);
    }

    private function throttleKey(string $scope, string $email, $request): string
    {
        return 'api:'.$scope.':'.hash('sha256', Str::lower($email).'|'.$request->ip());
    }
}

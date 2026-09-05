<?php

namespace App\Http\Controllers\Api\V1\Citizen;

use App\Exceptions\PasswordChangeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Citizen\RequestPasswordOtpRequest;
use App\Http\Requests\Api\V1\Citizen\ResendPasswordOtpRequest;
use App\Http\Requests\Api\V1\Citizen\UpdateIdentityRequest;
use App\Http\Requests\Api\V1\Citizen\UpdateProfileRequest;
use App\Http\Requests\Api\V1\Citizen\VerifyPasswordOtpRequest;
use App\Http\Resources\Api\V1\Citizen\ProfileResource;
use App\Models\Nguoi;
use App\Services\Citizen\CitizenProfileApiService;
use App\Services\PasswordChangeService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private readonly CitizenProfileApiService $profiles,
        private readonly PasswordChangeService $passwords,
    ) {}

    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new ProfileResource($this->profiles->profile($this->user($request))),
            'Thông tin hồ sơ cá nhân.',
            200,
            $request,
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        return ApiResponse::success(
            new ProfileResource($this->profiles->updateProfile($this->user($request), $request->validated())),
            'Đã cập nhật hồ sơ cá nhân.',
            200,
            $request,
        );
    }

    public function identity(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new ProfileResource($this->profiles->profile($this->user($request))),
            'Thông tin định danh.',
            200,
            $request,
        );
    }

    public function updateIdentity(UpdateIdentityRequest $request): JsonResponse
    {
        return ApiResponse::success(
            new ProfileResource($this->profiles->updateIdentity($this->user($request), $request->validated())),
            'Đã cập nhật thông tin định danh.',
            200,
            $request,
        );
    }

    public function requestPasswordOtp(RequestPasswordOtpRequest $request): JsonResponse
    {
        try {
            $challenge = $this->passwords->beginApi(
                $this->citizen($request),
                $request->string('current_password')->toString(),
            );
        } catch (PasswordChangeException $exception) {
            return $this->passwordError($exception, $request);
        }

        return ApiResponse::success($challenge, 'Mã OTP đã được gửi tới email của bạn.', 202, $request);
    }

    public function verifyPasswordOtp(VerifyPasswordOtpRequest $request): JsonResponse
    {
        try {
            $this->passwords->verifyApi(
                $this->citizen($request),
                $request->string('challenge_id')->toString(),
                $request->string('code')->toString(),
                $request->string('new_password')->toString(),
            );
        } catch (PasswordChangeException $exception) {
            return $this->passwordError($exception, $request);
        }

        return ApiResponse::success(['password_changed' => true], 'Đổi mật khẩu thành công.', 200, $request);
    }

    public function resendPasswordOtp(ResendPasswordOtpRequest $request): JsonResponse
    {
        try {
            $challenge = $this->passwords->resendApi(
                $this->citizen($request),
                $request->string('challenge_id')->toString(),
            );
        } catch (PasswordChangeException $exception) {
            return $this->passwordError($exception, $request);
        }

        return ApiResponse::success($challenge, 'Mã OTP mới đã được gửi.', 202, $request);
    }

    private function passwordError(PasswordChangeException $exception, Request $request): JsonResponse
    {
        return ApiResponse::error(
            $exception->getMessage(),
            $exception->errorCode,
            $exception->status,
            [],
            $request,
        );
    }

    private function user(Request $request): Nguoi
    {
        /** @var Nguoi $user */
        $user = $request->user('api');

        return $user;
    }

    private function citizen(Request $request): Nguoi
    {
        return $this->profiles->profile($this->user($request));
    }
}

<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    public function issue(string $email, array $payload): void
    {
        $code = (string) random_int(100000, 999999);
        unset($payload['password_confirmation']);
        $payload['password'] = Hash::make($payload['password']);

        Cache::put($this->key($email), [
            'payload' => $payload,
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));

        Mail::to($email)->send(new \App\Mail\OtpCodeMail($code));
    }

    public function pending(string $email): ?array
    {
        $pending = Cache::get($this->key($email));

        return is_array($pending) ? $pending : null;
    }

    public function resend(string $email): void
    {
        $pending = $this->pending($email);
        if (! is_array($pending)) {
            throw new \App\Exceptions\ApiException('Yêu cầu đăng ký không tồn tại hoặc đã hết hạn.', 'OTP_EXPIRED', 410);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->key($email), [
            'payload' => $pending['payload'],
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));

        Mail::to($email)->send(new \App\Mail\OtpCodeMail($code));
    }

    public function saveAttempts(string $email, array $pending): void
    {
        Cache::put($this->key($email), $pending, now()->addMinutes(10));
    }

    public function forget(string $email): void
    {
        Cache::forget($this->key($email));
    }

    public function issueLogin(\App\Models\Nguoi $user): void
    {
        $code = (string) random_int(100000, 999999);
        $email = $user->email;

        Cache::put($this->loginKey($email), [
            'user_id' => $user->getKey(),
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));

        Mail::to($email)->send(new \App\Mail\LoginOtpMail($code, $user->hoTen));
    }

    public function pendingLogin(string $email): ?array
    {
        $pending = Cache::get($this->loginKey($email));

        return is_array($pending) ? $pending : null;
    }

    public function resendLogin(string $email): void
    {
        $pending = $this->pendingLogin($email);
        if (! is_array($pending)) {
            throw new \App\Exceptions\ApiException('Phiên đăng nhập không tồn tại hoặc đã hết hạn.', 'LOGIN_OTP_EXPIRED', 410);
        }

        $user = \App\Models\Nguoi::find($pending['user_id']);
        $code = (string) random_int(100000, 999999);

        Cache::put($this->loginKey($email), [
            'user_id' => $pending['user_id'],
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(10));

        Mail::to($email)->send(new \App\Mail\LoginOtpMail($code, $user?->hoTen));
    }

    public function verifyLogin(string $email, string $code): \App\Models\Nguoi
    {
        $pending = $this->pendingLogin($email);
        if (! is_array($pending)) {
            throw new \App\Exceptions\ApiException('Phiên đăng nhập không tồn tại hoặc đã hết hạn.', 'LOGIN_OTP_EXPIRED', 410);
        }

        if (($pending['attempts'] ?? 0) >= 5) {
            $this->forgetLogin($email);

            throw new \App\Exceptions\ApiException('Bạn đã nhập sai OTP quá số lần cho phép.', 'LOGIN_OTP_LOCKED', 429);
        }

        if (! Hash::check($code, $pending['otp_hash'])) {
            $pending['attempts'] = (int) ($pending['attempts'] ?? 0) + 1;
            Cache::put($this->loginKey($email), $pending, now()->addMinutes(10));

            throw new \App\Exceptions\ApiException('Mã OTP không đúng.', 'LOGIN_OTP_INVALID', 422);
        }

        $this->forgetLogin($email);

        return \App\Models\Nguoi::findOrFail($pending['user_id']);
    }

    public function forgetLogin(string $email): void
    {
        Cache::forget($this->loginKey($email));
    }

    private function key(string $email): string
    {
        return 'api:registration:'.hash('sha256', Str::lower($email));
    }

    private function loginKey(string $email): string
    {
        return 'api:login:'.hash('sha256', Str::lower($email));
    }
}

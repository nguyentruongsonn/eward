<?php

namespace App\Services;

use App\Exceptions\PasswordChangeException;
use App\Mail\PasswordChangeOtpMail;
use App\Models\Nguoi;
use App\Models\PasswordChangeOtp;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordChangeService
{
    private const OTP_TTL_MINUTES = 10;

    private const MAX_ATTEMPTS = 5;

    public function begin(Nguoi $user, string $currentPassword, Session $session): void
    {
        $challenge = $this->beginApi($user, $currentPassword);
        $session->put('pending_password_change', ['email' => (string) $user->email, 'challenge_id' => $challenge['challenge_id']]);
    }

    public function pendingEmail(Nguoi $user, Session $session): ?string
    {
        $pending = $session->get('pending_password_change');
        $email = is_array($pending) ? trim((string) ($pending['email'] ?? '')) : '';
        if ($email === '' || ! hash_equals($email, trim((string) $user->email))) {
            $session->forget('pending_password_change');

            return null;
        }

        return $email;
    }

    public function verify(Nguoi $user, string $code, string $newPassword, Session $session): void
    {
        $pending = $session->get('pending_password_change');
        $challengeId = is_array($pending) ? (string) ($pending['challenge_id'] ?? '') : '';
        if ($challengeId === '') {
            throw new PasswordChangeException('code', 'Phiên đổi mật khẩu đã hết hạn.');
        }
        $this->verifyApi($user, $challengeId, $code, $newPassword);
        $session->forget('pending_password_change');
    }

    public function resend(Nguoi $user, Session $session): void
    {
        $pending = $session->get('pending_password_change');
        $challengeId = is_array($pending) ? (string) ($pending['challenge_id'] ?? '') : '';
        if ($challengeId === '') {
            throw new PasswordChangeException('code', 'Phiên đổi mật khẩu đã hết hạn.');
        }
        $this->resendApi($user, $challengeId);
    }

    public function beginApi(Nguoi $user, string $currentPassword): array
    {
        $email = $this->verifiedEmail($user, $currentPassword, true);
        $challengeId = (string) Str::uuid();
        $code = $this->newCode();

        DB::transaction(function () use ($user, $email, $challengeId, $code): void {
            PasswordChangeOtp::query()->where('nguoi_dung_id', $user->getKey())->whereNotNull('challenge_id')->delete();
            PasswordChangeOtp::query()->create([
                'email' => $email,
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                'attempts' => 0,
                'challenge_id' => $challengeId,
                'nguoi_dung_id' => $user->getKey(),
            ]);
        });

        try {
            $this->sendOtp($code, $email, 'current_password');
        } catch (PasswordChangeException $exception) {
            PasswordChangeOtp::query()->where('challenge_id', $challengeId)->delete();
            throw $exception;
        }

        return [
            'challenge_id' => $challengeId,
            'masked_email' => $this->maskEmail($email),
            'verification_required' => true,
            'expires_in' => self::OTP_TTL_MINUTES * 60,
        ];
    }

    public function verifyApi(Nguoi $user, string $challengeId, string $code, string $newPassword): void
    {
        $result = DB::transaction(function () use ($user, $challengeId, $code, $newPassword): string {
            $otp = PasswordChangeOtp::query()->where('challenge_id', $challengeId)->where('nguoi_dung_id', $user->getKey())->lockForUpdate()->first();
            if (! $otp) {
                return 'missing';
            }
            if (Carbon::now()->greaterThan($otp->expires_at)) {
                $otp->delete();

                return 'expired';
            }
            if ((int) $otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->delete();

                return 'locked';
            }
            if (! Hash::check($code, (string) $otp->code)) {
                $otp->increment('attempts');

                return 'invalid';
            }

            $this->replacePassword($user, $newPassword);
            $otp->delete();

            return 'valid';
        });

        if ($result !== 'valid') {
            throw $this->apiFailure($result);
        }
    }

    public function resendApi(Nguoi $user, string $challengeId): array
    {
        $code = $this->newCode();
        $result = DB::transaction(function () use ($user, $challengeId, $code): array {
            $otp = PasswordChangeOtp::query()->where('challenge_id', $challengeId)->where('nguoi_dung_id', $user->getKey())->lockForUpdate()->first();
            if (! $otp) {
                return ['state' => 'missing'];
            }
            if (Carbon::now()->greaterThan($otp->expires_at)) {
                $otp->delete();

                return ['state' => 'expired'];
            }
            if ((int) $otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->delete();

                return ['state' => 'locked'];
            }

            $otp->fill([
                'code' => Hash::make($code),
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                'attempts' => 0,
            ])->save();

            return ['state' => 'valid', 'email' => (string) $otp->email];
        });

        if ($result['state'] !== 'valid') {
            throw $this->apiFailure($result['state']);
        }

        try {
            $this->sendOtp($code, $result['email'], 'challenge_id');
        } catch (PasswordChangeException $exception) {
            PasswordChangeOtp::query()->where('challenge_id', $challengeId)->where('nguoi_dung_id', $user->getKey())->delete();
            throw $exception;
        }

        return ['challenge_id' => $challengeId, 'expires_in' => self::OTP_TTL_MINUTES * 60];
    }

    private function verifiedEmail(Nguoi $user, string $currentPassword, bool $api = false): string
    {
        if (! Hash::check($currentPassword, (string) $user->password)) {
            throw new PasswordChangeException('current_password', 'Mật khẩu hiện tại không đúng.', null, $api ? 'PASSWORD_CURRENT_INVALID' : 'PASSWORD_CHANGE_ERROR');
        }
        $email = trim((string) $user->email);
        if ($email === '') {
            throw new PasswordChangeException('current_password', 'Không tìm thấy email của bạn.', null, $api ? 'PASSWORD_EMAIL_MISSING' : 'PASSWORD_CHANGE_ERROR');
        }

        return $email;
    }

    private function newCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function replacePassword(Nguoi $user, string $newPassword): void
    {
        $account = Nguoi::query()->whereKey($user->getKey())->lockForUpdate()->firstOrFail();
        $hashedPassword = Hash::make($newPassword);
        $account->password = $hashedPassword;
        $account->save();
        if ($account->user) {
            $account->user->password = $hashedPassword;
            $account->user->save();
        }
    }

    private function apiFailure(string $result): PasswordChangeException
    {
        return match ($result) {
            'missing' => new PasswordChangeException('challenge_id', 'Phiên đổi mật khẩu không tồn tại.', null, 'PASSWORD_OTP_NOT_FOUND', 404),
            'expired' => new PasswordChangeException('challenge_id', 'Mã OTP đã hết hạn. Vui lòng yêu cầu lại.', null, 'PASSWORD_OTP_EXPIRED', 410),
            'locked' => new PasswordChangeException('code', 'OTP đã vượt quá số lần thử. Vui lòng yêu cầu lại.', null, 'PASSWORD_OTP_LOCKED', 429),
            default => new PasswordChangeException('code', 'Mã OTP không đúng.', null, 'PASSWORD_OTP_INVALID'),
        };
    }

    private function sendOtp(string $code, string $email, string $field): void
    {
        try {
            Mail::to($email)->send(new PasswordChangeOtpMail($code));
        } catch (\Throwable $exception) {
            $message = 'Không gửi được email OTP. Vui lòng thử lại sau.';
            if (app()->environment('local')) {
                $message .= ' Lỗi: '.$exception->getMessage();
            }
            throw new PasswordChangeException($field, $message, $exception);
        }
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) < 2) {
            return $email;
        }
        $name = $parts[0];
        $len = strlen($name);
        $masked = $len <= 2 ? $name[0].'***' : substr($name, 0, 2).str_repeat('*', max(1, $len - 3)).substr($name, -1);

        return $masked.'@'.$parts[1];
    }
}

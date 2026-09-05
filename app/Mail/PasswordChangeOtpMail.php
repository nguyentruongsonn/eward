<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangeOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Mã xác thực đổi mật khẩu (OTP)')
            ->view('emails.password-change-otp')
            ->with([
                'code' => $this->code,
            ]);
    }
}

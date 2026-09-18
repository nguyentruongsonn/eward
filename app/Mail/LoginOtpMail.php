<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $code;

    public ?string $userName;

    public function __construct(string $code, ?string $userName = null)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Mã xác thực đăng nhập (OTP) - Cổng Dịch vụ công e-Ward')
            ->view('emails.login-otp')
            ->with([
                'code' => $this->code,
                'userName' => $this->userName,
            ]);
    }
}

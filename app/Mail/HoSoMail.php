<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class HoSoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $hoSo;

    public $subject;

    public $content;

    public $loaiMail;

    public $messageId;

    public function __construct($hoSo, $subject, $content, $loaiMail = 'lien_lac', ?string $messageId = null)
    {
        $this->hoSo = $hoSo;
        $this->subject = $subject;
        $this->content = $content;
        $this->loaiMail = $loaiMail;
        $this->messageId = $messageId;
    }

    public function headers(): Headers
    {
        return new Headers(messageId: $this->messageId);
    }

    public function build()
    {
        $replyTo = config('mail.from.address');

        return $this->subject($this->subject)
            ->replyTo($replyTo)
            ->view('emails.hoso')
            ->with([
                'hoSo' => $this->hoSo,
                'content' => $this->content,
                'loaiMail' => $this->loaiMail,
            ]);
    }
}

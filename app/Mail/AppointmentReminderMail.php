<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lichHen;

    public $tthc;

    public $nguoi;

    public function __construct($lichHen, $tthc, $nguoi)
    {
        $this->lichHen = $lichHen;
        $this->tthc = $tthc;
        $this->nguoi = $nguoi;
    }

    public function build()
    {
        return $this->subject('Nhắc nhở lịch hẹn - '.($this->tthc->tenTTHC ?? 'Lịch hẹn'))
            ->view('emails.appointment-reminder')
            ->with([
                'lichHen' => $this->lichHen,
                'tthc' => $this->tthc,
                'nguoi' => $this->nguoi,
            ]);
    }
}

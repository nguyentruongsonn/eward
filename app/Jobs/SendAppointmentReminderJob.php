<?php

namespace App\Jobs;

use App\Mail\AppointmentReminderMail;
use App\Models\LichHen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly string $appointmentId) {}

    public function handle(): void
    {
        Cache::lock('appointment-reminder:'.$this->appointmentId, 300)->block(10, function (): void {
            $appointment = LichHen::query()->with(['congdan.nguoi', 'tthc'])->find($this->appointmentId);
            if (! $appointment || $appointment->reminder_sent_at || in_array($appointment->trangThai, ['Đã hủy', 'Hoàn thành', 'Không đến'], true)) {
                return;
            }

            $email = $appointment->congdan?->nguoi?->email;
            if (! $email) {
                return;
            }

            Mail::to($email)->send(new AppointmentReminderMail($appointment, $appointment->tthc, $appointment->congdan->nguoi));
            $appointment->forceFill(['reminder_sent_at' => now()])->save();
        });
    }
}

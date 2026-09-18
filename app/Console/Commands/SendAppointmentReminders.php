<?php

namespace App\Console\Commands;

use App\Services\Appointments\AppointmentReminderService;
use App\Services\Appointments\AppointmentService;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Gửi email nhắc nhở cho các lịch hẹn sắp tới trong vòng 24 giờ';

    public function handle(AppointmentReminderService $reminders, AppointmentService $appointments): int
    {
        $this->info('Bắt đầu gửi email nhắc nhở lịch hẹn...');

        $markedNoShows = $appointments->markNoShows();
        $queued = $reminders->dispatchUpcoming(24);

        $this->info("Đã đánh dấu {$markedNoShows} lịch hẹn không đến.");
        $this->info("Đã đưa {$queued} email nhắc lịch vào hàng đợi.");

        return 0;
    }
}

<?php

namespace App\Services\Appointments;

use App\Enums\AppointmentStatus;
use App\Exceptions\ApiException;
use App\Jobs\SendAppointmentReminderJob;
use App\Models\LichHen;
use Carbon\CarbonImmutable;

class AppointmentReminderService
{
    public function dispatchUpcoming(int $hours = 24): int
    {
        $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $now = CarbonImmutable::now($timezone);
        $until = $now->addHours($hours);
        $active = [
            AppointmentStatus::Booked->value,
            AppointmentStatus::Waiting->value,
            AppointmentStatus::Processing->value,
        ];
        $appointments = LichHen::query()
            ->whereIn('trangThai', $active)
            ->whereBetween('thoiGianHen', [$now, $until])
            ->whereNull('reminder_sent_at')
            ->get(['id']);

        foreach ($appointments as $appointment) {
            SendAppointmentReminderJob::dispatch((string) $appointment->getKey());
        }

        return $appointments->count();
    }

    public function dispatchManual(string $appointmentId): LichHen
    {
        $appointment = LichHen::query()
            ->with(['congdan.nguoi', 'tthc'])
            ->find($appointmentId);
        if (! $appointment) {
            throw new ApiException('Không tìm thấy lịch hẹn.', 'APPOINTMENT_NOT_FOUND', 404);
        }

        $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $now = CarbonImmutable::now($timezone);
        $scheduledAt = CarbonImmutable::parse($appointment->thoiGianHen)->setTimezone($timezone);
        $hoursUntil = $now->diffInHours($scheduledAt, false);
        if ($hoursUntil < 0 || $hoursUntil > 24) {
            throw new ApiException('Chỉ có thể gửi mail nhắc cho lịch hẹn trong vòng 24 giờ tới.', 'APPOINTMENT_REMINDER_WINDOW', 422);
        }

        if ($appointment->reminder_sent_at) {
            throw new ApiException('Mail nhắc đã được gửi trước đó.', 'APPOINTMENT_REMINDER_ALREADY_SENT', 409);
        }

        if (! in_array($appointment->trangThai, [
            AppointmentStatus::Booked->value,
            AppointmentStatus::Waiting->value,
            AppointmentStatus::Processing->value,
        ], true)) {
            throw new ApiException('Trạng thái lịch hẹn không cho phép gửi mail nhắc.', 'APPOINTMENT_REMINDER_STATUS', 409);
        }

        if (! $appointment->congdan?->nguoi?->email) {
            throw new ApiException('Không tìm thấy email người dùng.', 'APPOINTMENT_EMAIL_NOT_FOUND', 422);
        }

        SendAppointmentReminderJob::dispatch((string) $appointment->getKey());

        return $appointment;
    }
}

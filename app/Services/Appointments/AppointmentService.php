<?php

namespace App\Services\Appointments;

use App\Enums\AppointmentStatus;
use App\Enums\HoSoStatus;
use App\Exceptions\ApiException;
use App\Models\HoSoXuLy;
use App\Models\LichHen;
use App\Models\Nguoi;
use App\Models\TTHC;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    private const ACTIVE_STATUSES = [
        AppointmentStatus::Booked->value,
        AppointmentStatus::Waiting->value,
        AppointmentStatus::Processing->value,
    ];

    public function listForUser(Nguoi $user, int $perPage = 15, array $filters = [], ?int $page = null): LengthAwarePaginator
    {
        $query = LichHen::query()->whereIn('IDCD', $user->congDan()->select('IDCD'))->with(['tthc', 'quaylamviec']);
        if (($status = trim((string) ($filters['trang_thai'] ?? ''))) !== '') {
            $query->where('trangThai', $status);
        }
        if (($fromDate = trim((string) ($filters['from_date'] ?? ''))) !== '') {
            $query->whereDate('thoiGianHen', '>=', $fromDate);
        }
        if (($toDate = trim((string) ($filters['to_date'] ?? ''))) !== '') {
            $query->whereDate('thoiGianHen', '<=', $toDate);
        }

        return $query->orderByDesc('thoiGianHen')->paginate(max(1, min($perPage, 100)), ['*'], 'page', $page === null ? null : max(1, $page))->withQueryString();
    }

    public function findForUser(Nguoi $user, string $appointmentId): LichHen
    {
        return LichHen::query()->whereKey($appointmentId)->whereIn('IDCD', $user->congDan()->select('IDCD'))->with(['tthc', 'quaylamviec', 'congdan'])->firstOrFail();
    }

    public function book(Nguoi $user, int $procedureId, string $scheduledAt): LichHen
    {
        $when = CarbonImmutable::createFromFormat('Y-m-d H:i', $scheduledAt, config('app.timezone', 'Asia/Ho_Chi_Minh'));
        if (! $when || $when->isPast() || $when->isWeekend()) {
            throw new ApiException('Thời gian hẹn phải là ngày làm việc trong tương lai.', 'APPOINTMENT_TIME_INVALID', 422);
        }
        $citizen = $user->congDan()->firstOrCreate([]);

        return DB::transaction(function () use ($procedureId, $when, $citizen): LichHen {
            $procedure = TTHC::query()->whereKey($procedureId)->firstOrFail();
            if ($procedure->trangThai !== null && $procedure->trangThai !== 'Công khai') {
                throw new ApiException('Thủ tục hiện không công khai.', 'PROCEDURE_UNAVAILABLE', 409);
            }
            $start = $when->startOfHour();
            $end = $when->endOfHour();
            $slot = LichHen::query()->where('maTTHC', $procedureId)->whereBetween('thoiGianHen', [$start, $end])->whereIn('trangThai', self::ACTIVE_STATUSES)->lockForUpdate()->get();

            if ($slot->contains(fn (LichHen $appointment): bool => $appointment->IDCD === $citizen->getKey())) {
                throw new ApiException('Bạn đã có lịch trong khung giờ này.', 'APPOINTMENT_DUPLICATE', 409);
            }
            if ($slot->count() >= 6) {
                throw new ApiException('Khung giờ đã đủ số lượng.', 'APPOINTMENT_CAPACITY_REACHED', 409);
            }

            return LichHen::create([
                'IDCD' => $citizen->getKey(),
                'maTTHC' => $procedureId,
                'thoiGianHen' => $when,
                'trangThai' => AppointmentStatus::Booked,
            ])->load('tthc');
        });
    }

    public function cancel(Nguoi $user, string $appointmentId): LichHen
    {
        return DB::transaction(function () use ($user, $appointmentId): LichHen {
            $appointment = LichHen::query()->whereKey($appointmentId)->lockForUpdate()->firstOrFail();
            abort_unless($user->congDan()->where('IDCD', $appointment->IDCD)->exists(), 403);
            if (! in_array($appointment->trangThai, self::ACTIVE_STATUSES, true)) {
                throw new ApiException('Lịch hẹn không thể hủy ở trạng thái hiện tại.', 'APPOINTMENT_CANCEL_INVALID', 409);
            }
            if ($appointment->thoiGianHen && $appointment->thoiGianHen->lte(now(config('app.timezone', 'Asia/Ho_Chi_Minh')))) {
                throw new ApiException('Không thể hủy lịch hẹn đã tới hoặc quá thời gian.', 'APPOINTMENT_CANCEL_EXPIRED', 409);
            }
            $appointment->update(['trangThai' => AppointmentStatus::Canceled]);

            return $appointment->fresh('tthc');
        });
    }

    public function checkIn(string $token, ?Nguoi $actor = null): LichHen
    {
        return DB::transaction(function () use ($token): LichHen {
            $appointment = LichHen::query()->where('checkin_token', trim($token))->lockForUpdate()->first();
            if (! $appointment) {
                throw new ApiException('Không tìm thấy lịch hẹn.', 'APPOINTMENT_NOT_FOUND', 404);
            }
            if ($appointment->trangThai === AppointmentStatus::Canceled->value) {
                throw new ApiException('Lịch hẹn đã bị hủy.', 'APPOINTMENT_CANCELED', 409);
            }
            if ($appointment->checkin_time !== null) {
                throw new ApiException('Lịch hẹn đã được check-in.', 'APPOINTMENT_ALREADY_CHECKED_IN', 409);
            }

            $now = CarbonImmutable::now(config('app.timezone', 'Asia/Ho_Chi_Minh'));
            $when = CarbonImmutable::instance($appointment->thoiGianHen)->setTimezone($now->getTimezone());
            if (! $when->isSameDay($now) || $when->lt($now) || $when->diffInHours($now) > 3) {
                throw new ApiException('Chỉ được check-in trong vòng 3 giờ trước giờ hẹn và trong ngày hẹn.', 'APPOINTMENT_CHECKIN_WINDOW_INVALID', 409);
            }

            $counter = DB::table('quaylamviec')->orderBy('maQuayLamViec')->get()->first(function ($counter) use ($appointment, $when): bool {
                $count = LichHen::query()
                    ->where('maTTHC', $appointment->maTTHC)
                    ->where('maQuayLamViec', $counter->maQuayLamViec)
                    ->whereBetween('thoiGianHen', [$when->startOfHour(), $when->endOfHour()])
                    ->whereIn('trangThai', self::ACTIVE_STATUSES)
                    ->count();

                return $count < 2;
            });

            if (! $counter) {
                throw new ApiException('Tất cả quầy trong khung giờ đã đủ chỗ.', 'APPOINTMENT_COUNTER_CAPACITY_REACHED', 409);
            }

            $queueNumber = LichHen::query()->where('maQuayLamViec', $counter->maQuayLamViec)->whereDate('thoiGianHen', $when->toDateString())->whereNotNull('checkin_time')->lockForUpdate()->count() + 1;
            $appointment->update([
                'maQuayLamViec' => $counter->maQuayLamViec,
                'checkin_time' => now(),
                'soThuTu' => $queueNumber,
                'trangThai' => AppointmentStatus::Waiting,
            ]);

            return $appointment->fresh(['tthc', 'quaylamviec']);
        });
    }

    public function updateStatus(string $appointmentId, string $status): array
    {
        $target = AppointmentStatus::tryFrom($status);
        if (! $target) {
            throw new ApiException('Trạng thái lịch hẹn không hợp lệ.', 'APPOINTMENT_STATUS_INVALID', 422);
        }

        return DB::transaction(function () use ($appointmentId, $target): array {
            $appointment = LichHen::query()->with(['congdan.nguoi', 'tthc'])->whereKey($appointmentId)->lockForUpdate()->first();
            if (! $appointment) {
                throw new ApiException('Không tìm thấy lịch hẹn.', 'APPOINTMENT_NOT_FOUND', 404);
            }
            if ($target === AppointmentStatus::Completed && $appointment->trangThai === $target->value) {
                throw new ApiException('Lịch hẹn đã hoàn thành.', 'APPOINTMENT_STATUS_INVALID', 409);
            }

            if ($target === AppointmentStatus::Completed && ($appointment->checkin_time === null || ! in_array($appointment->trangThai, [AppointmentStatus::Waiting->value, AppointmentStatus::Processing->value], true))) {
                throw new ApiException('Chỉ có thể hoàn thành lịch hẹn sau khi công dân đã check-in.', 'APPOINTMENT_CHECKIN_REQUIRED', 409, ['from' => $appointment->trangThai]);
            }

            $oldStatus = $appointment->trangThai;
            $appointment->trangThai = $target->value;
            $appointment->save();

            $application = $target === AppointmentStatus::Completed ? $this->createDirectApplication($appointment) : null;

            return [
                'appointment' => $appointment->fresh(['congdan.nguoi', 'tthc']),
                'application' => $application,
                'old_status' => $oldStatus,
            ];
        });
    }

    private function createDirectApplication(LichHen $appointment): HoSoXuLy
    {
        $citizen = $appointment->congdan;
        $user = $citizen?->nguoi;
        if (! $citizen || ! $user) {
            throw new ApiException('Không tìm thấy thông tin công dân của lịch hẹn.', 'APPOINTMENT_APPLICANT_NOT_FOUND', 422);
        }

        $date = now()->format('Ymd');
        do {
            $applicationId = 'HSXL_'.$citizen->getKey().'_'.$date.'_'.random_int(1000, 9999);
        } while (HoSoXuLy::query()->whereKey($applicationId)->exists());

        return HoSoXuLy::create([
            'maHSXL' => $applicationId,
            'IDCD' => $citizen->getKey(),
            'maTTHC' => $appointment->maTTHC,
            'maTrangThai' => HoSoStatus::DirectReception->value,
            'ngayNop' => now(),
            'ngayTiepNhan' => now(),
            'hinhThuc' => 'Nhận trực tiếp',
            'tenChuHoSo' => $user->hoTen,
            'email' => $user->email,
            'soDienThoai' => $user->soDienThoai,
            'dulieu' => [
                'appointment_id' => (string) $appointment->getKey(),
                'hinhThuc' => 'Nhận trực tiếp',
                'tenTTHC' => $appointment->tthc?->tenTTHC ?? '',
                'hoTen' => $user->hoTen, 'ngaySinh' => $user->ngaySinh, 'gioiTinh' => $user->gioiTinh,
                'cccd' => $user->maCCCD, 'email' => $user->email, 'soDienThoai' => $user->soDienThoai,
                'diaChi' => $user->noiThuongTru ?? $user->noiTamTru ?? '', 'ngayCap' => '', 'noiCap' => '',
            ],
            'lePhi' => 0,
            'donViXuLy' => 'UBND Phường Hòa Hải',
            'ghiChu' => 'Hồ sơ được tạo tự động từ lịch hẹn '.$appointment->maLichHen.' vào lúc '.now()->format('d/m/Y H:i'),
        ])->fresh(['tthc']);
    }

    public function availableSlots(int $procedureId, string $date): array
    {
        $day = CarbonImmutable::createFromFormat('Y-m-d', $date, config('app.timezone', 'Asia/Ho_Chi_Minh'));
        if (! $day || $day->isWeekend()) {
            return [];
        }

        $slots = [];
        foreach (array_merge(range(7, 11), range(13, 16)) as $hour) {
            $scheduled = $day->setTime($hour, 30);
            $count = LichHen::query()->where('maTTHC', $procedureId)->whereBetween('thoiGianHen', [$scheduled->startOfHour(), $scheduled->endOfHour()])->whereIn('trangThai', self::ACTIVE_STATUSES)->count();
            if ($count < 6 && $scheduled->isFuture()) {
                $slots[] = ['scheduled_at' => $scheduled->format('Y-m-d H:i'), 'remaining' => 6 - $count];
            }
        }

        return $slots;
    }

    public function markNoShows(?CarbonImmutable $now = null): int
    {
        $now ??= CarbonImmutable::now(config('app.timezone', 'Asia/Ho_Chi_Minh'));

        return LichHen::query()
            ->whereIn('trangThai', [AppointmentStatus::Booked->value, AppointmentStatus::Waiting->value])
            ->where('thoiGianHen', '<', $now)
            ->whereNull('checkin_time')
            ->update(['trangThai' => AppointmentStatus::NoShow->value]);
    }

    public function findByCheckinToken(string $token): ?LichHen
    {
        return LichHen::query()->with(['tthc', 'quaylamviec', 'congdan.nguoi'])->where('checkin_token', trim($token))->first();
    }
}

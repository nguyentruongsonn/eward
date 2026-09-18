<?php

namespace App\Enums;

enum Role: string
{
    case Citizen = 'Công dân/ Tổ chức';
    case OneStopOfficer = 'Cán bộ một cửa';
    case CaseOfficer = 'Cán bộ thụ lý';
    case Leader = 'Lãnh đạo';
    case Administrator = 'Quản trị viên';
    case Checkin = 'Checkin';

    public static function normalize(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        foreach (self::cases() as $role) {
            if ($role->value === $normalized) {
                return $role;
            }
        }

        return null;
    }

    public function isStaff(): bool
    {
        return in_array($this, [
            self::Administrator,
            self::Leader,
            self::OneStopOfficer,
            self::CaseOfficer,
        ], true);
    }

    public function isAdministrator(): bool
    {
        return $this === self::Administrator;
    }

    public function canAccessAppointments(): bool
    {
        return $this->isStaff() || $this === self::Checkin;
    }

    public function canUseGenericTransition(HoSoStatus $current, HoSoStatus $target): bool
    {
        return match ($this) {
            self::Administrator => true,
            self::OneStopOfficer => match ($current) {
                HoSoStatus::PendingReception => $target === HoSoStatus::Accepted,
                HoSoStatus::Completed => $target === HoSoStatus::Delivered,
                default => false,
            },
            default => false,
        };
    }

    public function storageValue(): string
    {
        return $this === self::CaseOfficer ? 'Cán bộ thụ lý ' : $this->value;
    }
}

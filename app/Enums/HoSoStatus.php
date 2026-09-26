<?php

namespace App\Enums;

enum HoSoStatus: int
{
    case PendingReception = 1;
    case Accepted = 2;
    case Rejected = 3;
    case Processing = 4;
    case SupplementRequested = 5;
    case Supplemented = 6;
    case WithdrawalRequested = 7;
    case Suspended = 8;
    case Completed = 9;
    case Delivered = 10;
    case DirectReception = 11;
    case ReworkRequested = 12;
    case PendingPayment = 13;

    public function label(): string
    {
        return match ($this) {
            self::PendingReception => 'Chờ tiếp nhận',
            self::Accepted => 'Được tiếp nhận',
            self::Rejected => 'Không được tiếp nhận',
            self::Processing => 'Đang xử lý',
            self::SupplementRequested => 'Yêu cầu bổ sung giấy tờ',
            self::Supplemented => 'Hồ sơ đã bổ sung giấy tờ',
            self::WithdrawalRequested => 'Công dân yêu cầu rút hồ sơ',
            self::Suspended => 'Dừng xử lý',
            self::Completed => 'Đã xử lý xong',
            self::Delivered => 'Đã trả kết quả',
            self::DirectReception => 'Nhận trực tiếp',
            self::ReworkRequested => 'Yêu cầu xử lý lại',
            self::PendingPayment => 'Chờ thanh toán',
        };
    }

    /** @return list<int> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PendingReception => [self::Accepted->value, self::Rejected->value, self::DirectReception->value, self::WithdrawalRequested->value],
            self::Accepted, self::DirectReception, self::ReworkRequested => [self::Processing->value, self::WithdrawalRequested->value],
            self::Processing => [self::SupplementRequested->value, self::Completed->value, self::Suspended->value, self::WithdrawalRequested->value, self::ReworkRequested->value],
            self::SupplementRequested => [self::Supplemented->value, self::WithdrawalRequested->value, self::Rejected->value],
            self::Supplemented => [self::Processing->value, self::SupplementRequested->value, self::WithdrawalRequested->value],
            self::Completed => [self::Delivered->value, self::ReworkRequested->value],
            self::Rejected, self::Suspended, self::Delivered => [],
            self::WithdrawalRequested => [self::Suspended->value],
            self::PendingPayment => [self::WithdrawalRequested->value],
        };
    }
}

<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Booked = 'Đã đặt lịch';
    case Waiting = 'Chờ đến';
    case Processing = 'Đang xử lý';
    case Completed = 'Hoàn thành';
    case Canceled = 'Đã hủy';
    case SupplementRequested = 'Yêu cầu bổ sung giấy tờ';
    case NoShow = 'Không đến';
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .info-box {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nhắc nhở lịch hẹn</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $nguoi->hoTen ?? 'Quý khách' }}</strong>,</p>
            
            <p>Hệ thống xin nhắc nhở bạn có lịch hẹn sắp tới:</p>
            
            <div class="info-box">
                <p><strong>Mã lịch hẹn:</strong> {{ $lichHen->maLichHen }}</p>
                <p><strong>Thủ tục hành chính:</strong> {{ $tthc->tenTTHC ?? '-' }}</p>
                <p><strong>Thời gian hẹn:</strong> {{ \Carbon\Carbon::parse($lichHen->thoiGianHen)->format('d/m/Y H:i') }}</p>
                <p><strong>Trạng thái:</strong> {{ $lichHen->trangThai }}</p>
            </div>
            
            <p><strong>Lưu ý:</strong> Lịch hẹn của bạn sẽ diễn ra trong vòng 24 giờ tới. Vui lòng chuẩn bị đầy đủ giấy tờ và đến đúng giờ hẹn.</p>
            
            @if($lichHen->checkin_token)
                <p>Bạn có thể xem QR code để cán bộ quét tại: 
                    <a href="{{ route('appointment.checkin', $lichHen->checkin_token) }}" target="_blank">
                        {{ route('appointment.checkin', $lichHen->checkin_token) }}
                    </a>
                </p>
            @endif
        </div>
        <div class="footer">
            <p>Đây là email tự động từ hệ thống E-Ward. Vui lòng không trả lời email này.</p>
        </div>
    </div>
</body>
</html>


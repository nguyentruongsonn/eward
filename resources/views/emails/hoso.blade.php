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
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>
                @if($loaiMail === 'bo_sung')
                    Yêu cầu bổ sung hồ sơ
                @elseif($loaiMail === 'tra_ket_qua')
                    Thông báo trả kết quả giải quyết hồ sơ
                @else
                    Thông báo về hồ sơ
                @endif
            </h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $hoSo->tenChuHoSo }}</strong>,</p>
            
            <div class="info-box">
                <p><strong>Mã hồ sơ:</strong> {{ $hoSo->maHSXL }}</p>
                <p><strong>Thủ tục hành chính:</strong> {{ $hoSo->tthc->tenTTHC ?? '-' }}</p>
                <p><strong>Ngày tiếp nhận:</strong> {{ $hoSo->ngayTiepNhan ? $hoSo->ngayTiepNhan->format('d/m/Y') : '-' }}</p>
            </div>
            
            @if($loaiMail === 'bo_sung')
                <div class="warning-box">
                    <p><strong>Yêu cầu bổ sung hồ sơ:</strong></p>
                    <p>Hồ sơ của bạn cần được bổ sung thêm thông tin hoặc tài liệu để tiếp tục xử lý.</p>
                </div>
            @elseif($loaiMail === 'tra_ket_qua')
                <div style="background: #e8f5e9; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 4px;">
                    <p><strong>Thông báo trả kết quả:</strong></p>
                    <p>Hồ sơ của bạn đã được giải quyết hoàn tất và sẵn sàng trả kết quả.</p>
                </div>
            @endif
            
            <div style="background: white; padding: 15px; margin: 15px 0; border-radius: 4px;">
                {!! nl2br(e($content)) !!}
            </div>
            
            <p>Vui lòng kiểm tra và thực hiện theo hướng dẫn. Nếu có thắc mắc, vui lòng liên hệ với Bộ phận Một cửa để được hỗ trợ.</p>
        </div>
        <div class="footer">
            <p>Đây là email tự động từ hệ thống E-Ward. Vui lòng không trả lời email này.</p>
        </div>
    </div>
</body>
</html>


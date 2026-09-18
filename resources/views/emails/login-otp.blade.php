<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Mã xác thực đăng nhập OTP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; padding: 0; background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #004b87 0%, #007bff 100%); padding: 30px 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 1px;">
                                CỔNG DỊCH VỤ CÔNG TRỰC TUYẾN
                            </h1>
                            <p style="margin: 8px 0 0 0; color: #e0f2fe; font-size: 13px;">
                                Ủy ban nhân dân xã ABC
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 40px 30px 40px;">
                            <p style="margin: 0 0 15px 0; color: #1e293b; font-size: 16px; font-weight: 600;">
                                Xin chào {{ $userName ? $userName : 'Quý công dân / Cán bộ' }},
                            </p>
                            <p style="margin: 0 0 20px 0; color: #475569; font-size: 14px; line-height: 1.6;">
                                Hệ thống vừa nhận được yêu cầu đăng nhập vào tài khoản của bạn. Để xác thực đăng nhập an toàn, vui lòng sử dụng mã OTP gồm 6 chữ số dưới đây:
                            </p>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <div style="background: #f0fdf4; border: 2px dashed #16a34a; border-radius: 12px; padding: 20px 30px; display: inline-block;">
                                            <p style="margin: 0 0 8px 0; color: #15803d; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">
                                                Mã OTP đăng nhập của bạn
                                            </p>
                                            <div style="font-size: 36px; font-weight: 700; color: #15803d; letter-spacing: 8px; font-family: 'Courier New', monospace; line-height: 1.2;">
                                                {{ $code }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 4px; margin: 20px 0;">
                                <p style="margin: 0; color: #991b1b; font-size: 13px; line-height: 1.5;">
                                    <strong>Lưu ý bảo mật:</strong> Mã này có hiệu lực trong vòng <strong>10 phút</strong>. Tuyệt đối không chia sẻ mã này cho bất kỳ ai, kể cả nhân viên hỗ trợ hệ thống.
                                </p>
                            </div>
                            <p style="margin: 20px 0 0 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                                Nếu bạn không thực hiện yêu cầu đăng nhập này, vui lòng đổi mật khẩu ngay lập tức hoặc liên hệ với cơ quan quản lý để bảo vệ tài khoản.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 40px; text-align: center; border-top: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; color: #64748b; font-size: 12px; line-height: 1.5;">
                                Đây là email tự động từ Hệ thống Cổng Dịch vụ công e-Ward.<br>
                                Vui lòng không phản hồi trực tiếp vào thư này.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

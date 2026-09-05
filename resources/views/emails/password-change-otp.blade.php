<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Mã xác thực đổi mật khẩu OTP</title>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse:collapse;border-spacing:0;margin:0;}
        div, td {padding:0;}
        div {margin:0 !important;}
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; padding: 0; background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #007bff 0%, #28a745 100%); padding: 30px 40px; text-align: center; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: 1px;">
                                DỊCH VỤ CÔNG TRỰC TUYẾN
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">
                                Phường ABC
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px;">
                            <p style="margin: 0 0 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Xin chào,
                            </p>
                            <p style="margin: 0 0 25px 0; color: #555555; font-size: 15px; line-height: 1.6;">
                                Chúng tôi nhận được yêu cầu đổi mật khẩu của bạn. Để hoàn tất quá trình đổi mật khẩu, vui lòng sử dụng mã xác thực (OTP) dưới đây:
                            </p>
                            
                            <!-- OTP Code Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed #007bff; border-radius: 12px; padding: 25px 30px; display: inline-block;">
                                            <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                                Mã xác thực của bạn
                                            </p>
                                            <div style="font-size: 36px; font-weight: 700; color: #007bff; letter-spacing: 8px; font-family: 'Courier New', monospace; line-height: 1.2;">
                                                {{ $code }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning Box -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 25px 0;">
                                <tr>
                                    <td style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px 20px; border-radius: 4px;">
                                        <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.6;">
                                            <strong>⚠️ Lưu ý quan trọng:</strong><br>
                                            • Mã OTP này sẽ hết hạn sau <strong>10 phút</strong> kể từ khi email được gửi.<br>
                                            • Không chia sẻ mã này với bất kỳ ai để bảo vệ tài khoản của bạn.<br>
                                            • Nếu bạn không thực hiện đổi mật khẩu, vui lòng bỏ qua email này.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 25px 0 0 0; color: #777777; font-size: 14px; line-height: 1.6;">
                                Email này được gửi tự động, vui lòng không trả lời email này. Nếu bạn có thắc mắc, vui lòng liên hệ bộ phận hỗ trợ của chúng tôi.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px 40px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px;">
                                <strong>DỊCH VỤ CÔNG TRỰC TUYẾN - PHƯỜNG ABC</strong>
                            </p>
                            <p style="margin: 0 0 15px 0; color: #999999; font-size: 12px; line-height: 1.6;">
                                Hệ thống giải quyết thủ tục hành chính trực tuyến<br>
                                Phục vụ người dân và doanh nghiệp
                            </p>
                            <p style="margin: 0; color: #999999; font-size: 11px;">
                                © {{ date('Y') }} Phường ABC. Tất cả các quyền được bảo lưu.
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Spacer -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; margin: 20px auto 0;">
                    <tr>
                        <td style="text-align: center; padding: 20px 0;">
                            <p style="margin: 0; color: #999999; font-size: 11px; line-height: 1.6;">
                                Đây là email tự động, vui lòng không trả lời. Nếu bạn nhận được email này do nhầm lẫn, vui lòng bỏ qua.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>


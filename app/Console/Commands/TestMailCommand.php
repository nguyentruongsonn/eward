<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email? : Địa chỉ email người nhận để gửi thử}';

    protected $description = 'Kiểm tra kết nối SMTP, phân tích cấu hình Mail/Queue và gửi email thử nghiệm để chẩn đoán lỗi';

    public function handle(): int
    {
        $this->info('===============================================================');
        $this->info('  E-WARD: CÔNG CỤ CHẨN ĐOÁN LỖI GỬI EMAIL TRÊN HOSTING / SERVER');
        $this->info('===============================================================');

        $recipient = $this->argument('email') ?: config('mail.from.address');

        // 1. Kiểm tra cấu hình
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $encryption = config('mail.mailers.smtp.encryption');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');
        $fromAddress = config('mail.from.address');
        $queueConnection = config('queue.default');

        $this->line('');
        $this->info('[1/4] Kiểm tra thông số cấu hình (.env):');
        $this->table(
            ['Thông số', 'Giá trị cấu hình', 'Đánh giá'],
            [
                ['MAIL_MAILER', $mailer, $mailer === 'smtp' ? '✅ Chuẩn' : ($mailer === 'log' ? '⚠️ Đang ghi vào LOG, không gửi ra ngoài!' : 'ℹ️ Driver khác')],
                ['MAIL_HOST', $host, !empty($host) ? '✅ Đã điền' : '❌ Trống'],
                ['MAIL_PORT', $port, in_array((int)$port, [587, 465, 25, 2525]) ? "✅ $port" : "⚠️ Cổng lạ: $port"],
                ['MAIL_ENCRYPTION', $encryption ?: '(null)', in_array($encryption, ['tls', 'ssl']) ? '✅ Chuẩn' : '⚠️ Nên dùng tls hoặc ssl'],
                ['MAIL_USERNAME', $username ?: '(trống)', !empty($username) ? '✅ Đã điền' : '❌ Chưa điền tài khoản gửi'],
                ['MAIL_PASSWORD', !empty($password) ? '******** (' . strlen($password) . ' ký tự)' : '(trống)', !empty($password) ? '✅ Đã điền' : '❌ Chưa điền mật khẩu'],
                ['MAIL_FROM_ADDRESS', $fromAddress ?: '(trống)', !empty($fromAddress) ? '✅ Đã điền' : '❌ Chưa điền'],
                ['QUEUE_CONNECTION', $queueConnection, $queueConnection === 'sync' ? '⚡ Gửi trực tiếp (sync)' : "⏳ Đang qua hàng đợi ($queueConnection)"],
            ]
        );

        if ($mailer === 'log') {
            $this->error('❌ LỖI: MAIL_MAILER đang là "log". Toàn bộ email chỉ được ghi vào file storage/logs/laravel.log chứ không gửi đi!');
            $this->warn('👉 Khắc phục: Sửa MAIL_MAILER=smtp trong file .env');
            return 1;
        }

        // 2. Kiểm tra hàng đợi Queue
        $this->line('');
        $this->info('[2/4] Kiểm tra hàng đợi Queue:');
        if ($queueConnection !== 'sync') {
            $this->warn("⚠️ QUEUE_CONNECTION=$queueConnection: Toàn bộ email OTP / Thông báo đang được đưa vào hàng đợi chứ không gửi ngay lập tức.");
            
            if (DB::getSchemaBuilder()->hasTable('jobs')) {
                $pendingJobs = DB::table('jobs')->count();
                if ($pendingJobs > 0) {
                    $this->error("❌ CẢNH BÁO: Đang có {$pendingJobs} job (bao gồm email) bị kẹt trong bảng `jobs` vì Queue Worker chưa chạy trên hosting!");
                    $this->warn('👉 Khắc phục:');
                    $this->line('   Cách 1 (Khuyên dùng cho cPanel): Đổi QUEUE_CONNECTION=sync trong .env để gửi email ngay lập tức.');
                    $this->line('   Cách 2: Chạy worker thủ công: php artisan queue:work --stop-when-empty');
                } else {
                    $this->info('✅ Bảng jobs hiện đang trống.');
                }
            }

            if (DB::getSchemaBuilder()->hasTable('failed_jobs')) {
                $failedJobs = DB::table('failed_jobs')->count();
                if ($failedJobs > 0) {
                    $this->error("❌ Có {$failedJobs} job gửi mail đã bị THẤT BẠI (Failed).");
                    $this->warn('   Xem chi tiết lỗi bằng lệnh: php artisan queue:failed');
                }
            }
        } else {
            $this->info('✅ QUEUE_CONNECTION=sync: Email được gửi trực tiếp, không lo bị kẹt hàng đợi.');
        }

        // 3. Kiểm tra kết nối mạng (Socket & Port)
        $this->line('');
        $this->info("[3/4] Kiểm tra kết nối mạng đến máy chủ SMTP ($host:$port)...");
        $t0 = microtime(true);
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, (int)$port, $errno, $errstr, 10);
        
        if (!$socket) {
            $this->error("❌ KHÔNG THỂ KẾT NỐI MẠNG TỚI $host:$port (Lỗi #$errno: $errstr)");
            $this->warn('👉 NGUYÊN NHÂN RẤT PHỔ BIẾN TRÊN SHARED HOSTING:');
            $this->line('   Nhà cung cấp hosting (Firewall) đã CHẶN các cổng ra bên ngoài (Port 587 hoặc 465).');
            $this->line('   Khắc phục:');
            $this->line('   1. Mở ticket nhờ kỹ thuật hosting mở cổng kết nối SMTP ra ngoài (Outbound port 587/465).');
            $this->line('   2. Hoặc chuyển sang dùng dịch vụ Webmail nội bộ của cPanel: host=localhost hoặc mail.yourdomain.com port=465 (ssl).');
            return 1;
        }

        $greeting = fgets($socket, 512);
        fclose($socket);
        $connMs = round((microtime(true) - $t0) * 1000);
        $this->info("✅ Kết nối TCP thành công sau {$connMs}ms.");
        $this->line("   Phản hồi từ server: " . trim($greeting));

        // 4. Thử gửi email thực tế
        $this->line('');
        $this->info("[4/4] Thử gửi email thực tế đến: $recipient...");
        $tSend = microtime(true);

        try {
            Mail::raw("Xin chào! Đây là email kiểm tra hệ thống Cổng Dịch vụ công e-Ward lúc " . now()->format('H:i:s d/m/Y') . ".\n\nNếu bạn nhận được email này, cấu hình gửi thư trên môi trường production đã hoạt động 100%!", function ($msg) use ($recipient) {
                $msg->to($recipient)->subject('✅ Kiểm tra cấu hình Email e-Ward Production [' . now()->format('H:i:s') . ']');
            });

            $sendMs = round((microtime(true) - $tSend) * 1000);
            $this->info("🎉 THÀNH CÔNG! Email đã được gửi đến $recipient trong {$sendMs}ms!");
            $this->info("👉 Vui lòng kiểm tra Hộp thư đến (Inbox) hoặc Thư rác (Spam) của $recipient.");
        } catch (\Throwable $e) {
            $this->error('❌ GỬI MAIL THẤT BẠI!');
            $this->error('Chi tiết lỗi: ' . $e->getMessage());

            $msg = $e->getMessage();
            $this->line('');
            $this->warn('👉 HƯỚNG DẪN XỬ LÝ LỖI NÀY:');
            if (str_contains($msg, '535') || str_contains($msg, 'BadCredentials') || str_contains($msg, 'Username and Password not accepted')) {
                $this->line('   • Lỗi MẬT KHẨU GMAIL: Google từ chối mật khẩu đăng nhập.');
                $this->line('   • Bạn PHẢI dùng "Mật khẩu ứng dụng" (App Password 16 ký tự của Google) chứ KHÔNG ĐƯỢC dùng mật khẩu Gmail thông thường!');
                $this->line('   • Cách tạo: Vào Google Account -> Bảo mật -> Xác minh 2 bước -> Mật khẩu ứng dụng -> Tạo mật khẩu 16 ký tự.');
            } elseif (str_contains($msg, 'certificate verify failed')) {
                $this->line('   • Lỗi CHỨNG CHỈ SSL trên Hosting: Hosting của bạn có chứng chỉ CA quá cũ.');
                $this->line('   • Cần liên hệ hosting cập nhật ca-certificates của PHP.');
            } elseif (str_contains($msg, 'Connection timed out') || str_contains($msg, 'refused')) {
                $this->line('   • Hosting đang chặn kết nối ra máy chủ mail Google.');
                $this->line('   • Hãy thử đổi sang cổng 465 (MAIL_PORT=465, MAIL_ENCRYPTION=ssl).');
            }
            return 1;
        }

        return 0;
    }
}

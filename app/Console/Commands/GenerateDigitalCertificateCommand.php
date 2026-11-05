<?php

namespace App\Console\Commands;

use App\Services\Files\PdfDigitalSignatureService;
use Illuminate\Console\Command;
use Throwable;

class GenerateDigitalCertificateCommand extends Command
{
    protected $signature = 'cert:generate {--force : Ghi đè nếu chứng thư đã tồn tại}';

    protected $description = 'Tạo hoặc làm mới chứng thư số X.509 phục vụ ký số file kết quả PDF';

    public function handle(PdfDigitalSignatureService $signatureService): int
    {
        $this->info('--- KHỞI TẠO CHỨNG THƯ SỐ ĐIỆN TỬ ---');

        $certPath = storage_path('app/certs/certificate.crt');
        $keyPath = storage_path('app/certs/private.key');

        if (file_exists($certPath) && file_exists($keyPath) && ! $this->option('force')) {
            $this->warn('Chứng thư số đã tồn tại. Dùng cờ --force nếu muốn tạo lại.');
            $this->line("• Certificate: {$certPath}");
            $this->line("• Private Key: {$keyPath}");

            return self::SUCCESS;
        }

        try {
            $this->line('Đang sinh cặp khóa RSA 2048-bit và chứng thư số X.509...');
            $signatureService->generateCertificate();

            $this->info('✓ Tạo chứng thư số thành công!');
            $this->line("• Certificate: {$certPath}");
            $this->line("• Private Key: {$keyPath}");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Lỗi khi tạo chứng thư số: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}

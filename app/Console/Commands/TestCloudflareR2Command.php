<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TestCloudflareR2Command extends Command
{
    protected $signature = 'r2:test';

    protected $description = 'Kiểm tra kết nối và quyền đọc/ghi tới Cloudflare R2 bucket';

    public function handle(): int
    {
        $this->info('--- KIỂM TRA CẤU HÌNH CLOUDFLARE R2 ---');

        $bucket = config('filesystems.disks.r2.bucket');
        $endpoint = config('filesystems.disks.r2.endpoint');
        $key = config('filesystems.disks.r2.key');
        $secret = config('filesystems.disks.r2.secret');
        $privateDisk = config('filesystems.private_disk');

        $this->line("• Private Disk hiện tại: <comment>{$privateDisk}</comment>");
        $this->line('• Bucket: '.($bucket ? "<info>{$bucket}</info>" : '<error>Chưa cấu hình (CLOUDFLARE_R2_BUCKET)</error>'));
        $this->line('• Endpoint: '.($endpoint ? "<info>{$endpoint}</info>" : '<error>Chưa cấu hình (CLOUDFLARE_R2_ENDPOINT)</error>'));
        $this->line('• Access Key ID: '.($key ? '<info>Đã cấu hình</info>' : '<error>Chưa cấu hình (CLOUDFLARE_R2_ACCESS_KEY_ID)</error>'));
        $this->line('• Secret Access Key: '.($secret ? '<info>Đã cấu hình</info>' : '<error>Chưa cấu hình (CLOUDFLARE_R2_SECRET_ACCESS_KEY)</error>'));

        if (! $bucket || ! $endpoint || ! $key || ! $secret) {
            $this->newLine();
            $this->warn('Vui lòng điền đầy đủ các biến môi trường Cloudflare R2 trong file .env trước khi test:');
            $this->line('CLOUDFLARE_R2_ACCESS_KEY_ID=...');
            $this->line('CLOUDFLARE_R2_SECRET_ACCESS_KEY=...');
            $this->line('CLOUDFLARE_R2_BUCKET=...');
            $this->line('CLOUDFLARE_R2_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com');
            $this->line('FILESYSTEM_PRIVATE_DISK=r2');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Đang kiểm tra kết nối và quyền ghi/đọc/xóa trên bucket...');

        $testPath = 'r2-test/connectivity-'.time().'.txt';
        $testContent = 'E-Ward Cloudflare R2 Connectivity Test at '.now()->toIso8601String();

        try {
            $disk = Storage::disk('r2');

            // 1. Test Ghi (Put)
            $this->line('1. Ghi tệp kiểm tra vào R2...');
            $disk->put($testPath, $testContent);

            // 2. Test Tồn tại (Exists)
            $this->line('2. Kiểm tra sự tồn tại của tệp...');
            if (! $disk->exists($testPath)) {
                $this->error('Tệp không tồn tại trên R2 sau khi ghi!');

                return self::FAILURE;
            }

            // 3. Test Đọc (Get)
            $this->line('3. Đọc lại nội dung tệp...');
            $retrieved = $disk->get($testPath);
            if ($retrieved !== $testContent) {
                $this->error('Nội dung tệp đọc lại không khớp với nội dung đã ghi!');

                return self::FAILURE;
            }

            // 4. Test Xóa (Delete)
            $this->line('4. Dọn dẹp tệp kiểm tra...');
            $disk->delete($testPath);

            $this->newLine();
            $this->info('✓ KẾT NỐI CLOUDFLARE R2 THÀNH CÔNG RỰC RỠ! Quyền đọc/ghi/xóa hoạt động hoàn hảo.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error('LỖI KẾT NỐI CLOUDFLARE R2:');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}

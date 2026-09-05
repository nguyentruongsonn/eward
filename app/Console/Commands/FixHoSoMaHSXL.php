<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixHoSoMaHSXL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hosoxuly:fix-mahoso';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix mã hồ sơ có giá trị là "0" hoặc không đúng format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu fix mã hồ sơ...');

        $hososWithWrongIDCD = DB::table('hosoxuly')
            ->where(function ($query) {
                $query->whereRaw("CAST(maHSXL AS CHAR) = '0'")
                    ->orWhere('maHSXL', '')
                    ->orWhereNull('maHSXL')
                    ->orWhereRaw('LENGTH(TRIM(CAST(maHSXL AS CHAR))) = 0')
                    ->orWhereRaw("CAST(maHSXL AS CHAR) NOT LIKE 'HSXL_%'");
            })
            ->get();

        if ($hososWithWrongIDCD->isEmpty()) {
            $hososWithWrongIDCD = DB::table('hosoxuly')
                ->whereIn('IDCD', [0, 1])
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();
        }

        $this->info('Tìm thấy '.$hososWithWrongIDCD->count().' hồ sơ cần sửa');

        if ($hososWithWrongIDCD->isEmpty()) {
            $this->info('Không có hồ sơ nào cần sửa!');

            return 0;
        }

        $fixedCount = 0;
        $errorCount = 0;

        foreach ($hososWithWrongIDCD as $hosoItem) {
            try {
                $hoso = (object) $hosoItem;

                $emailValue = $hoso->email ?? 'NULL';
                $this->line("Xử lý hồ sơ: maHSXL={$hoso->maHSXL}, IDCD={$hoso->IDCD}, email={$emailValue}");

                $nguoi = null;
                if (! empty($hoso->email)) {
                    $nguoi = DB::table('nguoi')->where('email', $hoso->email)->first();
                }

                if (! $nguoi && ! empty($hoso->IDCD) && $hoso->IDCD > 1) {
                    $congDanTemp = DB::table('congdan')->where('IDCD', $hoso->IDCD)->first();
                    if ($congDanTemp) {
                        $nguoi = DB::table('nguoi')->where('IDnguoiDung', $congDanTemp->IDnguoiDung)->first();
                    }
                }

                if (! $nguoi) {
                    $this->warn("  → Không tìm thấy người dùng với email: {$emailValue}, IDCD: {$hoso->IDCD}");

                    continue;
                }

                $congDan = DB::table('congdan')
                    ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                    ->first();

                if (! $congDan) {
                    $IDCD = DB::table('congdan')->insertGetId([
                        'IDnguoiDung' => $nguoi->IDnguoiDung,
                    ]);
                    $this->info("  → Đã tạo công dân mới với IDCD: {$IDCD}");
                } else {
                    $IDCD = $congDan->IDCD;
                }

                if ($hoso->IDCD == $IDCD && $IDCD > 1) {
                    $this->line('  → IDCD đã đúng, bỏ qua');

                    continue;
                }

                $oldMaHSXL = trim((string) ($hoso->maHSXL ?? '0'));
                if ($oldMaHSXL === '' || $oldMaHSXL === null || $oldMaHSXL === '0') {
                    $oldMaHSXL = '0';
                }

                if (preg_match('/^HSXL_\d+_\d{8}_\d{4}$/', $oldMaHSXL)) {
                    if ($hoso->IDCD != $IDCD) {
                        DB::table('hosoxuly')
                            ->where('maHSXL', $oldMaHSXL)
                            ->update(['IDCD' => $IDCD]);
                        $this->info("  → Đã cập nhật IDCD: {$hoso->IDCD} -> {$IDCD}");
                        $fixedCount++;
                    }

                    continue;
                }

                if ($oldMaHSXL === '0' || $oldMaHSXL === '' || ! preg_match('/^HSXL_/', $oldMaHSXL)) {
                    $datePart = '';
                    if ($hoso->ngayTiepNhan) {
                        $datePart = \Carbon\Carbon::parse($hoso->ngayTiepNhan)->format('Ymd');
                    } else {
                        $datePart = now()->format('Ymd');
                    }

                    $maxAttempts = 50;
                    $attempts = 0;
                    $newMaHSXL = null;

                    do {
                        $rand = random_int(1000, 9999);
                        $newMaHSXL = 'HSXL_'.(string) $IDCD.'_'.$datePart.'_'.(string) $rand;
                        $attempts++;
                    } while (DB::table('hosoxuly')->where('maHSXL', $newMaHSXL)->exists() && $attempts < $maxAttempts);

                    if ($attempts >= $maxAttempts) {
                        $timestamp = (string) time();
                        $lastFour = substr($timestamp, -4);
                        $extraRand = random_int(0, 9);
                        $combined = str_pad((int) $lastFour + $extraRand, 4, '0', STR_PAD_LEFT);
                        $newMaHSXL = 'HSXL_'.(string) $IDCD.'_'.$datePart.'_'.$combined;
                    }

                    if (DB::table('hosoxuly')->where('maHSXL', $newMaHSXL)->exists()) {
                        $this->warn("  → Mã mới đã tồn tại, bỏ qua: {$newMaHSXL}");

                        continue;
                    }

                    DB::beginTransaction();

                    try {
                        DB::table('tailieunop')
                            ->where('maHSXL', $oldMaHSXL)
                            ->orWhereRaw('CAST(maHSXL AS CHAR) = ?', [$oldMaHSXL])
                            ->update(['maHSXL' => $newMaHSXL]);

                        DB::table('lichsuthanhtoan')
                            ->where('maHSXL', $oldMaHSXL)
                            ->orWhereRaw('CAST(maHSXL AS CHAR) = ?', [$oldMaHSXL])
                            ->update(['maHSXL' => $newMaHSXL]);

                        $updated = DB::statement(
                            'UPDATE hosoxuly SET maHSXL = ?, IDCD = ? WHERE (CAST(maHSXL AS CHAR) = ? OR maHSXL IS NULL)',
                            [
                                (string) $newMaHSXL,
                                (int) $IDCD,
                                (string) $oldMaHSXL,
                            ]
                        );

                        if ($updated) {
                            DB::commit();
                            $this->info("  ✓ Đã cập nhật: {$oldMaHSXL} -> {$newMaHSXL}, IDCD: {$IDCD}");
                            $fixedCount++;
                        } else {
                            DB::rollBack();
                            $this->error("  ✗ Không thể cập nhật hồ sơ {$oldMaHSXL}");
                            $errorCount++;
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("  ✗ Lỗi khi cập nhật hồ sơ {$oldMaHSXL}: ".$e->getMessage());
                        $errorCount++;
                    }
                }
            } catch (\Exception $e) {
                $this->error('  ✗ Lỗi khi xử lý hồ sơ: '.$e->getMessage());
                $errorCount++;
            }
        }

        $this->info("\nHoàn thành!");
        $this->info("Đã sửa: {$fixedCount} hồ sơ");
        $this->info("Lỗi: {$errorCount} hồ sơ");

        return 0;
    }
}

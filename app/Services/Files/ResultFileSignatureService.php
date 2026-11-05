<?php

namespace App\Services\Files;

use App\Exceptions\ApiException;
use App\Models\HoSoResultFile;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ResultFileSignatureService
{
    public function __construct(
        private readonly PdfDigitalSignatureService $pdfSignatureService,
    ) {}

    private function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('filesystems.private_disk', 'local'));
    }

    public function sign(HoSoXuLy $application, HoSoResultFile $file, Nguoi $actor, ?string $note = null): HoSoXuLy
    {
        if (! $this->disk()->exists($file->path)) {
            throw new ApiException('Không tìm thấy file kết quả.', 'RESULT_FILE_NOT_FOUND', 404);
        }

        $rawContent = $this->disk()->get($file->path);
        $signatureMethod = 'internal_sha256';

        // Nếu là file PDF thì ký số và đóng dấu mộc đỏ trực tiếp vào file
        if ($this->pdfSignatureService->isPdf($file->path, $file->mime_type)) {
            try {
                $signedPdf = $this->pdfSignatureService->sign(
                    $rawContent,
                    $actor->hoTen,
                    $note ?: 'Phê duyệt kết quả giải quyết TTHC'
                );

                // Ghi đè file PDF đã ký lên ổ đĩa
                $this->disk()->put($file->path, $signedPdf);
                $file->size = strlen($signedPdf);
                $file->save();

                $rawContent = $signedPdf;
                $signatureMethod = 'pades_digital_signature';
            } catch (Throwable $e) {
                // Ghi log nếu lỗi và fallback về mã băm
                Log::warning('Lỗi ký số file PDF: '.$e->getMessage());
            }
        }

        $hash = hash('sha256', $rawContent);
        $note = trim((string) $note);

        return DB::transaction(function () use ($application, $file, $actor, $hash, $note, $signatureMethod): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $signatures = is_array($locked->file_signatures)
                ? $locked->file_signatures
                : (json_decode((string) $locked->file_signatures, true) ?: []);
            $signatureKey = 'result:'.$file->getKey();
            if (isset($signatures[$signatureKey])) {
                throw new ApiException('File đã được ký trước đó.', 'RESULT_FILE_ALREADY_SIGNED', 409);
            }

            $signatures[$signatureKey] = [
                'result_file_id' => $file->getKey(),
                'signed_by' => $actor->getKey(),
                'signed_at' => now()->toIso8601String(),
                'signature_method' => $signatureMethod,
                'file_sha256' => $hash,
                'note' => $note !== '' ? $note : null,
            ];
            $locked->file_signatures = json_encode($signatures, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $signLabel = $signatureMethod === 'pades_digital_signature' ? 'ký số điện tử PAdES' : 'xác nhận SHA-256';
            $line = '['.now()->format('Y-m-d H:i').'] '.$actor->hoTen.' đã '.$signLabel.' file kết quả #'.$file->getKey().': '.$hash;
            $locked->ghiChu = trim((string) $locked->ghiChu);
            $locked->ghiChu = $locked->ghiChu === '' ? $line : $locked->ghiChu."\n".$line;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc', 'resultFiles']);
        });
    }
}

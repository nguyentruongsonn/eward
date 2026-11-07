<?php

namespace App\Services\Files;

use App\Exceptions\ApiException;
use App\Models\HoSoResultFile;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ResultFileSignatureService
{
    public function __construct(
        private readonly PdfDigitalSignatureService $pdfSignatureService,
        private readonly OfficialSealGenerator $sealGenerator,
    ) {}

    private function disk(string $path = ''): \Illuminate\Contracts\Filesystem\Filesystem
    {
        $primary = Storage::disk(config('filesystems.private_disk', 'local'));
        if ($path === '' || $primary->exists($path)) {
            return $primary;
        }

        foreach (['r2', 'local'] as $candidate) {
            try {
                $disk = Storage::disk($candidate);
                if ($disk->exists($path)) {
                    return $disk;
                }
            } catch (\Throwable) {
            }
        }

        return $primary;
    }

    public function sign(HoSoXuLy $application, HoSoResultFile $file, Nguoi $actor, ?string $note = null): HoSoXuLy
    {
        $disk = $this->disk($file->path);
        if (! $disk->exists($file->path)) {
            throw new ApiException('Không tìm thấy file kết quả.', 'RESULT_FILE_NOT_FOUND', 404);
        }

        $rawContent = $disk->get($file->path);
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
                $disk->put($file->path, $signedPdf);
                $file->size = strlen($signedPdf);
                $file->save();

                $rawContent = $signedPdf;
                $signatureMethod = 'pades_digital_signature';
            } catch (Throwable $e) {
                // Ghi log nếu lỗi và fallback về mã băm
                Log::warning('Lỗi ký số file PDF: '.$e->getMessage());
            }
        } elseif ($this->isImage($file->path, $file->mime_type) && extension_loaded('gd')) {
            try {
                $stampedImage = $this->stampImage($rawContent, $file->mime_type, $actor->hoTen);
                if ($stampedImage !== null) {
                    $disk->put($file->path, $stampedImage);
                    $file->size = strlen($stampedImage);
                    $file->save();

                    $rawContent = $stampedImage;
                    $signatureMethod = 'image_digital_seal';
                }
            } catch (Throwable $e) {
                Log::warning('Lỗi đóng dấu file ảnh: '.$e->getMessage());
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
            $signLabel = match ($signatureMethod) {
                'pades_digital_signature' => 'ký số điện tử PAdES',
                'image_digital_seal' => 'ký số và đóng dấu điện tử',
                default => 'xác nhận SHA-256',
            };
            $line = '['.now()->format('Y-m-d H:i').'] '.$actor->hoTen.' đã '.$signLabel.' file kết quả #'.$file->getKey().': '.$hash;
            $locked->ghiChu = trim((string) $locked->ghiChu);
            $locked->ghiChu = $locked->ghiChu === '' ? $line : $locked->ghiChu."\n".$line;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc', 'resultFiles']);
        });
    }

    private function isImage(string $path, ?string $mimeType = null): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            return true;
        }

        return str_starts_with((string) $mimeType, 'image/');
    }

    private function stampImage(string $rawContent, ?string $mimeType, string $signerName): ?string
    {
        return $this->sealGenerator->stampImage($rawContent, $mimeType, $signerName);
    }
}

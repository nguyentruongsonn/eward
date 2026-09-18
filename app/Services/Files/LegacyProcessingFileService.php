<?php

namespace App\Services\Files;

use App\Exceptions\ApiException;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LegacyProcessingFileService
{
    public const OPINION = 'opinion';

    public const RESULT = 'result';

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array<int, string>
     */
    public function upload(HoSoXuLy $application, array $files, string $type): array
    {
        $directory = $this->directory($type);
        $newReferences = [];
        $storedPaths = [];

        try {
            foreach ($files as $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }

                $path = $this->privateDisk()->putFileAs(
                    $directory,
                    $file,
                    Str::uuid()->toString().'.'.strtolower($file->extension()),
                );

                if (! is_string($path) || $path === '') {
                    throw new \RuntimeException('Không thể lưu file xử lý.');
                }

                $storedPaths[] = $path;
                $newReferences[] = 'storage/'.$path;
            }

            if ($newReferences === []) {
                return $this->references($application, $type);
            }

            $allReferences = array_values(array_unique(array_merge(
                $this->references($application, $type),
                $newReferences,
            )));

            $application->forceFill([$this->column($type) => json_encode($allReferences, JSON_UNESCAPED_UNICODE)])->save();

            return $allReferences;
        } catch (\Throwable $exception) {
            $this->privateDisk()->delete($storedPaths);
            throw $exception;
        }
    }

    /**
     * @return array<int, string>
     */
    public function references(HoSoXuLy $application, string $type): array
    {
        $decoded = json_decode((string) $application->getAttribute($this->column($type)), true);

        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
    }

    /**
     * Persist a client-provided legacy list after restricting it to the expected storage prefix.
     *
     * @param  array<int, string>|string|null  $references
     * @return array<int, string>
     */
    public function replaceReferences(HoSoXuLy $application, string $type, array|string|null $references): array
    {
        if (is_string($references)) {
            $references = json_decode($references, true);
        }

        $valid = [];
        foreach (is_array($references) ? $references : [] as $reference) {
            if (is_string($reference) && $this->relativePath($reference, $type) !== null) {
                $valid[] = $reference;
            }
        }

        $valid = array_values(array_unique($valid));
        $application->forceFill([$this->column($type) => json_encode($valid, JSON_UNESCAPED_UNICODE)])->save();

        return $valid;
    }

    /**
     * @return array<int, string>
     */
    public function remove(HoSoXuLy $application, string $type, string $reference): array
    {
        $relativePath = $this->relativePath($reference, $type);
        $references = $this->references($application, $type);

        if ($relativePath === null || ! in_array($reference, $references, true)) {
            return $references;
        }

        $remaining = array_values(array_filter($references, static fn (string $item): bool => $item !== $reference));
        DB::transaction(function () use ($application, $type, $remaining): void {
            $application->forceFill([$this->column($type) => json_encode($remaining, JSON_UNESCAPED_UNICODE)])->save();
        });
        $this->privateDisk()->delete($relativePath);
        $this->legacyDisk()->delete($relativePath);

        return $remaining;
    }

    public function download(HoSoXuLy $application, string $type, string $reference): mixed
    {
        $relativePath = $this->relativePath($reference, $type);
        if ($relativePath === null || ! in_array($reference, $this->references($application, $type), true)) {
            abort(404);
        }

        $disk = $this->diskFor($relativePath);
        abort_unless($disk !== null, 404);

        return $disk->download($relativePath, basename($relativePath));
    }

    /**
     * @return array<int, string>
     */
    public function convertOpinionToResult(HoSoXuLy $application, string $reference): array
    {
        $sourcePath = $this->relativePath($reference, self::OPINION);
        if ($sourcePath === null || ! in_array($reference, $this->references($application, self::OPINION), true)) {
            return $this->references($application, self::RESULT);
        }

        $targetPath = $this->directory(self::RESULT).'/'.basename($sourcePath);
        $sourceDisk = $this->diskFor($sourcePath);
        if ($sourceDisk === null) {
            return $this->references($application, self::RESULT);
        }

        $stream = $sourceDisk->readStream($sourcePath);
        if (! is_resource($stream)) {
            return $this->references($application, self::RESULT);
        }

        try {
            if (! $this->privateDisk()->put($targetPath, $stream)) {
                throw new \RuntimeException('Không thể sao chép file xử lý.');
            }
        } finally {
            fclose($stream);
        }

        $targetReference = 'storage/'.$targetPath;
        $results = array_values(array_unique(array_merge(
            $this->references($application, self::RESULT),
            [$targetReference],
        )));
        $application->forceFill([$this->column(self::RESULT) => json_encode($results, JSON_UNESCAPED_UNICODE)])->save();

        return $results;
    }

    public function signResult(
        HoSoXuLy $application,
        string $reference,
        Nguoi $actor,
        string $signatureMethod = 'internal_sha256',
        ?string $note = null,
    ): HoSoXuLy {
        if ($this->relativePath($reference, self::RESULT) === null) {
            throw new ApiException('File kết quả không hợp lệ.', 'RESULT_FILE_REFERENCE_INVALID', 422);
        }
        if ($signatureMethod !== 'internal_sha256') {
            throw new ApiException('Phương thức ký số chưa được cấu hình.', 'SIGNATURE_METHOD_UNSUPPORTED', 422);
        }

        return DB::transaction(function () use ($application, $reference, $actor, $signatureMethod, $note): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if (! in_array($reference, $this->references($locked, self::RESULT), true)) {
                throw new ApiException('File kết quả không thuộc hồ sơ này.', 'RESULT_FILE_REFERENCE_INVALID', 422);
            }

            $relativePath = $this->relativePath($reference, self::RESULT);
            $disk = $this->diskFor($relativePath);
            if ($disk === null) {
                throw new ApiException('Không tìm thấy file kết quả.', 'RESULT_FILE_NOT_FOUND', 404);
            }
            $fileHash = hash('sha256', $disk->get($relativePath));

            $signatures = json_decode((string) $locked->file_signatures, true);
            $signatures = is_array($signatures) ? $signatures : [];
            if (isset($signatures[$reference])) {
                throw new ApiException('File đã được ký số trước đó.', 'RESULT_FILE_ALREADY_SIGNED', 409);
            }

            $signatures[$reference] = [
                'signed_by' => $actor->getKey(),
                'signed_at' => now()->toIso8601String(),
                'signature_method' => $signatureMethod,
                'file_sha256' => $fileHash,
                'note' => $note,
            ];
            $locked->file_signatures = json_encode($signatures, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $locked->ghiChu = ($locked->ghiChu ?? '')."\n[".now()->format('d/m/Y H:i').'] Lãnh đạo đã xác nhận file kết quả nội bộ: '.basename($reference);
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    private function column(string $type): string
    {
        return match ($type) {
            self::OPINION => 'duongdanfileykien',
            self::RESULT => 'duongdanfileketqua',
            default => throw new \InvalidArgumentException('Loại file xử lý không hợp lệ.'),
        };
    }

    private function directory(string $type): string
    {
        return match ($type) {
            self::OPINION => 'ykien',
            self::RESULT => 'ketqua',
            default => throw new \InvalidArgumentException('Loại file xử lý không hợp lệ.'),
        };
    }

    private function privateDisk(): FilesystemAdapter
    {
        return Storage::disk('local');
    }

    private function legacyDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }

    private function diskFor(string $relativePath): ?FilesystemAdapter
    {
        $private = $this->privateDisk();
        if ($private->exists($relativePath)) {
            return $private;
        }

        $legacy = $this->legacyDisk();
        if ($legacy->exists($relativePath)) {
            return $legacy;
        }

        return null;
    }

    private function relativePath(string $reference, string $type): ?string
    {
        $prefix = 'storage/'.$this->directory($type).'/';
        if (! str_starts_with($reference, $prefix)) {
            return null;
        }

        $name = basename($reference);
        if ($name === '' || $name === '.' || $name === '..' || $name !== str_replace(['/', '\\'], '', $name)) {
            return null;
        }

        return $this->directory($type).'/'.$name;
    }
}

<?php

namespace App\Services\Files;

use App\Contracts\Files\FileStorage;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Models\TaiLieuNop;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrivateFileStorage implements FileStorage
{
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

    public function storeApplicationFile(UploadedFile $file, HoSoXuLy $application, int $documentType, Nguoi $actor): TaiLieuNop
    {
        $extension = strtolower($file->extension());
        $safeName = Str::uuid()->toString().'.'.$extension;
        $directory = 'applications/'.$application->getKey();
        $path = $this->disk()->putFileAs($directory, $file, $safeName);

        try {
            return DB::transaction(fn (): TaiLieuNop => TaiLieuNop::create([
                'maHSXL' => $application->getKey(),
                'maGiayTo' => $documentType,
                'tenTep' => $this->safeOriginalName($file->getClientOriginalName()),
                'duongDan' => $path,
                'dinhDang' => $file->getMimeType() ?: 'application/octet-stream',
                'kichThuoc' => (string) $file->getSize(),
                'ngayTai' => now(),
            ]));
        } catch (\Throwable $exception) {
            $this->disk()->delete($path);
            throw $exception;
        }
    }

    public function download(TaiLieuNop $file, bool $inline = false): mixed
    {
        $disk = $this->disk($file->duongDan);
        abort_unless($disk->exists($file->duongDan), 404);

        if ($inline) {
            return $disk->response(
                $file->duongDan,
                $file->tenTep,
                ['Content-Type' => $file->dinhDang ?: 'application/octet-stream'],
                'inline',
            );
        }

        return $disk->download(
            $file->duongDan,
            $file->tenTep,
            ['Content-Type' => $file->dinhDang ?: 'application/octet-stream'],
        );
    }

    private function safeOriginalName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[^A-Za-z0-9._ -]+/', '_', $name) ?: 'document';

        return Str::limit($name, 180, '');
    }
}

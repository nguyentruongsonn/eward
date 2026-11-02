<?php

namespace App\Services\Files;

use App\Exceptions\ApiException;
use App\Models\HoSoOpinionFile;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationOpinionFileService
{
    private function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('filesystems.private_disk', 'local'));
    }

    public function upload(HoSoXuLy $application, Nguoi $actor, UploadedFile $file): HoSoOpinionFile
    {
        $extension = strtolower($file->extension());
        $path = $this->disk()->putFileAs(
            'applications/'.$application->getKey().'/opinions',
            $file,
            Str::uuid()->toString().'.'.$extension,
        );

        if (! is_string($path) || $path === '') {
            throw new ApiException('Không thể lưu file ý kiến.', 'OPINION_FILE_UPLOAD_FAILED', 500);
        }

        try {
            return DB::transaction(fn (): HoSoOpinionFile => HoSoOpinionFile::create([
                'maHSXL' => $application->getKey(),
                'original_name' => $this->safeOriginalName($file->getClientOriginalName()),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => (int) $file->getSize(),
                'uploaded_by' => $actor->getKey(),
            ]));
        } catch (\Throwable $exception) {
            $this->disk()->delete($path);
            throw $exception;
        }
    }

    public function paginate(HoSoXuLy $application, int $perPage = 15): LengthAwarePaginator
    {
        return HoSoOpinionFile::query()
            ->where('maHSXL', $application->getKey())
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findForApplication(HoSoXuLy $application, int $fileId): HoSoOpinionFile
    {
        return HoSoOpinionFile::query()
            ->where('maHSXL', $application->getKey())
            ->whereKey($fileId)
            ->firstOrFail();
    }

    public function download(HoSoOpinionFile $file): mixed
    {
        abort_unless($this->disk()->exists($file->path), 404);

        return $this->disk()->download(
            $file->path,
            $file->original_name,
            ['Content-Type' => $file->mime_type ?: 'application/octet-stream'],
        );
    }

    public function delete(HoSoOpinionFile $file): void
    {
        $path = DB::transaction(function () use ($file): string {
            $locked = HoSoOpinionFile::query()->whereKey($file->getKey())->lockForUpdate()->firstOrFail();
            $path = $locked->path;
            $locked->delete();

            return $path;
        });

        $this->disk()->delete($path);
    }

    private function safeOriginalName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[^A-Za-z0-9._ -]+/', '_', $name) ?: 'opinion';

        return Str::limit($name, 180, '');
    }
}

<?php

namespace App\Services\Files;

use App\Exceptions\ApiException;
use App\Models\HoSoResultFile;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationResultFileService
{
    public function upload(HoSoXuLy $application, Nguoi $actor, UploadedFile $file): HoSoResultFile
    {
        $extension = strtolower($file->extension());
        $path = Storage::disk('local')->putFileAs(
            'applications/'.$application->getKey().'/results',
            $file,
            Str::uuid()->toString().'.'.$extension,
        );

        if (! is_string($path) || $path === '') {
            throw new ApiException('Không thể lưu file kết quả.', 'RESULT_FILE_UPLOAD_FAILED', 500);
        }

        try {
            return DB::transaction(fn (): HoSoResultFile => HoSoResultFile::create([
                'maHSXL' => $application->getKey(),
                'original_name' => $this->safeOriginalName($file->getClientOriginalName()),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => (int) $file->getSize(),
                'uploaded_by' => $actor->getKey(),
            ]));
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }
    }

    public function paginate(HoSoXuLy $application, int $perPage = 15): LengthAwarePaginator
    {
        return HoSoResultFile::query()
            ->where('maHSXL', $application->getKey())
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findForApplication(HoSoXuLy $application, int $fileId): HoSoResultFile
    {
        return HoSoResultFile::query()
            ->where('maHSXL', $application->getKey())
            ->whereKey($fileId)
            ->firstOrFail();
    }

    public function download(HoSoResultFile $file): mixed
    {
        abort_unless(Storage::disk('local')->exists($file->path), 404);

        return Storage::disk('local')->download(
            $file->path,
            $file->original_name,
            ['Content-Type' => $file->mime_type ?: 'application/octet-stream'],
        );
    }

    public function delete(HoSoResultFile $file): void
    {
        $path = DB::transaction(function () use ($file): string {
            $locked = HoSoResultFile::query()->whereKey($file->getKey())->lockForUpdate()->firstOrFail();
            $path = $locked->path;
            $locked->delete();

            return $path;
        });

        Storage::disk('local')->delete($path);
    }

    private function safeOriginalName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[^A-Za-z0-9._ -]+/', '_', $name) ?: 'result';

        return Str::limit($name, 180, '');
    }
}

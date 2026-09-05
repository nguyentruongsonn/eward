<?php

namespace App\Console\Commands;

use App\Models\TaiLieuNop;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class MigrateLegacyApplicationFiles extends Command
{
    protected $signature = 'files:migrate-legacy-application-files
        {--delete-public : Delete the public copy after verification (run with application writes stopped)}';

    protected $description = 'Copy referenced legacy application documents to private storage';

    public function handle(): int
    {
        $private = Storage::disk('local');
        $public = Storage::disk('public');
        $moved = 0;
        $alreadyPrivate = 0;
        $missing = 0;
        $failed = 0;
        $deleted = 0;
        $seen = [];

        foreach (TaiLieuNop::query()->select(['taiLieuID', 'duongDan'])->cursor() as $document) {
            $relativePath = $this->relativePath($document->duongDan);
            if ($relativePath === null || isset($seen[$relativePath])) {
                continue;
            }
            $seen[$relativePath] = true;

            try {
                if ($private->exists($relativePath)) {
                    $alreadyPrivate++;
                    if ($this->deletePublicCopy($private, $public, $relativePath)) {
                        $deleted++;
                    }

                    continue;
                }

                if (! $public->exists($relativePath)) {
                    $missing++;

                    continue;
                }

                $sourceHash = $this->sha256($public, $relativePath);
                $stream = $public->readStream($relativePath);
                if (! is_resource($stream)) {
                    throw new \RuntimeException('Không thể đọc file public.');
                }

                try {
                    if (! $private->put($relativePath, $stream)) {
                        throw new \RuntimeException('Không thể ghi file private.');
                    }
                } finally {
                    fclose($stream);
                }

                if (! $private->exists($relativePath) || $this->sha256($private, $relativePath) !== $sourceHash) {
                    $private->delete($relativePath);
                    throw new \RuntimeException('Nội dung file private không khớp file public.');
                }

                $moved++;
                if ($this->option('delete-public') && $public->delete($relativePath)) {
                    $deleted++;
                }
            } catch (\Throwable $exception) {
                $failed++;
                $this->error($relativePath.': '.$exception->getMessage());
            }
        }

        $this->line('Moved: '.$moved);
        $this->line('Already private: '.$alreadyPrivate);
        $this->line('Missing: '.$missing);
        $this->line('Public deleted: '.$deleted);
        $this->line('Failed: '.$failed);

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function deletePublicCopy(FilesystemAdapter $private, FilesystemAdapter $public, string $relativePath): bool
    {
        if (! $this->option('delete-public') || ! $public->exists($relativePath)) {
            return false;
        }

        if (! $private->exists($relativePath) || $this->sha256($private, $relativePath) !== $this->sha256($public, $relativePath)) {
            throw new \RuntimeException('Nội dung file private không khớp file public.');
        }

        return $public->delete($relativePath);
    }

    private function sha256(FilesystemAdapter $disk, string $relativePath): string
    {
        $stream = $disk->readStream($relativePath);
        if (! is_resource($stream)) {
            throw new \RuntimeException('Không thể đọc file để xác minh.');
        }

        $hash = hash_init('sha256');
        try {
            hash_update_stream($hash, $stream);
        } finally {
            fclose($stream);
        }

        return hash_final($hash);
    }

    private function relativePath(mixed $reference): ?string
    {
        if (! is_string($reference)) {
            return null;
        }

        $reference = ltrim($reference, '/');
        if (str_starts_with($reference, 'storage/')) {
            $reference = substr($reference, strlen('storage/'));
        }
        if (! str_starts_with($reference, 'tailieu/')) {
            return null;
        }

        $name = basename($reference);
        if ($name === '' || $name === '.' || $name === '..' || $name !== str_replace(['/', '\\'], '', $name)) {
            return null;
        }

        return 'tailieu/'.$name;
    }
}

<?php

namespace App\Services\HoSo;

use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;

class ApplicationCommentService
{
    public function append(HoSoXuLy $application, Nguoi $actor, string $content): HoSoXuLy
    {
        $content = trim((string) preg_replace('/\R/u', ' ', $content));
        $actorName = trim((string) $actor->hoTen) ?: (string) $actor->email;
        $line = sprintf('[%s] %s: %s', now()->format('Y-m-d H:i'), $actorName, $content);

        return DB::transaction(function () use ($application, $line): HoSoXuLy {
            $locked = HoSoXuLy::query()
                ->whereKey($application->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $existing = trim((string) $locked->ghiChu);
            $locked->ghiChu = $existing === '' ? $line : $existing."\n".$line;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }
}

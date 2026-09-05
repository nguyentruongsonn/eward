<?php

namespace App\Services\HoSo;

use App\Models\DanhGia;
use App\Models\Nguoi;
use Illuminate\Database\Eloquent\Collection;

class CitizenRatingService
{
    /** @return Collection<int, DanhGia> */
    public function listForUser(Nguoi $user): Collection
    {
        $ratings = DanhGia::query()
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->with('application.tthc')
            ->orderByDesc('ngayDanhGia')
            ->get();

        $ratings->each(static function (DanhGia $rating): void {
            $rating->setAttribute('tenTTHC', $rating->application?->tthc?->tenTTHC);
        });

        return $ratings;
    }
}

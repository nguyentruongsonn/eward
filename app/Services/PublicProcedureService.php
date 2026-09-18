<?php

namespace App\Services;

use App\Models\TTHC;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PublicProcedureService
{
    /** @param array{q?: string|null, field_id?: int|string|null} $filters */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TTHC::query()->with(['linhVuc', 'cachThucHiens', 'lephis'])->published();

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $query->where('tenTTHC', 'like', '%'.$search.'%');
        }
        if (($fieldId = $filters['field_id'] ?? null) !== null && $fieldId !== '') {
            $query->where('maLinhVuc', (int) $fieldId);
        }

        return $query
            ->orderBy('maTTHC')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();
    }

    /** @param array{search?: string|null, field_id?: int|string|null} $filters */
    public function paginateForWeb(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = TTHC::query()->with('linhVuc')->published();

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('tenTTHC', 'like', '%'.$search.'%')
                    ->orWhereHas('linhVuc', fn ($fieldQuery) => $fieldQuery->where('tenLinhVuc', 'like', '%'.$search.'%'));
            });
        }
        if (($fieldId = $filters['field_id'] ?? null) !== null && $fieldId !== '') {
            $query->where('maLinhVuc', (int) $fieldId);
        }

        return $query
            ->orderBy('tenTTHC')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();
    }

    public function findPublic(int $procedure, bool $withDetails = false): ?TTHC
    {
        $query = TTHC::query()->whereKey($procedure)->published();

        if ($withDetails) {
            $query->with(['linhVuc', 'thanhPhanHoSos.giayTos', 'cachThucHiens', 'lephis', 'formConfig']);
        }

        return $query->first();
    }

    /** @return array{summary: object, reviews: LengthAwarePaginator} */
    public function ratings(TTHC $procedure, int $perPage = 10): array
    {
        $base = DB::table('danhgia')
            ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
            ->where('hosoxuly.maTTHC', $procedure->getKey());
        $summary = (clone $base)
            ->selectRaw('AVG(danhgia.soDiem) as average_score')
            ->selectRaw('COUNT(danhgia.id) as total_reviews')
            ->first();
        $reviews = (clone $base)
            ->whereNotNull('danhgia.nhanXet')
            ->where('danhgia.nhanXet', '!=', '')
            ->select('danhgia.id', 'danhgia.soDiem', 'danhgia.nhanXet', 'danhgia.ngayDanhGia', 'danhgia.IDCD')
            ->orderByDesc('danhgia.ngayDanhGia')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();

        return ['summary' => $summary, 'reviews' => $reviews];
    }
}

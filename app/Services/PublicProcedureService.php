<?php

namespace App\Services;

use App\Models\TTHC;
use App\Services\Search\ProcedureSearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;

class PublicProcedureService
{
    public function __construct(private readonly ProcedureSearchService $searchService) {}

    /** @param array{q?: string|null, field_id?: int|string|null} $filters */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TTHC::query()->with(['linhVuc', 'cachThucHiens', 'lephis'])->published();
        $perPage = max(1, min($perPage, 100));

        $search = trim((string) ($filters['q'] ?? ''));
        $fieldId = ($filters['field_id'] ?? null) !== '' && ($filters['field_id'] ?? null) !== null
            ? (int) $filters['field_id']
            : null;
        if ($search !== '') {
            $page = Paginator::resolveCurrentPage();
            $searchResult = $this->searchService->searchPublicProcedureIds($search, $fieldId, $page, $perPage);
            if ($searchResult !== null) {
                return $this->paginateSearchResults($query, $searchResult, $perPage, $page);
            }

            $query->where('tenTTHC', 'like', '%'.$search.'%');
        }
        if ($fieldId !== null) {
            $query->where('maLinhVuc', $fieldId);
        }

        return $query
            ->orderBy('maTTHC')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @param array{search?: string|null, field_id?: int|string|null} $filters */
    public function paginateForWeb(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = TTHC::query()->with('linhVuc')->published();
        $perPage = max(1, min($perPage, 100));

        $search = trim((string) ($filters['search'] ?? ''));
        $fieldId = ($filters['field_id'] ?? null) !== '' && ($filters['field_id'] ?? null) !== null
            ? (int) $filters['field_id']
            : null;
        if ($search !== '') {
            $page = Paginator::resolveCurrentPage();
            $searchResult = $this->searchService->searchPublicProcedureIds($search, $fieldId, $page, $perPage);
            if ($searchResult !== null) {
                return $this->paginateSearchResults($query, $searchResult, $perPage, $page);
            }

            $query->where(function ($inner) use ($search): void {
                $inner->where('tenTTHC', 'like', '%'.$search.'%')
                    ->orWhereHas('linhVuc', fn ($fieldQuery) => $fieldQuery->where('tenLinhVuc', 'like', '%'.$search.'%'));
            });
        }
        if ($fieldId !== null) {
            $query->where('maLinhVuc', $fieldId);
        }

        return $query
            ->orderBy('tenTTHC')
            ->paginate($perPage)
            ->withQueryString();
    }

    private function paginateSearchResults(Builder $query, array $searchResult, int $perPage, int $page): LengthAwarePaginator
    {
        $ids = array_values(array_unique(array_map('intval', $searchResult['ids'])));
        $positions = array_flip(array_map('strval', $searchResult['ids']));
        $items = $query
            ->whereIn('maTTHC', $ids)
            ->get()
            ->sortBy(fn (TTHC $procedure): int => $positions[(string) $procedure->getKey()] ?? PHP_INT_MAX)
            ->values();

        return (new Paginator(
            $items,
            (int) $searchResult['total'],
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page'],
        ))->withQueryString();
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

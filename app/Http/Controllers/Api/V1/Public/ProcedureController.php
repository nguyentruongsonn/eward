<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Public\ProcedureListRequest;
use App\Http\Requests\Api\V1\Public\ProcedureRatingsRequest;
use App\Http\Resources\Api\V1\ProcedureResource;
use App\Models\HoSoXuLy;
use App\Models\TTHC;
use App\Services\PublicProcedureService;
use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ProcedureController extends Controller
{
    public function __construct(private readonly PublicProcedureService $procedures) {}

    public function index(ProcedureListRequest $request): JsonResponse
    {
        $procedures = $this->procedures->paginate($request->validated(), $request->integer('per_page', 15));
        $data = $procedures->getCollection()->map(fn (TTHC $procedure): array => (new ProcedureResource($procedure))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách thủ tục.', 200, $request, [
            'pagination' => [
                'page' => $procedures->currentPage(),
                'per_page' => $procedures->perPage(),
                'total' => $procedures->total(),
                'last_page' => $procedures->lastPage(),
            ],
        ]);
    }

    public function show(int $procedure): JsonResponse
    {
        $item = $this->procedures->findPublic($procedure, true);
        abort_unless($item, 404);

        return ApiResponse::success(new ProcedureResource($item));
    }

    public function ratings(ProcedureRatingsRequest $request, int $procedure): JsonResponse
    {
        $item = $this->procedures->findPublic($procedure);
        abort_unless($item, 404);

        $ratingData = $this->procedures->ratings($item, $request->integer('per_page', 10));
        $summary = $ratingData['summary'];
        $reviews = $ratingData['reviews'];

        return ApiResponse::success([
            'procedure' => ['id' => $item->getKey(), 'name' => $item->tenTTHC],
            'summary' => [
                'average_score' => $summary?->average_score !== null ? (float) $summary->average_score : null,
                'total_reviews' => (int) ($summary?->total_reviews ?? 0),
            ],
            'reviews' => $reviews->getCollection()->map(fn (object $review): array => [
                'id' => $review->id,
                'score' => (int) $review->soDiem,
                'comment' => $review->nhanXet,
                'rated_at' => $review->ngayDanhGia,
            ])->values()->all(),
        ], 'Đánh giá của thủ tục.', 200, $request, [
            'pagination' => [
                'page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
            ],
        ]);
    }

    public function statistics(): JsonResponse
    {
        $total = HoSoXuLy::query()->count();
        $processing = HoSoXuLy::query()->whereIn('maTrangThai', [1, 2, 4, 5, 6, 11])->count();
        $resolved = HoSoXuLy::query()->whereIn('maTrangThai', [9, 10])->count();

        $early = HoSoXuLy::query()
            ->whereIn('maTrangThai', [9, 10])
            ->whereColumn('ngayKetThucXuLy', '<', 'ngayHenTra')
            ->count();

        $ontime = HoSoXuLy::query()
            ->whereIn('maTrangThai', [9, 10])
            ->whereColumn('ngayKetThucXuLy', '=', 'ngayHenTra')
            ->count();

        $overdue = HoSoXuLy::query()
            ->whereIn('maTrangThai', [9, 10])
            ->whereColumn('ngayKetThucXuLy', '>', 'ngayHenTra')
            ->count();

        $days = HoSoXuLy::query()
            ->selectRaw('DATE(ngayTiepNhan) as dt, COUNT(*) as cnt')
            ->whereNotNull('ngayTiepNhan')
            ->groupBy('dt')
            ->orderByDesc('dt')
            ->limit(7)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($r) => [
                'date' => (string) $r->dt,
                'day_label' => Carbon::parse($r->dt)->format('d/m'),
                'count' => (int) $r->cnt,
            ])
            ->all();

        return ApiResponse::success([
            'total_received' => (int) $total,
            'processing' => (int) $processing,
            'resolved' => (int) $resolved,
            'early' => (int) $early,
            'on_time' => (int) $ontime,
            'overdue' => (int) $overdue,
            'on_time_rate' => $resolved > 0 ? round((($early + $ontime) / $resolved) * 100, 1) : 100.0,
            'recent_7_days' => $days,
        ], 'Thống kê hồ sơ công khai.');
    }
}

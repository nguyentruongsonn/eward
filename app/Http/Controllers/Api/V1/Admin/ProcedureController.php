<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateProcedureRequest;
use App\Http\Requests\Api\V1\Admin\ProcedureListRequest;
use App\Http\Requests\Api\V1\Admin\UpdateProcedureRequest;
use App\Http\Resources\Api\V1\AdminProcedureResource;
use App\Services\Admin\AdminProcedureService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function __construct(private readonly AdminProcedureService $procedures) {}

    public function index(ProcedureListRequest $request): JsonResponse
    {
        $data = $request->validated();
        $items = $this->procedures->paginate([
            'search' => $data['q'] ?? null,
            'maLinhVuc' => $data['field_id'] ?? null,
            'trangThai' => $data['status'] ?? null,
        ], $request->integer('per_page', 20));
        $resources = $items->getCollection()
            ->map(fn (object $item): array => (new AdminProcedureResource($item))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($resources, 'Danh sách thủ tục quản trị.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, int $procedure): JsonResponse
    {
        $item = $this->procedures->findModel($procedure, true);
        abort_unless($item, 404);

        return ApiResponse::success(new AdminProcedureResource($item), 'Chi tiết thủ tục.', 200, $request);
    }

    public function store(CreateProcedureRequest $request): JsonResponse
    {
        $data = $request->validated();
        $item = $this->procedures->create(
            $this->attributes($data),
            $data['audience_ids'] ?? [],
            array_map(static fn (array $method): array => [
                'kenh' => $method['channel'],
                'thoiHanGiaiQuyet' => $method['resolution_time'] ?? null,
                'moTaPhiLePhi' => $method['fee_description'] ?? null,
                'thoiHan' => $method['duration'] ?? 0,
                'moTa' => $method['description'] ?? null,
            ], $data['methods'] ?? []),
            array_map(static fn (array $fee): array => [
                'loaiLePhi' => $fee['type'],
                'soTien' => $fee['amount'] ?? 0,
                'batBuoc' => $fee['required'] ?? null,
                'moTa' => $fee['description'] ?? null,
            ], $data['fees'] ?? []),
            $data['form_config'] ?? null,
            array_map(static fn (array $component): array => [
                'tenThanhPhan' => $component['name'],
                'giayTo' => array_map(static fn (array $document): array => [
                    'maGiayTo' => $document['document_id'],
                    'soLuongBanChinh' => $document['original_copies'] ?? 0,
                    'soLuongBanSao' => $document['duplicate_copies'] ?? 0,
                ], $component['documents'] ?? []),
            ], $data['components'] ?? []),
        );

        return ApiResponse::success(
            new AdminProcedureResource($this->procedures->findModel($item->getKey(), true)),
            'Đã tạo thủ tục.',
            201,
            $request,
        );
    }

    public function update(UpdateProcedureRequest $request, int $procedure): JsonResponse
    {
        $data = $request->validated();
        $item = $this->procedures->update(
            $procedure,
            $this->attributes($data),
            $this->relatedConfig($data),
        );

        return ApiResponse::success(
            new AdminProcedureResource($this->procedures->findModel($item->getKey(), true)),
            'Đã cập nhật thủ tục.',
            200,
            $request,
        );
    }

    public function destroy(Request $request, int $procedure): JsonResponse
    {
        $this->procedures->delete($procedure);

        return ApiResponse::success(null, 'Đã xóa thủ tục.', 200, $request);
    }

    /** @param array<string, mixed> $data */
    private function attributes(array $data): array
    {
        return [
            'tenTTHC' => $data['name'],
            'maLinhVuc' => $data['field_id'],
            'maQuayLamViec' => $data['counter_id'] ?? null,
            'trinhTuThucHien' => $data['instructions'],
            'doiTuongThucHien' => $data['target'],
            'coQuanThucHien' => $data['agency'],
            'trangThai' => $data['status'] ?? 'Chờ công khai',
            'yeuCauDieuKien' => $data['requirements'],
            'canCuPhapLy' => $data['legal_basis'],
            'ketQuaThucHien' => $data['result'],
        ];
    }

    /** @param array<string, mixed> $data */
    private function relatedConfig(array $data): array
    {
        $related = [];
        if (array_key_exists('audience_ids', $data)) {
            $related['audience_ids'] = $data['audience_ids'];
        }
        if (array_key_exists('methods', $data)) {
            $related['methods'] = array_map(static fn (array $method): array => [
                'kenh' => $method['channel'],
                'thoiHanGiaiQuyet' => $method['resolution_time'] ?? null,
                'moTaPhiLePhi' => $method['fee_description'] ?? null,
                'thoiHan' => $method['duration'] ?? 0,
                'moTa' => $method['description'] ?? null,
            ], $data['methods']);
        }
        if (array_key_exists('fees', $data)) {
            $related['fees'] = array_map(static fn (array $fee): array => [
                'loaiLePhi' => $fee['type'],
                'soTien' => $fee['amount'] ?? 0,
                'batBuoc' => $fee['required'] ?? null,
                'moTa' => $fee['description'] ?? null,
            ], $data['fees']);
        }
        if (array_key_exists('form_config', $data)) {
            $related['form_config'] = $data['form_config'];
        }
        if (array_key_exists('components', $data)) {
            $related['components'] = array_map(static fn (array $component): array => [
                'tenThanhPhan' => $component['name'],
                'giayTo' => array_map(static fn (array $document): array => [
                    'maGiayTo' => $document['document_id'],
                    'soLuongBanChinh' => $document['original_copies'] ?? 0,
                    'soLuongBanSao' => $document['duplicate_copies'] ?? 0,
                ], $component['documents'] ?? []),
            ], $data['components']);
        }

        return $related;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateFieldRequest;
use App\Http\Requests\Api\V1\Admin\FieldListRequest;
use App\Http\Requests\Api\V1\Admin\UpdateFieldRequest;
use App\Http\Resources\Api\V1\AdminFieldResource;
use App\Models\LinhVuc;
use App\Services\Admin\AdminFieldService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function __construct(private readonly AdminFieldService $fields) {}

    public function index(FieldListRequest $request): JsonResponse
    {
        $items = $this->fields->paginate(
            $request->validated(),
            $request->integer('per_page', 20),
        );
        $data = $items->getCollection()
            ->map(fn (LinhVuc $field): array => (new AdminFieldResource($field))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($data, 'Danh sách lĩnh vực.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(CreateFieldRequest $request): JsonResponse
    {
        $field = $this->fields->create($request->string('name')->toString());

        return ApiResponse::success(new AdminFieldResource($field), 'Đã tạo lĩnh vực.', 201, $request);
    }

    public function update(UpdateFieldRequest $request, int $field): JsonResponse
    {
        $item = $this->fields->update($field, $request->string('name')->toString());

        return ApiResponse::success(new AdminFieldResource($item), 'Đã cập nhật lĩnh vực.', 200, $request);
    }

    public function destroy(Request $request, int $field): JsonResponse
    {
        $this->fields->delete($field);

        return ApiResponse::success(null, 'Đã xóa lĩnh vực.', 200, $request);
    }
}

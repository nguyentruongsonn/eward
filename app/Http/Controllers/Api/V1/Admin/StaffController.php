<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateStaffRequest;
use App\Http\Requests\Api\V1\Admin\StaffListRequest;
use App\Http\Requests\Api\V1\Admin\UpdateStaffRequest;
use App\Http\Resources\Api\V1\AdminStaffResource;
use App\Services\Admin\AdminStaffService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function __construct(private readonly AdminStaffService $staff) {}

    public function index(StaffListRequest $request): JsonResponse
    {
        $items = $this->staff->paginate(
            $request->validated(),
            $request->integer('per_page', 20),
        );
        $resources = $items->getCollection()
            ->map(fn (object $item): array => (new AdminStaffResource($item))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($resources, 'Danh sách cán bộ.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'counters' => $this->staff->counters(),
        ]);
    }

    public function counters(Request $request): JsonResponse
    {
        return ApiResponse::success($this->staff->counters(), 'Danh sách quầy làm việc.', 200, $request);
    }

    public function show(Request $request, int $staff): JsonResponse
    {
        $item = $this->staff->findForView($staff);
        abort_unless($item, 404);

        return ApiResponse::success(new AdminStaffResource($item), 'Chi tiết cán bộ.', 200, $request);
    }

    public function store(CreateStaffRequest $request): JsonResponse
    {
        $user = $this->staff->create($request->validated());
        $assignment = DB::table('canbo')->where('IDnguoiDung', $user->getKey())->first();
        $view = $this->staff->findForView($assignment->IDCB);

        return ApiResponse::success(new AdminStaffResource($view), 'Đã tạo cán bộ.', 201, $request);
    }

    public function update(UpdateStaffRequest $request, int $staff): JsonResponse
    {
        $this->staff->update($staff, $request->validated());
        $view = $this->staff->findForView($staff);

        return ApiResponse::success(new AdminStaffResource($view), 'Đã cập nhật cán bộ.', 200, $request);
    }

    public function destroy(Request $request, int $staff): JsonResponse
    {
        $this->staff->delete($staff);

        return ApiResponse::success(null, 'Đã xóa cán bộ.', 200, $request);
    }
}

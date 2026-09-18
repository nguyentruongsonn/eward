<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateAdminUserRequest;
use App\Http\Requests\Api\V1\Admin\UpdateAdminUserRequest;
use App\Http\Requests\Api\V1\Admin\UserListRequest;
use App\Http\Resources\Api\V1\AdminUserResource;
use App\Models\Nguoi;
use App\Services\Admin\AdminUserService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly AdminUserService $users) {}

    public function index(UserListRequest $request): JsonResponse
    {
        $users = $this->users->paginate($request->validated(), $request->integer('per_page', 20));
        $data = $users->getCollection()->map(fn (Nguoi $user): array => (new AdminUserResource($user))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách người dùng.', 200, $request, [
            'pagination' => [
                'page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function store(CreateAdminUserRequest $request): JsonResponse
    {
        $user = $this->users->create($request->validated());

        return ApiResponse::success(new AdminUserResource($user), 'Đã tạo người dùng.', 201, $request);
    }

    public function update(UpdateAdminUserRequest $request, int $user): JsonResponse
    {
        $model = $this->users->update($user, $request->validated());

        return ApiResponse::success(new AdminUserResource($model), 'Đã cập nhật người dùng.', 200, $request);
    }

    public function destroy(Request $request, int $user): JsonResponse
    {
        $this->users->delete($user, (int) $request->user('api')->getKey());

        return ApiResponse::success(null, 'Đã xóa người dùng.', 200, $request);
    }
}

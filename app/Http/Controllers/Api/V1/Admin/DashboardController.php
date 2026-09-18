<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\DashboardRequest;
use App\Http\Resources\Api\V1\Admin\DashboardResource;
use App\Models\Nguoi;
use App\Services\Admin\AdminDashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboard) {}

    public function show(DashboardRequest $request): JsonResponse
    {
        /** @var Nguoi $actor */
        $actor = $request->user('api');
        $data = $this->dashboard->forApi($actor, $request->validated());

        return ApiResponse::success(new DashboardResource($data), 'Tổng quan nghiệp vụ.', 200, $request);
    }
}

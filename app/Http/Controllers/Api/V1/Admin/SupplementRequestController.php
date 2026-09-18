<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CreateSupplementRequest;
use App\Http\Resources\Api\V1\AdminApplicationResource;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Services\HoSo\ApplicationSupplementService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SupplementRequestController extends Controller
{
    public function __construct(private readonly ApplicationSupplementService $supplements) {}

    public function store(CreateSupplementRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $actor */
        $actor = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('requestSupplement', $item);
        $item = $this->supplements->request(
            $item,
            $actor,
            $request->input('document_ids', []),
            $request->input('note'),
        );

        return ApiResponse::success(new AdminApplicationResource($item), 'Đã gửi yêu cầu bổ sung hồ sơ.', 202, $request);
    }
}

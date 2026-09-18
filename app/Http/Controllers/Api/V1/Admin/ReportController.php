<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ReportFilterRequest;
use App\Services\Reports\AdminReportService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private readonly AdminReportService $reports) {}

    public function revenue(ReportFilterRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->reports->revenue($request->input('from'), $request->input('to')),
            'Báo cáo doanh thu.',
            200,
            $request,
        );
    }

    public function applications(ReportFilterRequest $request): JsonResponse
    {
        return ApiResponse::success(
            $this->reports->applications($request->input('from'), $request->input('to')),
            'Báo cáo hồ sơ.',
            200,
            $request,
        );
    }
}

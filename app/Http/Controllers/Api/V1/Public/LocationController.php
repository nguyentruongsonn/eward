<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicLocationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function __construct(private readonly PublicLocationService $locations) {}

    public function provinces(): JsonResponse
    {
        return ApiResponse::success($this->locations->provinces());
    }

    public function wards(int $province): JsonResponse
    {
        return ApiResponse::success($this->locations->wards($province));
    }

    public function fields(): JsonResponse
    {
        return ApiResponse::success($this->locations->fields());
    }
}

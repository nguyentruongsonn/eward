<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Public\ApplicationTrackingRequest;
use App\Services\PublicApplicationTrackingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ApplicationTrackingController extends Controller
{
    public function __construct(private readonly PublicApplicationTrackingService $tracking) {}

    public function store(ApplicationTrackingRequest $request): JsonResponse
    {
        $application = $this->tracking->findVerifiedByCode(
            $request->string('code')->trim()->toString(),
            $request->string('verification')->trim()->toString(),
        );

        abort_unless($application, 404);

        return ApiResponse::success(
            $this->tracking->minimalTimeline($application),
            'Thông tin theo dõi hồ sơ.',
            200,
            $request,
        );
    }
}

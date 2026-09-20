<?php

namespace App\Http\Controllers\Api\V1\Citizen;

use App\Enums\HoSoStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\HoSo\ApplicationListRequest;
use App\Http\Requests\Api\V1\HoSo\CancelApplicationRequest;
use App\Http\Requests\Api\V1\HoSo\CreateApplicationRequest;
use App\Http\Requests\Api\V1\HoSo\RateApplicationRequest;
use App\Http\Requests\Api\V1\HoSo\UpdateCitizenApplicationRequest;
use App\Http\Resources\Api\V1\ApplicationResource;
use App\Http\Resources\Api\V1\RatingResource;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Services\HoSo\ApplicationRatingService;
use App\Services\HoSo\ApplicationSubmissionService;
use App\Services\Workflow\HoSoWorkflowService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly ApplicationSubmissionService $submissionService,
        private readonly ApplicationRatingService $ratingService,
        private readonly HoSoWorkflowService $workflow,
    ) {}

    public function index(ApplicationListRequest $request): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $applications = $this->submissionService->listForUser(
            $user,
            $request->integer('per_page', 15),
            $request->validated(),
        );

        $data = $applications->getCollection()->map(fn (HoSoXuLy $application): array => (new ApplicationResource($application))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách hồ sơ.', 200, $request, [
            'pagination' => [
                'page' => $applications->currentPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'last_page' => $applications->lastPage(),
            ],
        ]);
    }

    public function store(CreateApplicationRequest $request): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $application = $this->submissionService->submit($user, $request->validated(), $request->header('Idempotency-Key'));

        return ApiResponse::success(new ApplicationResource($application), 'Nộp hồ sơ thành công.', 201, $request);
    }

    public function update(UpdateCitizenApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = $this->submissionService->findForUser($user, $application);
        $this->authorize('update', $item);
        $item = $this->submissionService->updateDraft($item, $request->validated('data'));

        return ApiResponse::success(new ApplicationResource($item), 'Đã cập nhật hồ sơ.', 200, $request);
    }

    public function show(Request $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->with(['trangThai', 'tthc', 'resultFiles', 'paymentHistories', 'paymentIntents'])->findOrFail($application);
        $this->authorize('view', $item);

        return ApiResponse::success(new ApplicationResource($item), 'Chi tiết hồ sơ.', 200, $request);
    }

    public function cancel(CancelApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('cancel', $item);
        $reason = $request->string('reason')->trim()->toString();
        $note = $reason !== ''
            ? 'Công dân yêu cầu rút hồ sơ: '.$reason
            : 'Công dân yêu cầu rút hồ sơ.';
        $item = $this->workflow->transition($item, HoSoStatus::WithdrawalRequested->value, $user, $note);

        return ApiResponse::success(new ApplicationResource($item), 'Đã gửi yêu cầu rút hồ sơ.', 200, $request);
    }

    public function rate(RateApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('rate', $item);
        $rating = $this->ratingService->rate(
            $user,
            $item,
            $request->integer('score'),
            $request->input('comment'),
        );

        return ApiResponse::success(new RatingResource($rating), 'Cảm ơn bạn đã đánh giá.', 201, $request);
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\AcceptApplicationRequest;
use App\Http\Requests\Api\V1\Admin\ApplicationListRequest;
use App\Http\Requests\Api\V1\Admin\ApproveApplicationRequest;
use App\Http\Requests\Api\V1\Admin\CompleteDirectReceptionRequest;
use App\Http\Requests\Api\V1\Admin\ConfirmReceptionRequest;
use App\Http\Requests\Api\V1\Admin\CreateApplicationCommentRequest;
use App\Http\Requests\Api\V1\Admin\DeliverApplicationRequest;
use App\Http\Requests\Api\V1\Admin\ForwardApplicationRequest;
use App\Http\Requests\Api\V1\Admin\MailHistoryListRequest;
use App\Http\Requests\Api\V1\Admin\RejectApplicationRequest;
use App\Http\Requests\Api\V1\Admin\ReworkApplicationRequest;
use App\Http\Requests\Api\V1\Admin\SendApplicationMailRequest;
use App\Http\Requests\Api\V1\Admin\UpdateApplicationGeneralInfoRequest;
use App\Http\Requests\Api\V1\Admin\UpdateApplicationOpinionRequest;
use App\Http\Requests\Api\V1\HoSo\TransitionApplicationRequest;
use App\Http\Resources\Api\V1\AdminApplicationResource;
use App\Http\Resources\Api\V1\ApplicationResource;
use App\Http\Resources\Api\V1\MailHistoryResource;
use App\Http\Resources\Api\V1\WorkflowEventResource;
use App\Models\HoSoXuLy;
use App\Models\HoSoXuLyMailHistory;
use App\Models\Nguoi;
use App\Services\Admin\AdminApplicationListService;
use App\Services\Admin\AdminApplicationViewService;
use App\Services\HoSo\ApplicationCommentService;
use App\Services\HoSo\ApplicationGeneralInfoService;
use App\Services\HoSo\ApplicationProcessingService;
use App\Services\Mail\ApplicationMailService;
use App\Services\Workflow\HoSoWorkflowService;
use App\Services\Workflow\StaffApplicationScope;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly HoSoWorkflowService $workflow,
        private readonly ApplicationCommentService $commentService,
        private readonly ApplicationGeneralInfoService $generalInfo,
        private readonly ApplicationProcessingService $processingService,
        private readonly AdminApplicationViewService $applicationViews,
        private readonly AdminApplicationListService $applicationLists,
        private readonly ApplicationMailService $mailService,
    ) {}

    public function show(\Illuminate\Http\Request $request, string $application): JsonResponse
    {
        $item = $this->applicationViews->findForAdmin($application);
        $this->authorize('viewStaff', $item);

        return ApiResponse::success(new AdminApplicationResource($item), 'Chi tiết hồ sơ.', 200, $request);
    }

    public function index(ApplicationListRequest $request): JsonResponse
    {
        $filters = [
            'maTrangThai' => $request->input('status'),
            'maTTHC' => $request->input('procedure_id'),
            'search' => $request->input('citizen'),
            'ngayTiepNhan_from' => $request->input('from'),
            'ngayTiepNhan_to' => $request->input('to'),
            'overdue' => $request->input('overdue'),
        ];
        $sortColumn = [
            'received_at' => 'ngayTiepNhan',
            'id' => 'maHSXL',
            'status' => 'maTrangThai',
        ][$request->input('sort', 'received_at')];
        $items = $this->applicationLists->paginate(
            $filters,
            AdminApplicationListService::ALL_SCOPE,
            $request->integer('per_page', 20),
            $sortColumn,
            $request->input('direction', 'desc'),
            StaffApplicationScope::statusesFor(Role::normalize($request->user('api')->vaiTro)),
        );
        $data = $items->getCollection()->map(fn (HoSoXuLy $item): array => (new \App\Http\Resources\Api\V1\Admin\WorkflowQueueResource($item))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách hồ sơ quản trị.', 200, $request, ['pagination' => ['page' => $items->currentPage(), 'per_page' => $items->perPage(), 'total' => $items->total(), 'last_page' => $items->lastPage()], 'filters' => $request->only(['status', 'procedure_id', 'citizen', 'from', 'to', 'sort', 'direction', 'overdue'])]);
    }

    public function transition(TransitionApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('transition', [$item, $request->integer('status')]);
        $item = $this->workflow->transition($item, $request->integer('status'), $user, $request->input('note'));

        return ApiResponse::success(new ApplicationResource($item), 'Đã cập nhật trạng thái hồ sơ.', 200, $request);
    }

    public function accept(AcceptApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('accept', $item);
        $item = $this->workflow->accept($item, $user);

        return ApiResponse::success(new ApplicationResource($item), 'Đã tiếp nhận hồ sơ.', 200, $request);
    }

    public function completeDirectReception(CompleteDirectReceptionRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('completeDirectReception', $item);
        $item = $this->workflow->completeDirectReception($item, $user);

        return ApiResponse::success(new ApplicationResource($item), 'Đã hoàn tất tiếp nhận trực tiếp.', 200, $request);
    }

    public function reject(RejectApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('reject', $item);
        $item = $this->workflow->reject($item, $user, $request->string('reason')->toString());

        return ApiResponse::success(new ApplicationResource($item), 'Đã từ chối tiếp nhận hồ sơ.', 200, $request);
    }

    public function forward(ForwardApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('forward', $item);

        $note = $request->input('note');
        if ($request->filled('leader_id')) {
            $leader = Nguoi::query()
                ->whereKey($request->integer('leader_id'))
                ->whereRaw('TRIM(vaiTro) = ?', [Role::Leader->value])
                ->first();
            if (! $leader) {
                return ApiResponse::error('Người được chỉ định không phải lãnh đạo.', 'LEADER_INVALID', 422, [], $request);
            }

            $note = trim('Chuyển đến lãnh đạo: '.$leader->hoTen.($note ? "\nGhi chú chuyển: {$note}" : ''));
        }

        $item = $this->workflow->forwardForApproval($item, $user, $note);

        return ApiResponse::success(new ApplicationResource($item), 'Đã chuyển hồ sơ sang lãnh đạo.', 200, $request);
    }

    public function confirmReception(ConfirmReceptionRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('confirmReception', $item);
        $item = $this->processingService->confirmReception($item, $request->input('note'), $user);

        return ApiResponse::success(new ApplicationResource($item), 'Đã chuyển hồ sơ sang cán bộ thụ lý.', 200, $request);
    }

    public function approve(ApproveApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('approve', $item);

        $approvalComment = $request->input('approval_comment');
        $note = $request->input('note') ?: ($approvalComment ? 'Lãnh đạo phê duyệt: '.$approvalComment : null);
        $item = $this->workflow->approve($item, $user, $note, $approvalComment);

        return ApiResponse::success(new ApplicationResource($item), 'Đã phê duyệt hồ sơ.', 200, $request);
    }

    public function rework(ReworkApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('rework', $item);
        $item = $this->workflow->requestRework($item, $user, $request->string('note')->toString());

        return ApiResponse::success(new ApplicationResource($item), 'Đã yêu cầu xử lý lại hồ sơ.', 200, $request);
    }

    public function deliver(DeliverApplicationRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('deliver', $item);
        $item = $this->workflow->deliver($item, $user);

        if (! empty($item->email)) {
            try {
                $subject = 'Thông báo trả kết quả giải quyết thủ tục hành chính';
                $body = "Kính gửi công dân {$item->tenChuHoSo},\n\nHồ sơ thủ tục hành chính mã số {$item->maHSXL} ({$item->tthc?->tenTTHC}) đã được xử lý hoàn tất và trả kết quả.\nQuý công dân có thể tải về bản kết quả điện tử tại cổng Dịch vụ công hoặc nhận trực tiếp tại Bộ phận Tiếp nhận và Trả kết quả (Một cửa).\n\nTrân trọng thông báo!";
                $this->mailService->sendApplicationMail($item, $subject, $body, $user, 'tra_ket_qua');
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return ApiResponse::success(new ApplicationResource($item), 'Đã trả kết quả cho công dân.', 200, $request);
    }

    public function updateGeneralInfo(UpdateApplicationGeneralInfoRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('updateGeneralInfo', $item);
        $item = $this->generalInfo->update($item, $request->validated());

        return ApiResponse::success(new ApplicationResource($item), 'Đã cập nhật thông tin hồ sơ.', 200, $request);
    }

    public function comment(CreateApplicationCommentRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('comment', $item);
        $content = (string) ($request->input('content') ?? $request->input('comment') ?? '');
        $item = $this->commentService->append($item, $user, $content);

        return ApiResponse::success(new AdminApplicationResource($item), 'Đã thêm ghi chú xử lý hồ sơ.', 201, $request);
    }

    public function opinion(UpdateApplicationOpinionRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('comment', $item);
        $content = $request->input('content') ?? $request->input('opinion');
        $item = $this->processingService->saveOpinion($item, $content);

        return ApiResponse::success(new AdminApplicationResource($item), 'Đã lưu ý kiến xử lý hồ sơ.', 200, $request);
    }

    public function mailHistory(MailHistoryListRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewStaff', $item);

        $history = $this->mailService->paginateHistory(
            $item,
            $request->input('direction'),
            $request->integer('per_page', 20),
        );
        $data = $history->getCollection()
            ->map(fn (HoSoXuLyMailHistory $mail): array => (new MailHistoryResource($mail))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($data, 'Lịch sử trao đổi email của hồ sơ.', 200, $request, [
            'pagination' => [
                'page' => $history->currentPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
                'last_page' => $history->lastPage(),
            ],
            'filters' => $request->only(['direction']),
        ]);
    }

    public function sendMail(SendApplicationMailRequest $request, string $application): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('comment', $item);

        if (! $item->email) {
            return ApiResponse::error('Hồ sơ không có email.', 'APPLICATION_EMAIL_MISSING', 422, [], $request);
        }

        try {
            $history = $this->mailService->sendApplicationMail(
                $item,
                $request->string('subject')->toString(),
                $request->string('content')->toString(),
                $user,
                $request->string('type')->toString(),
            );
        } catch (\Throwable $exception) {
            report($exception);

            return ApiResponse::error('Không thể gửi email cho công dân.', 'MAIL_SEND_FAILED', 502, [], $request);
        }

        return ApiResponse::success(new MailHistoryResource($history), 'Đã gửi email cho công dân.', 201, $request, [
            'last_mail_sent_at' => optional($item->refresh()->last_mail_sent_at)->toIso8601String(),
        ]);
    }

    public function events(string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewStaff', $item);

        $events = $item->workflowEvents()->get();

        return ApiResponse::success(
            WorkflowEventResource::collection($events),
            'Lịch sử tiến trình xử lý hồ sơ.',
            200,
        );
    }
}

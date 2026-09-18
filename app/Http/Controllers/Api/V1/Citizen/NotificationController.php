<?php

namespace App\Http\Controllers\Api\V1\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Citizen\NotificationListRequest;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Models\Nguoi;
use App\Services\Notifications\CitizenNotificationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly CitizenNotificationService $notifications) {}

    public function index(NotificationListRequest $request): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $items = $this->notifications->paginateForUser(
            $user,
            $request->boolean('only_unread'),
            $request->integer('per_page', 15),
        );
        $data = $items->getCollection()
            ->map(fn ($notification): array => (new NotificationResource($notification))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($data, 'Danh sách thông báo.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'unread_count' => $this->notifications->unreadCount($user),
        ]);
    }

    public function show(Request $request, int $notification): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = $this->notifications->findForUser($user, $notification);

        return ApiResponse::success(new NotificationResource($item), 'Chi tiết thông báo.', 200, $request);
    }

    public function markAsRead(Request $request, int $notification): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $item = $this->notifications->markAsRead($this->notifications->findForUser($user, $notification));

        return ApiResponse::success(new NotificationResource($item), 'Đã đánh dấu thông báo đã đọc.', 200, $request, [
            'unread_count' => $this->notifications->unreadCount($user),
        ]);
    }
}

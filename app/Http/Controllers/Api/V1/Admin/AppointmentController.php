<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\AppointmentListRequest;
use App\Http\Requests\Api\V1\Admin\AppointmentReminderRequest;
use App\Http\Requests\Api\V1\Admin\CheckInRequest;
use App\Http\Requests\Api\V1\Admin\UpdateAppointmentStatusRequest;
use App\Http\Resources\Api\V1\AdminAppointmentResource;
use App\Http\Resources\Api\V1\AppointmentResource;
use App\Models\LichHen;
use App\Services\Admin\AdminAppointmentService;
use App\Services\Appointments\AppointmentReminderService;
use App\Services\Appointments\AppointmentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentService $appointments,
        private readonly AppointmentReminderService $reminders,
        private readonly AdminAppointmentService $appointmentQueries,
    ) {}

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $item = $this->appointments->checkIn($request->string('token')->toString(), $request->user('api'));

        return ApiResponse::success(new AppointmentResource($item), 'Check-in thành công.', 200, $request);
    }

    public function index(AppointmentListRequest $request): JsonResponse
    {
        $items = $this->appointmentQueries->paginate(
            $request->validated(),
            $request->integer('per_page', 20),
        );
        $data = $items->getCollection()->map(fn (LichHen $item): array => (new AdminAppointmentResource($item))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách lịch hẹn quản trị.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'filters' => $request->only(['search', 'date', 'from_date', 'to_date', 'status', 'procedure_id']),
        ]);
    }

    public function reminders(AppointmentReminderRequest $request): JsonResponse
    {
        $queued = $this->reminders->dispatchUpcoming($request->integer('hours', 24));

        return ApiResponse::success(['queued' => $queued], 'Đã đưa email nhắc lịch vào hàng đợi.', 202, $request);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, string $appointment): JsonResponse
    {
        $result = $this->appointments->updateStatus(
            $appointment,
            $request->string('status')->toString(),
        );

        return ApiResponse::success([
            'appointment' => new AdminAppointmentResource($result['appointment']),
            'application_id' => $result['application']?->getKey(),
            'old_status' => $result['old_status'],
        ], 'Đã cập nhật trạng thái lịch hẹn.', 200, $request);
    }

    public function reminder(Request $request, string $appointment): JsonResponse
    {
        $item = $this->reminders->dispatchManual($appointment);

        return ApiResponse::success([
            'appointment_id' => $item->getKey(),
            'email' => $item->congdan?->nguoi?->email,
        ], 'Đã đưa mail nhắc hẹn vào hàng đợi.', 202, $request);
    }
}

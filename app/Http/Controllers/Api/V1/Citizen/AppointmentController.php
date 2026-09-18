<?php

namespace App\Http\Controllers\Api\V1\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Appointment\AppointmentListRequest;
use App\Http\Requests\Api\V1\Appointment\AvailableSlotsRequest;
use App\Http\Requests\Api\V1\Appointment\CreateAppointmentRequest;
use App\Http\Resources\Api\V1\AppointmentResource;
use App\Models\LichHen;
use App\Models\Nguoi;
use App\Services\Appointments\AppointmentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointments) {}

    public function index(AppointmentListRequest $request): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $items = $this->appointments->listForUser(
            $user,
            $request->integer('per_page', 15),
            $request->validated(),
        );
        $data = $items->getCollection()->map(fn (LichHen $appointment): array => (new AppointmentResource($appointment))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách lịch hẹn.', 200, $request, ['pagination' => ['page' => $items->currentPage(), 'per_page' => $items->perPage(), 'total' => $items->total(), 'last_page' => $items->lastPage()]]);
    }

    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->appointments->book($request->user('api'), $request->integer('procedure_id'), $request->string('scheduled_at')->toString());

        return ApiResponse::success(new AppointmentResource($appointment), 'Đặt lịch thành công.', 201, $request);
    }

    public function slots(AvailableSlotsRequest $request): JsonResponse
    {
        return ApiResponse::success($this->appointments->availableSlots($request->integer('procedure_id'), $request->string('date')->toString()), 'Khung giờ còn trống.', 200, $request);
    }

    public function cancel(Request $request, string $appointment): JsonResponse
    {
        $item = $this->appointments->cancel($request->user('api'), $appointment);

        return ApiResponse::success(new AppointmentResource($item), 'Đã hủy lịch hẹn.', 200, $request);
    }
}

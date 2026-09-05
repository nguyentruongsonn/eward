<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Payment\CreatePaymentIntentRequest;
use App\Http\Requests\Api\V1\Payment\PaymentHistoryListRequest;
use App\Http\Requests\Api\V1\Payment\PaymentListRequest;
use App\Http\Resources\Api\V1\PaymentHistoryResource;
use App\Http\Resources\Api\V1\PaymentIntentResource;
use App\Http\Resources\Api\V1\PaymentInvoiceResource;
use App\Models\Nguoi;
use App\Models\PaymentIntent;
use App\Services\Payments\PaymentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function store(CreatePaymentIntentRequest $request): JsonResponse
    {
        $intent = $this->payments->createIntent(
            $request->user('api'),
            $request->string('application_id')->toString(),
            $request->string('provider')->toString(),
            $request->header('Idempotency-Key'),
        );

        return ApiResponse::success(new PaymentIntentResource($intent), 'Đã tạo yêu cầu thanh toán.', 201, $request);
    }

    public function index(PaymentListRequest $request): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $status = $request->string('status')->trim()->toString() ?: null;
        $items = $this->payments->listForUser($user, $perPage, $status);
        $data = $items->getCollection()->map(fn (PaymentIntent $item): array => (new PaymentIntentResource($item))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách thanh toán.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'filters' => ['status' => $status],
        ]);
    }

    public function history(PaymentHistoryListRequest $request): JsonResponse
    {
        /** @var Nguoi $user */
        $user = $request->user('api');
        $items = $this->payments->historyForUser(
            $user,
            $request->integer('per_page', 15),
            $request->validated(),
        );
        $data = $items->getCollection()
            ->map(fn ($item): array => (new PaymentHistoryResource($item))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($data, 'Lịch sử giao dịch thanh toán.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'filters' => [
                'loai_gd' => $request->input('loai_gd'),
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ],
        ]);
    }

    public function show(Request $request, string $intent): JsonResponse
    {
        $item = $this->payments->findForUser($request->user('api'), $intent);

        return ApiResponse::success(new PaymentIntentResource($item), 'Chi tiết thanh toán.', 200, $request);
    }

    public function invoice(Request $request, string $intent): JsonResponse
    {
        $item = $this->payments->findForUser($request->user('api'), $intent);

        return ApiResponse::success(new PaymentInvoiceResource($this->payments->invoice($item)), 'Thông tin hóa đơn.', 200, $request);
    }

    public function checkout(Request $request, string $intent): JsonResponse
    {
        $item = $this->payments->findForUser($request->user('api'), $intent);

        return ApiResponse::success($this->payments->checkout($item), 'Thông tin thanh toán.', 200, $request);
    }

    public function cassoWebhook(Request $request): JsonResponse
    {
        $intent = $this->payments->handleCassoWebhook($request->all(), $request->header('X-API-KEY'));

        return ApiResponse::success(['payment_intent_id' => $intent->getKey(), 'status' => $intent->status->value], 'Webhook đã được xử lý.', 200, $request);
    }
}

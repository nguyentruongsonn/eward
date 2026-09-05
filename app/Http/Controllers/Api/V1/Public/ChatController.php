<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Exceptions\ChatAssistantException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Services\Chat\ChatAssistantService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct(private readonly ChatAssistantService $assistant) {}

    public function sendMessage(ChatMessageRequest $request): JsonResponse
    {
        try {
            return ApiResponse::success([
                'reply' => $this->assistant->reply($request->string('message')->toString()),
            ], 'Tư vấn thành công.', 200, $request);
        } catch (ChatAssistantException $exception) {
            return ApiResponse::error($exception->getMessage(), 'CHAT_PROVIDER_UNAVAILABLE', 503, [], $request);
        } catch (\Throwable $exception) {
            Log::error('Chat API request failed', ['exception' => $exception::class]);

            return ApiResponse::error('Không thể xử lý yêu cầu tư vấn.', 'CHAT_PROVIDER_ERROR', 500, [], $request);
        }
    }
}

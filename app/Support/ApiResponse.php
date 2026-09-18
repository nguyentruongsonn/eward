<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'OK',
        int $status = 200,
        ?Request $request = null,
        array $meta = [],
    ): JsonResponse {
        $responseMeta = self::meta($request, $meta);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'request_id' => $responseMeta['request_id'],
            'meta' => $responseMeta,
        ], $status);
    }

    public static function error(
        string $message,
        string $errorCode = 'API_ERROR',
        int $status = 400,
        array $details = [],
        ?Request $request = null,
    ): JsonResponse {
        $errors = ['code' => $errorCode];
        if ($details !== []) {
            $errors['details'] = $details;
        }

        $responseMeta = self::meta($request);

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'request_id' => $responseMeta['request_id'],
            'meta' => $responseMeta,
        ], $status);
    }

    public static function validation(array $errors, ?Request $request = null): JsonResponse
    {
        $responseMeta = self::meta($request);

        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'data' => null,
            'errors' => [
                'code' => 'VALIDATION_ERROR',
                'fields' => $errors,
            ],
            'request_id' => $responseMeta['request_id'],
            'meta' => $responseMeta,
        ], 422);
    }

    private static function meta(?Request $request, array $extra = []): array
    {
        $request ??= request();
        $requestId = $request->attributes->get('request_id')
            ?? $request->header('X-Request-Id')
            ?? 'req_'.Str::lower((string) Str::uuid());

        return array_merge([
            'request_id' => $requestId,
            'timestamp' => now()->toIso8601String(),
        ], $extra);
    }
}

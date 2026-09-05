<?php

namespace App\Http\Controllers;

use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiOnlyFallbackController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if (! $request->is('api/*')) {
            $candidates = [
                public_path('index.html'),
                base_path('../public_html/index.html'),
            ];

            foreach ($candidates as $candidate) {
                if (file_exists($candidate)) {
                    return response()->file($candidate);
                }
            }
        }

        return ApiResponse::error(
            'Không tìm thấy tài nguyên.',
            'NOT_FOUND',
            404,
            [],
            $request,
        );
    }
}

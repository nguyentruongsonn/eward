<?php

namespace App\Http\Controllers\Api\V1\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\HoSo\ResultFileListRequest;
use App\Http\Resources\Api\V1\ResultFileResource;
use App\Models\HoSoResultFile;
use App\Models\HoSoXuLy;
use App\Services\Files\ApplicationResultFileService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultFileController extends Controller
{
    public function __construct(private readonly ApplicationResultFileService $files) {}

    public function index(ResultFileListRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewResultFile', $item);
        $items = $this->files->paginate($item, $request->integer('per_page', 15));
        $data = $items->getCollection()->map(fn (HoSoResultFile $file): array => (new ResultFileResource($file))->resolve($request))->values()->all();

        return ApiResponse::success($data, 'Danh sách file kết quả.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $application, int $file): mixed
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewResultFile', $item);
        $resultFile = $this->files->findForApplication($item, $file);

        return $this->files->download($resultFile);
    }
}

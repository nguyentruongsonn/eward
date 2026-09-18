<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\SignResultFileRequest;
use App\Http\Requests\Api\V1\Admin\UploadResultFileRequest;
use App\Http\Requests\Api\V1\HoSo\ResultFileListRequest;
use App\Http\Resources\Api\V1\ResultFileResource;
use App\Models\HoSoResultFile;
use App\Models\HoSoXuLy;
use App\Services\Files\ApplicationResultFileService;
use App\Services\Files\ResultFileSignatureService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultFileController extends Controller
{
    public function __construct(
        private readonly ApplicationResultFileService $files,
        private readonly ResultFileSignatureService $signatures,
    ) {}

    public function index(ResultFileListRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewStaffResultFile', $item);
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

    public function store(UploadResultFileRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('manageResultFile', $item);
        $file = $this->files->upload($item, $request->user('api'), $request->file('file'));

        return ApiResponse::success(new ResultFileResource($file), 'Đã tải file kết quả.', 201, $request);
    }

    public function show(Request $request, string $application, int $file): mixed
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewStaffResultFile', $item);
        $resultFile = $this->files->findForApplication($item, $file);

        return $this->files->download($resultFile);
    }

    public function destroy(Request $request, string $application, int $file): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('manageResultFile', $item);
        $resultFile = $this->files->findForApplication($item, $file);
        $this->files->delete($resultFile);

        return ApiResponse::success(['deleted' => true, 'id' => $file], 'Đã xóa file kết quả.', 200, $request);
    }

    public function sign(SignResultFileRequest $request, string $application, int $file): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('signResultFile', $item);
        $resultFile = $this->files->findForApplication($item, $file);
        $updated = $this->signatures->sign($item, $resultFile, $request->user('api'), $request->input('note'));

        return ApiResponse::success(new \App\Http\Resources\Api\V1\AdminApplicationResource($updated), 'Đã xác nhận file kết quả bằng SHA-256.', 200, $request);
    }
}

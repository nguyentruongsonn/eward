<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\UploadOpinionFileRequest;
use App\Http\Requests\Api\V1\HoSo\ResultFileListRequest;
use App\Http\Resources\Api\V1\OpinionFileResource;
use App\Models\HoSoOpinionFile;
use App\Models\HoSoXuLy;
use App\Services\Files\ApplicationOpinionFileService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpinionFileController extends Controller
{
    public function __construct(private readonly ApplicationOpinionFileService $files) {}

    public function index(ResultFileListRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewOpinionFile', $item);
        $items = $this->files->paginate($item, $request->integer('per_page', 15));
        $data = $items->getCollection()
            ->map(fn (HoSoOpinionFile $file): array => (new OpinionFileResource($file))->resolve($request))
            ->values()
            ->all();

        return ApiResponse::success($data, 'Danh sách file ý kiến xử lý.', 200, $request, [
            'pagination' => [
                'page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ]);
    }

    public function store(UploadOpinionFileRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('manageOpinionFile', $item);
        $file = $this->files->upload($item, $request->user('api'), $request->file('file'));

        return ApiResponse::success(new OpinionFileResource($file), 'Đã tải file ý kiến xử lý.', 201, $request);
    }

    public function show(Request $request, string $application, int $file): mixed
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewOpinionFile', $item);
        $opinionFile = $this->files->findForApplication($item, $file);

        return $this->files->download($opinionFile);
    }

    public function destroy(Request $request, string $application, int $file): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('manageOpinionFile', $item);
        $opinionFile = $this->files->findForApplication($item, $file);
        $this->files->delete($opinionFile);

        return ApiResponse::success(['deleted' => true, 'id' => $file], 'Đã xóa file ý kiến xử lý.', 200, $request);
    }
}

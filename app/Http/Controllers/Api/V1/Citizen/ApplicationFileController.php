<?php

namespace App\Http\Controllers\Api\V1\Citizen;

use App\Contracts\Files\FileStorage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\HoSo\UploadCitizenApplicationDocumentRequest;
use App\Http\Requests\Api\V1\HoSo\UploadSupplementRequest;
use App\Http\Resources\Api\V1\FileResource;
use App\Models\HoSoXuLy;
use App\Models\TaiLieuNop;
use App\Services\Files\ApplicationDocumentService;
use App\Services\HoSo\CitizenSupplementService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationFileController extends Controller
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly ApplicationDocumentService $documents,
        private readonly CitizenSupplementService $supplements,
    ) {}

    public function supplement(UploadSupplementRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('uploadSupplement', $item);
        $actor = $request->user('api');
        $files = array_map(
            static fn (TaiLieuNop $file): FileResource => new FileResource($file),
            $this->supplements->submit(
                $actor,
                $item,
                $request->file('documents', []),
                $request->input('document_types', []),
            ),
        );

        return ApiResponse::success($files, 'Đã tải tài liệu bổ sung.', 201, $request);
    }

    public function uploadDraft(UploadCitizenApplicationDocumentRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('update', $item);
        $file = $this->documents->replaceDraftDocument(
            $request->file('file'),
            $item,
            $request->integer('document_type'),
            $request->user('api'),
            $this->storage,
        );

        return ApiResponse::success(new FileResource($file), 'Đã cập nhật tài liệu thành phần hồ sơ.', 201, $request);
    }

    public function show(Request $request, string $application, int $file): mixed
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('view', $item);
        $document = $this->documents->findForApplication($item, $file);

        return $this->storage->download($document);
    }
}

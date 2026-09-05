<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\Files\FileStorage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\UploadApplicationComponentFileRequest;
use App\Http\Resources\Api\V1\FileResource;
use App\Models\HoSoXuLy;
use App\Services\Files\ApplicationDocumentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationFileController extends Controller
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly ApplicationDocumentService $documents,
    ) {}

    public function store(UploadApplicationComponentFileRequest $request, string $application): JsonResponse
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('uploadComponentFile', $item);
        $file = $this->documents->storeForDocumentType(
            $request->file('file'),
            $item,
            $request->integer('document_type'),
            $request->user('api'),
            $this->storage,
        );

        return ApiResponse::success(new FileResource($file), 'Đã tải tài liệu thành phần hồ sơ.', 201, $request);
    }

    public function show(Request $request, string $application, int $file): mixed
    {
        $item = HoSoXuLy::query()->findOrFail($application);
        $this->authorize('viewStaff', $item);
        $document = $this->documents->findForApplication($item, $file);

        return $this->storage->download($document);
    }
}

<?php

namespace App\Services\Files;

use App\Contracts\Files\FileStorage;
use App\Exceptions\ApiException;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Models\TaiLieuNop;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ApplicationDocumentService
{
    public function findForApplication(HoSoXuLy $application, int $fileId): TaiLieuNop
    {
        return $application->files()->whereKey($fileId)->firstOrFail();
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, int|string>  $documentTypes
     * @return array<int, TaiLieuNop>
     */
    public function storeSupplementDocuments(
        array $files,
        array $documentTypes,
        HoSoXuLy $application,
        Nguoi $actor,
        FileStorage $storage,
    ): array {
        $stored = [];
        foreach ($files as $index => $file) {
            $stored[] = $this->storeSupplementDocument(
                $file,
                $application,
                (int) ($documentTypes[$index] ?? 0),
                $actor,
                $storage,
            );
        }

        return $stored;
    }

    public function storeSupplementDocument(
        UploadedFile $file,
        HoSoXuLy $application,
        int $documentType,
        Nguoi $actor,
        FileStorage $storage,
    ): TaiLieuNop {
        return $storage->storeApplicationFile($file, $application, $documentType, $actor);
    }

    public function storeForComponent(
        UploadedFile $file,
        HoSoXuLy $application,
        string $componentName,
        Nguoi $actor,
        FileStorage $storage,
    ): TaiLieuNop {
        $documentType = DB::table('thanhphanhoso')
            ->join('thanhphangiayto', 'thanhphangiayto.maThanhPhan', '=', 'thanhphanhoso.maThanhPhan')
            ->where('thanhphanhoso.maTTHC', $application->maTTHC)
            ->where('thanhphanhoso.tenThanhPhan', $componentName)
            ->orderBy('thanhphangiayto.maTPGT')
            ->value('thanhphangiayto.maGiayTo');

        if ($documentType === null) {
            throw new ApiException(
                'Không xác định được loại giấy tờ của thành phần hồ sơ.',
                'APPLICATION_COMPONENT_DOCUMENT_NOT_FOUND',
                422,
            );
        }

        return $storage->storeApplicationFile($file, $application, (int) $documentType, $actor);
    }

    public function storeForDocumentType(
        UploadedFile $file,
        HoSoXuLy $application,
        int $documentType,
        Nguoi $actor,
        FileStorage $storage,
    ): TaiLieuNop {
        $belongsToProcedure = DB::table('thanhphanhoso')
            ->join('thanhphangiayto', 'thanhphangiayto.maThanhPhan', '=', 'thanhphanhoso.maThanhPhan')
            ->where('thanhphanhoso.maTTHC', $application->maTTHC)
            ->where('thanhphangiayto.maGiayTo', $documentType)
            ->exists();

        if (! $belongsToProcedure) {
            throw new ApiException(
                'Loại giấy tờ không thuộc thủ tục của hồ sơ.',
                'APPLICATION_DOCUMENT_TYPE_INVALID',
                422,
            );
        }

        return $storage->storeApplicationFile($file, $application, $documentType, $actor);
    }
}

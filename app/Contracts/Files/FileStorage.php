<?php

namespace App\Contracts\Files;

use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Models\TaiLieuNop;
use Illuminate\Http\UploadedFile;

interface FileStorage
{
    public function storeApplicationFile(UploadedFile $file, HoSoXuLy $application, int $documentType, Nguoi $actor): TaiLieuNop;

    public function download(TaiLieuNop $file): mixed;
}

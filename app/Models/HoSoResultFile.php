<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoResultFile extends Model
{
    use HasFactory;

    protected $table = 'hoso_result_files';

    protected $fillable = [
        'maHSXL',
        'original_name',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function application()
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }

    public function uploader()
    {
        return $this->belongsTo(Nguoi::class, 'uploaded_by', 'IDnguoiDung');
    }
}

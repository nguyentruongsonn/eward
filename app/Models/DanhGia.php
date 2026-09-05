<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danhgia';

    protected $fillable = [
        'maHSXL',
        'soDiem',
        'nhanXet',
        'IDCD',
        'ngayDanhGia',
    ];

    protected $casts = [
        'soDiem' => 'integer',
        'ngayDanhGia' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }
}

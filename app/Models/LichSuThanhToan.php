<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuThanhToan extends Model
{
    use HasFactory;

    protected $table = 'lichsuthanhtoan';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'maGD',
        'soGD',
        'loaiGD',
        'ngayGD',
        'soTien',
        'trangThai',
        'IDCD',
        'maHSXL',
        'moTa',
    ];

    protected $casts = [
        'ngayGD' => 'datetime',
        'soTien' => 'float',
    ];

    public function hoso()
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }
}

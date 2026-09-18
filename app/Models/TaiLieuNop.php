<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaiLieuNop extends Model
{
    use HasFactory;

    protected $table = 'tailieunop';

    protected $primaryKey = 'taiLieuID';

    public $timestamps = false;

    protected $fillable = [
        'maHSXL',
        'maGiayTo',
        'tenTep',
        'duongDan',
        'dinhDang',
        'kichThuoc',
        'ngayTai',
    ];

    protected $casts = [
        'ngayTai' => 'datetime',
    ];

    public function hoSo()
    {
        return $this->belongsTo(HoSoXuLy::class, 'maHSXL', 'maHSXL');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiayTo extends Model
{
    use HasFactory;

    protected $table = 'giayto';

    protected $primaryKey = 'maGiayTo';

    public $timestamps = false;

    protected $fillable = [
        'tenGiayTo',
        'loaiGiayTo',
    ];

    public function thanhPhanHoSos()
    {
        return $this->belongsToMany(
            ThanhPhanHoSo::class,
            'thanhphangiayto',
            'maGiayTo',
            'maThanhPhan',
            'maGiayTo',
            'maThanhPhan'
        )->withPivot('soLuongBanChinh', 'soLuongBanSao');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhPhanHoSo extends Model
{
    use HasFactory;

    protected $table = 'thanhphanhoso';

    protected $primaryKey = 'maThanhPhan';

    public $timestamps = false;

    protected $fillable = [
        'maTTHC', 'tenThanhPhan',
    ];

    public function tthc()
    {
        return $this->belongsTo(TTHC::class, 'maTTHC', 'maTTHC');
    }

    public function giayTos()
    {
        return $this->belongsToMany(
            \App\Models\GiayTo::class,
            'thanhphangiayto',
            'maThanhPhan',
            'maGiayTo',
            'maThanhPhan',
            'maGiayTo'
        )->withPivot('soLuongBanChinh', 'soLuongBanSao');
    }
}

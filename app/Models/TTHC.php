<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TTHC extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\TTHCFactory
    {
        return \Database\Factories\TTHCFactory::new();
    }

    protected $table = 'tthc';

    protected $primaryKey = 'maTTHC';

    protected $fillable = [
        'tenTTHC',
        'maLinhVuc',
        'maQuayLamViec',
        'doiTuongThucHien',
        'trinhTuThucHien',
        'thoiHanGiaiQuyet',
        'phi',
        'lePhi',
        'yeuCauDieuKien',
        'canCuPhapLy',
        'ketQuaThucHien',
    ];

    public $timestamps = false;

    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function (Builder $inner): void {
            $inner->where('trangThai', 'Công khai')->orWhereNull('trangThai');
        });
    }

    public function linhVuc()
    {
        return $this->belongsTo(\App\Models\LinhVuc::class, 'maLinhVuc', 'maLinhVuc');
    }

    public function cachThucHiens()
    {
        return $this->hasMany(\App\Models\CachThucHien::class, 'maTTHC', 'maTTHC');
    }

    public function thanhPhanHoSos()
    {
        return $this->hasMany(\App\Models\ThanhPhanHoSo::class, 'maTTHC', 'maTTHC');
    }

    public function doiTuongs()
    {
        return $this->belongsToMany(\App\Models\DoiTuongThucHien::class, 'thutucdoituong', 'maTTHC', 'maDoiTuong');
    }

    public function lephis()
    {
        return $this->hasMany(\App\Models\LePhi::class, 'maTTHC', 'maTTHC');
    }

    public function formConfig()
    {
        return $this->hasOne(\App\Models\CauHinhForm::class, 'maTTHC', 'maTTHC');
    }
}

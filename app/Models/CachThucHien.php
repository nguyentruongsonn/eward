<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CachThucHien extends Model
{
    use HasFactory;

    protected $table = 'cachthuchien';

    protected $primaryKey = 'maCTH';

    public $timestamps = false;

    protected $fillable = [
        'maTTHC', 'kenh', 'thoiHanGiaiQuyet', 'moTaPhiLePhi', 'moTa',
    ];
}

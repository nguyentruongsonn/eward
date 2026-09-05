<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrangThaiHoSo extends Model
{
    use HasFactory;

    protected $table = 'trangthaihoso';

    protected $primaryKey = 'maTrangThai';

    public $timestamps = false;

    protected $fillable = [
        'tenTrangThai',
    ];
}

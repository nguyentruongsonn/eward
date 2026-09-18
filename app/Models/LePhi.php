<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LePhi extends Model
{
    use HasFactory;

    protected $table = 'lephi';

    protected $primaryKey = 'maLePhi';

    public $timestamps = false;

    protected $fillable = [
        'loaiLePhi',
        'maTTHC',
        'soTien',
        'batBuoc',
        'moTa',
    ];

    protected $casts = [
        'soTien' => 'float',
    ];
}

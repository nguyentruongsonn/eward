<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tinh extends Model
{
    use HasFactory;

    protected $table = 'tinh';

    protected $primaryKey = 'maTinh';

    protected $fillable = [
        'tenTinh',
        'tenTinhKhongDau',
    ];

    public function xas()
    {
        return $this->hasMany(Xa::class, 'maTinh', 'maTinh');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Xa extends Model
{
    use HasFactory;

    protected $table = 'xa';

    protected $primaryKey = 'maXa';

    protected $fillable = [
        'tenXa',
        'maTinh',
    ];

    public function tinh()
    {
        return $this->belongsTo(Tinh::class, 'maTinh', 'maTinh');
    }
}

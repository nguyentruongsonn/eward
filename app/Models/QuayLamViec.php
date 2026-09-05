<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuayLamViec extends Model
{
    use HasFactory;

    protected $table = 'quaylamviec';

    protected $primaryKey = 'maQuayLamViec';

    public $timestamps = false;

    protected $fillable = [
        'tenQuayLamViec',
    ];

    public function lichHens()
    {
        return $this->hasMany(LichHen::class, 'maQuayLamViec', 'maQuayLamViec');
    }
}

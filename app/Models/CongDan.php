<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongDan extends Model
{
    use HasFactory;

    protected $table = 'congdan';

    protected $primaryKey = 'IDCD';

    public $timestamps = false;

    protected $fillable = [
        'IDnguoiDung',
    ];

    public function nguoi()
    {
        return $this->belongsTo(Nguoi::class, 'IDnguoiDung', 'IDnguoiDung');
    }

    public function hoSoXuLys()
    {
        return $this->hasMany(HoSoXuLy::class, 'IDCD', 'IDCD');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thongbao';

    protected $primaryKey = 'id';

    protected $fillable = [
        'IDCD',
        'tieuDe',
        'noiDung',
        'loai',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function congdan()
    {
        return $this->belongsTo(CongDan::class, 'IDCD', 'IDCD');
    }
}

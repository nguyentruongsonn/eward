<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinhVuc extends Model
{
    use HasFactory;

    protected $table = 'linhvuc';

    protected $primaryKey = 'maLinhVuc';

    public $timestamps = false;

    protected $fillable = [
        'tenLinhVuc',
    ];
}

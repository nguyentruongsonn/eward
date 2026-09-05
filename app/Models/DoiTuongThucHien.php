<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoiTuongThucHien extends Model
{
    use HasFactory;

    protected $table = 'doituongthuchien';

    protected $primaryKey = 'maDoiTuong';

    public $timestamps = false;
}

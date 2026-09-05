<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHinhForm extends Model
{
    use HasFactory;

    protected $table = 'formtructuyen';

    protected $primaryKey = 'maForm';

    public $incrementing = true;

    protected $fillable = [
        'maForm',
        'maTTHC',
        'cauHinhForm',
    ];

    protected $casts = [
        'cauHinhForm' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordChangeOtp extends Model
{
    use HasFactory;

    protected $table = 'password_change_otps';

    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'attempts',
        'challenge_id',
        'nguoi_dung_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}

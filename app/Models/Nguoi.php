<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Nguoi extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'nguoi';

    protected $primaryKey = 'IDnguoiDung';

    public $timestamps = false;

    protected $fillable = [
        'maCCCD',
        'hoTen',
        'gioiTinh',
        'ngaySinh',
        'queQuan',
        'noiThuongTru',
        'noiTamTru',
        'email',
        'password',
        'soDienThoai',
        'vaiTro',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'role' => trim((string) $this->vaiTro),
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'IDnguoiDung', 'IDnguoiDung');
    }

    public function congDan()
    {
        return $this->hasOne(CongDan::class, 'IDnguoiDung', 'IDnguoiDung');
    }
}

<?php

namespace App\Services\Auth;

use App\Models\Nguoi;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function createCitizen(array $payload): Nguoi
    {
        return DB::transaction(function () use ($payload): Nguoi {
            $user = Nguoi::create([
                'maCCCD' => $payload['citizen_id'],
                'hoTen' => $payload['full_name'],
                'gioiTinh' => $payload['gender'] ?? null,
                'ngaySinh' => $payload['birth_date'] ?? null,
                'queQuan' => $payload['hometown'] ?? null,
                'noiThuongTru' => $payload['permanent_address'] ?? null,
                'noiTamTru' => $payload['temporary_address'] ?? null,
                'email' => $payload['email'],
                'password' => $payload['password'],
                'soDienThoai' => $payload['phone'],
                'vaiTro' => 'Công dân/ Tổ chức',
            ]);

            DB::table('congdan')->insertOrIgnore(['IDnguoiDung' => $user->getKey()]);

            return $user;
        });
    }
}

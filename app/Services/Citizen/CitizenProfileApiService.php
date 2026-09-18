<?php

namespace App\Services\Citizen;

use App\Enums\Role;
use App\Models\Nguoi;
use Illuminate\Auth\Access\AuthorizationException;

class CitizenProfileApiService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateProfile(Nguoi $user, array $attributes): Nguoi
    {
        $this->assertCitizen($user);

        $user->fill($this->map($attributes, [
            'full_name' => 'hoTen',
            'email' => 'email',
            'phone' => 'soDienThoai',
        ]));
        $user->save();

        return $user->fresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateIdentity(Nguoi $user, array $attributes): Nguoi
    {
        $this->assertCitizen($user);

        $user->fill($this->map($attributes, [
            'citizen_id' => 'maCCCD',
            'gender' => 'gioiTinh',
            'birth_date' => 'ngaySinh',
            'hometown' => 'queQuan',
            'permanent_address' => 'noiThuongTru',
            'temporary_address' => 'noiTamTru',
        ]));
        $user->save();

        return $user->fresh();
    }

    public function profile(Nguoi $user): Nguoi
    {
        $this->assertCitizen($user);

        return $user->fresh();
    }

    private function assertCitizen(Nguoi $user): void
    {
        if (Role::normalize($user->vaiTro) !== Role::Citizen) {
            throw new AuthorizationException('Bạn không có quyền truy cập hồ sơ công dân.');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, string>  $fieldMap
     * @return array<string, mixed>
     */
    private function map(array $attributes, array $fieldMap): array
    {
        $mapped = [];
        foreach ($fieldMap as $input => $column) {
            if (array_key_exists($input, $attributes)) {
                $mapped[$column] = $attributes[$input];
            }
        }

        return $mapped;
    }
}

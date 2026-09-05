<?php

namespace App\Services\HoSo;

use App\Models\HoSoXuLy;
use Illuminate\Support\Facades\DB;

class ApplicationGeneralInfoService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(HoSoXuLy $application, array $attributes): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $attributes): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $payload = is_array($locked->dulieu)
                ? $locked->dulieu
                : (json_decode((string) $locked->getRawOriginal('dulieu'), true) ?: []);

            $payloadKeys = ['hoTen', 'ngaySinh', 'gioiTinh', 'cccd', 'ngayCap', 'noiCap', 'email', 'soDienThoai', 'diaChi'];
            foreach ($payloadKeys as $key) {
                if (array_key_exists($key, $attributes)) {
                    $payload[$key] = $attributes[$key];
                }
            }

            foreach (['hoTen' => 'tenChuHoSo', 'email' => 'email', 'soDienThoai' => 'soDienThoai'] as $input => $column) {
                if (array_key_exists($input, $attributes)) {
                    $locked->{$column} = $attributes[$input];
                }
            }

            $locked->dulieu = $payload;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }
}

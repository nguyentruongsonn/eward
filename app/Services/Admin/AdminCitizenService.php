<?php

namespace App\Services\Admin;

use App\Enums\Role;
use App\Exceptions\ApiException;
use App\Models\Nguoi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminCitizenService
{
    public function findForUpdate(int $userId): ?Nguoi
    {
        return Nguoi::query()
            ->whereKey($userId)
            ->whereRaw('TRIM(vaiTro) = ?', [Role::Citizen->value])
            ->with('congDan')
            ->first();
    }

    public function findForView(int $userId): ?object
    {
        return DB::table('nguoi')
            ->where('nguoi.IDnguoiDung', $userId)
            ->whereRaw('TRIM(nguoi.vaiTro) = ?', [Role::Citizen->value])
            ->leftJoin('congdan', 'nguoi.IDnguoiDung', '=', 'congdan.IDnguoiDung')
            ->select('nguoi.*', 'congdan.IDCD')
            ->first();
    }

    /** @param array{search?: string|null} $filters */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = DB::table('nguoi')
            ->whereRaw('TRIM(nguoi.vaiTro) = ?', [Role::Citizen->value])
            ->leftJoin('congdan', 'nguoi.IDnguoiDung', '=', 'congdan.IDnguoiDung')
            ->select('nguoi.*', 'congdan.IDCD');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('nguoi.hoTen', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.email', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.soDienThoai', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.maCCCD', 'like', '%'.$search.'%');
            });
        }

        return $query->orderByDesc('nguoi.IDnguoiDung')->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Nguoi
    {
        return DB::transaction(function () use ($attributes): Nguoi {
            $citizen = Nguoi::create([
                'maCCCD' => $attributes['maCCCD'],
                'hoTen' => $attributes['hoTen'],
                'gioiTinh' => $attributes['gioiTinh'] ?? null,
                'ngaySinh' => $attributes['ngaySinh'] ?? null,
                'queQuan' => $attributes['queQuan'] ?? null,
                'noiThuongTru' => $attributes['noiThuongTru'] ?? null,
                'noiTamTru' => $attributes['noiTamTru'] ?? null,
                'soDienThoai' => $attributes['soDienThoai'] ?? null,
                'email' => $attributes['email'],
                'password' => Hash::make($attributes['password']),
                'vaiTro' => 'Công dân/ Tổ chức',
            ]);
            $citizen->congDan()->create();

            return $citizen->fresh('congDan');
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Nguoi $citizen, array $attributes): Nguoi
    {
        return DB::transaction(function () use ($citizen, $attributes): Nguoi {
            $locked = Nguoi::query()->whereKey($citizen->getKey())->lockForUpdate()->firstOrFail();
            $locked->fill([
                'maCCCD' => $attributes['maCCCD'],
                'hoTen' => $attributes['hoTen'],
                'gioiTinh' => $attributes['gioiTinh'] ?? null,
                'ngaySinh' => $attributes['ngaySinh'] ?? null,
                'queQuan' => $attributes['queQuan'] ?? null,
                'noiThuongTru' => $attributes['noiThuongTru'] ?? null,
                'noiTamTru' => $attributes['noiTamTru'] ?? null,
                'soDienThoai' => $attributes['soDienThoai'] ?? null,
                'email' => $attributes['email'],
                'vaiTro' => 'Công dân/ Tổ chức',
            ]);
            if (! empty($attributes['password'])) {
                $locked->password = Hash::make($attributes['password']);
            }
            $locked->save();

            return $locked->fresh('congDan');
        });
    }

    public function delete(int $userId): void
    {
        DB::transaction(function () use ($userId): void {
            $citizen = Nguoi::query()->whereKey($userId)->whereRaw('TRIM(vaiTro) = ?', [Role::Citizen->value])->lockForUpdate()->firstOrFail();
            $citizenId = $citizen->congDan()->value('IDCD');
            $applicationCount = $citizenId === null ? 0 : $citizen->congDan()->first()->hoSoXuLys()->count();
            if ($applicationCount > 0) {
                throw new ApiException('Không thể xóa công dân vì đang có '.$applicationCount.' hồ sơ xử lý liên quan.', 'CITIZEN_HAS_APPLICATIONS', 409);
            }

            $citizen->congDan()->delete();
            $citizen->delete();
        });
    }
}

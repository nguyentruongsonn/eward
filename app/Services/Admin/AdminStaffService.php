<?php

namespace App\Services\Admin;

use App\Enums\Role;
use App\Models\Nguoi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminStaffService
{
    public function counters()
    {
        return DB::table('quaylamviec')->orderBy('maQuayLamViec')->get();
    }

    public function findForView(int $staffId): ?object
    {
        return DB::table('nguoi')
            ->join('canbo', 'nguoi.IDnguoiDung', '=', 'canbo.IDnguoiDung')
            ->leftJoin('quaylamviec', 'canbo.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->where('canbo.IDCB', $staffId)
            ->select('nguoi.*', 'canbo.IDCB', 'canbo.maQuayLamViec', 'canbo.chucVu', 'quaylamviec.tenQuayLamViec')
            ->first();
    }

    /** @param array{search?: string|null, vaiTro?: string|null} $filters */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = DB::table('nguoi')
            ->whereRaw('TRIM(nguoi.vaiTro) IN (?, ?)', [Role::OneStopOfficer->value, Role::CaseOfficer->value])
            ->leftJoin('canbo', 'nguoi.IDnguoiDung', '=', 'canbo.IDnguoiDung')
            ->leftJoin('quaylamviec', 'canbo.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->select('nguoi.*', 'canbo.IDCB', 'canbo.maQuayLamViec', 'canbo.chucVu', 'quaylamviec.tenQuayLamViec');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('nguoi.hoTen', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.email', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.soDienThoai', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.maCCCD', 'like', '%'.$search.'%');
            });
        }

        $role = trim((string) ($filters['vaiTro'] ?? ''));
        if ($role !== '') {
            $query->whereRaw('TRIM(nguoi.vaiTro) = ?', [$role]);
        }

        return $query->orderByDesc('nguoi.IDnguoiDung')->paginate($perPage)->withQueryString();
    }

    public function find(int $staffId): ?object
    {
        return DB::table('canbo')->where('IDCB', $staffId)->first();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Nguoi
    {
        $role = Role::normalize($attributes['vaiTro'] ?? null);
        if ($role === null || ! in_array($role, [Role::OneStopOfficer, Role::CaseOfficer], true)) {
            throw new \InvalidArgumentException('Vai trò cán bộ không hợp lệ.');
        }

        return DB::transaction(function () use ($attributes, $role): Nguoi {
            $staff = Nguoi::create([
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
                'vaiTro' => $role->storageValue(),
            ]);
            DB::table('canbo')->insert([
                'IDnguoiDung' => $staff->getKey(),
                'maQuayLamViec' => $attributes['maQuayLamViec'] ?? null,
            ]);

            return $staff->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(int $staffId, array $attributes): Nguoi
    {
        $role = Role::normalize($attributes['vaiTro'] ?? null);
        if ($role === null || ! in_array($role, [Role::OneStopOfficer, Role::CaseOfficer], true)) {
            throw new \InvalidArgumentException('Vai trò cán bộ không hợp lệ.');
        }

        return DB::transaction(function () use ($staffId, $attributes, $role): Nguoi {
            $assignment = DB::table('canbo')->where('IDCB', $staffId)->lockForUpdate()->firstOrFail();
            $staff = Nguoi::query()->whereKey($assignment->IDnguoiDung)->lockForUpdate()->firstOrFail();
            $staff->fill([
                'maCCCD' => $attributes['maCCCD'],
                'hoTen' => $attributes['hoTen'],
                'gioiTinh' => $attributes['gioiTinh'] ?? null,
                'ngaySinh' => $attributes['ngaySinh'] ?? null,
                'queQuan' => $attributes['queQuan'] ?? null,
                'noiThuongTru' => $attributes['noiThuongTru'] ?? null,
                'noiTamTru' => $attributes['noiTamTru'] ?? null,
                'soDienThoai' => $attributes['soDienThoai'] ?? null,
                'email' => $attributes['email'],
                'vaiTro' => $role->storageValue(),
            ]);
            if (! empty($attributes['password'])) {
                $staff->password = Hash::make($attributes['password']);
            }
            $staff->save();
            DB::table('canbo')->where('IDCB', $staffId)->update([
                'maQuayLamViec' => $attributes['maQuayLamViec'] ?? null,
            ]);

            return $staff->fresh();
        });
    }

    public function delete(int $staffId): void
    {
        DB::transaction(function () use ($staffId): void {
            $assignment = DB::table('canbo')->where('IDCB', $staffId)->lockForUpdate()->firstOrFail();
            DB::table('canbo')->where('IDCB', $staffId)->delete();
            Nguoi::query()->whereKey($assignment->IDnguoiDung)->delete();
        });
    }
}

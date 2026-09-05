<?php

namespace App\Services\Admin;

use App\Enums\Role;
use App\Exceptions\ApiException;
use App\Models\Nguoi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserService
{
    /** @param array{search?: string|null, role?: string|null} $filters */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Nguoi::query();
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('hoTen', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('maCCCD', 'like', '%'.$search.'%')
                    ->orWhere('soDienThoai', 'like', '%'.$search.'%');
            });
        }

        $role = Role::normalize($filters['role'] ?? null);
        if ($role !== null) {
            $query->whereRaw('TRIM(vaiTro) = ?', [$role->value]);
        }

        return $query
            ->orderBy('IDnguoiDung')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();
    }

    /** @param array{full_name:string,email:string,password:string,phone:string,citizen_id?:string|null,role:string} $attributes */
    public function create(array $attributes): Nguoi
    {
        $role = Role::normalize($attributes['role'] ?? null);
        if ($role === null) {
            throw new ApiException('Vai trò không hợp lệ.', 'ROLE_INVALID', 422);
        }

        return DB::transaction(function () use ($attributes, $role): Nguoi {
            return Nguoi::query()->create([
                'hoTen' => $attributes['full_name'],
                'email' => $attributes['email'],
                'password' => Hash::make($attributes['password']),
                'soDienThoai' => $attributes['phone'],
                'maCCCD' => $attributes['citizen_id'] ?? null,
                'vaiTro' => $role->storageValue(),
            ]);
        });
    }

    /** @param array<string, mixed> $attributes */
    public function update(int $userId, array $attributes): Nguoi
    {
        $role = array_key_exists('role', $attributes) ? Role::normalize($attributes['role']) : null;
        if (array_key_exists('role', $attributes) && $role === null) {
            throw new ApiException('Vai trò không hợp lệ.', 'ROLE_INVALID', 422);
        }

        return DB::transaction(function () use ($userId, $attributes, $role): Nguoi {
            $user = Nguoi::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
            $user->fill(array_filter([
                'hoTen' => $attributes['full_name'] ?? null,
                'email' => $attributes['email'] ?? null,
                'soDienThoai' => $attributes['phone'] ?? null,
                'vaiTro' => $role?->storageValue(),
            ], static fn (mixed $value): bool => $value !== null));
            if (isset($attributes['password'])) {
                $user->password = Hash::make($attributes['password']);
            }
            $user->save();

            return $user;
        });
    }

    public function delete(int $userId, int $actorId): void
    {
        if ($userId === $actorId) {
            throw new ApiException('Không thể tự xóa tài khoản đang đăng nhập.', 'SELF_DELETE_FORBIDDEN', 409);
        }

        DB::transaction(function () use ($userId): void {
            Nguoi::query()->whereKey($userId)->lockForUpdate()->firstOrFail()->delete();
        });
    }
}

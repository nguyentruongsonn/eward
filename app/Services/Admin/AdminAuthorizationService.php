<?php

namespace App\Services\Admin;

use App\Enums\Role;
use App\Models\Nguoi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes the legacy web application's role and admin-table checks.
 *
 * The web guard normally returns Nguoi, but this resolver also accepts the
 * legacy User model so callers do not need to know which relation was loaded.
 */
class AdminAuthorizationService
{
    public function isAdmin(mixed $user): bool
    {
        $nguoi = $this->resolveNguoi($user);
        if (! $nguoi) {
            return false;
        }

        if (Role::normalize($nguoi->vaiTro)?->isStaff()) {
            return true;
        }

        return DB::table('quantrivien')
            ->where('IDnguoiDung', $nguoi->getKey())
            ->exists();
    }

    public function isAppointmentStaff(mixed $user): bool
    {
        return Role::normalize($this->resolveNguoi($user)?->vaiTro)?->canAccessAppointments() ?? false;
    }

    public function isSuperAdmin(mixed $user): bool
    {
        return Role::normalize($this->resolveNguoi($user)?->vaiTro)?->isAdministrator() ?? false;
    }

    public function hasRole(mixed $user, Role ...$roles): bool
    {
        $role = Role::normalize($this->resolveNguoi($user)?->vaiTro);

        return $role !== null && in_array($role, $roles, true);
    }

    private function resolveNguoi(mixed $user): ?Nguoi
    {
        if ($user instanceof Nguoi) {
            return $user;
        }

        if ($user instanceof Model && method_exists($user, 'nguoi')) {
            $nguoi = $user->nguoi;

            return $nguoi instanceof Nguoi ? $nguoi : null;
        }

        return null;
    }
}

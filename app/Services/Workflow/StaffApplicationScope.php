<?php

namespace App\Services\Workflow;

use App\Enums\Role;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;

final class StaffApplicationScope
{
    /** @return list<int>|null */
    public static function statusesFor(?Role $role): ?array
    {
        return match ($role) {
            Role::OneStopOfficer => [1, 2, 9, 11],
            Role::CaseOfficer => [2, 4, 5, 6, 12],
            Role::Leader => [4, 9, 12],
            Role::Administrator => null,
            default => [],
        };
    }

    public static function canView(Nguoi $actor, HoSoXuLy $application): bool
    {
        $role = Role::normalize($actor->vaiTro);
        if (! $role || ! $role->isStaff()) {
            return false;
        }

        if ($role->isAdministrator()) {
            return true;
        }

        $viewableStatuses = match ($role) {
            Role::OneStopOfficer => [1, 2, 3, 5, 6, 9, 10, 11, 12],
            Role::CaseOfficer => [2, 4, 5, 6, 8, 9, 10, 12],
            Role::Leader => [4, 9, 10, 12],
            default => self::statusesFor($role) ?? [],
        };

        return in_array((int) $application->maTrangThai, $viewableStatuses, true);
    }
}

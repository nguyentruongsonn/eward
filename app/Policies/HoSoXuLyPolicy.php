<?php

namespace App\Policies;

use App\Enums\HoSoStatus;
use App\Enums\Role;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Services\Workflow\StaffApplicationScope;

class HoSoXuLyPolicy
{
    public function before(Nguoi $user): ?bool
    {
        $role = Role::normalize($user->vaiTro);

        return $role?->isAdministrator() ? true : null;
    }

    public function view(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->owns($user, $application) || StaffApplicationScope::canView($user, $application);
    }

    /**
     * The admin API must never fall back to citizen ownership. Citizen reads
     * have their own /citizen endpoints and resource contract.
     */
    public function viewStaff(Nguoi $user, HoSoXuLy $application): bool
    {
        return StaffApplicationScope::canView($user, $application);
    }

    public function update(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->owns($user, $application)
            && in_array((int) $application->maTrangThai, [HoSoStatus::PendingPayment->value, HoSoStatus::PendingReception->value], true);
    }

    public function cancel(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->owns($user, $application);
    }

    public function uploadSupplement(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->owns($user, $application) && (int) $application->maTrangThai === 5;
    }

    public function rate(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->owns($user, $application) && (int) $application->maTrangThai === 10;
    }

    public function transition(Nguoi $user, HoSoXuLy $application, int $targetStatus): bool
    {
        $role = Role::normalize($user->vaiTro);
        $current = HoSoStatus::tryFrom((int) $application->maTrangThai);
        $target = HoSoStatus::tryFrom($targetStatus);

        return $role !== null
            && $current !== null
            && $target !== null
            && $role->canUseGenericTransition($current, $target);
    }

    public function accept(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function confirmCounterPayment(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function completeDirectReception(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function reject(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true)
            && (int) $application->maTrangThai === HoSoStatus::PendingReception->value;
    }

    public function requestSupplement(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::CaseOfficer, Role::Administrator], true);
    }

    public function uploadComponentFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true)
            && (int) $application->maTrangThai === HoSoStatus::DirectReception->value;
    }

    public function confirmReception(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function forward(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::CaseOfficer, Role::Administrator], true);
    }

    public function approve(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::Leader, Role::Administrator], true);
    }

    public function rework(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::Leader, Role::Administrator], true);
    }

    public function deliver(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function updateGeneralInfo(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function comment(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->isStaff($user);
    }

    public function manageResultFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->isStaff($user) && Role::normalize($user->vaiTro) !== Role::OneStopOfficer;
    }

    public function manageOpinionFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return $this->isStaff($user) && Role::normalize($user->vaiTro) !== Role::OneStopOfficer;
    }

    public function viewOpinionFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return StaffApplicationScope::canView($user, $application);
    }

    public function viewResultFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return StaffApplicationScope::canView($user, $application)
            || ($this->owns($user, $application) && (int) $application->maTrangThai === 10);
    }

    public function viewStaffResultFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return StaffApplicationScope::canView($user, $application);
    }

    public function signResultFile(Nguoi $user, HoSoXuLy $application): bool
    {
        return in_array(Role::normalize($user->vaiTro), [Role::Leader, Role::Administrator], true);
    }

    private function owns(Nguoi $user, HoSoXuLy $application): bool
    {
        static $userCitizenMap = [];
        $userId = $user->getKey();
        if (! array_key_exists($userId, $userCitizenMap)) {
            $userCitizenMap[$userId] = $user->congDan()->pluck('IDCD')->all();
        }

        return in_array($application->IDCD, $userCitizenMap[$userId], true);
    }

    private function isStaff(Nguoi $user): bool
    {
        return Role::normalize($user->vaiTro)?->isStaff() ?? false;
    }
}

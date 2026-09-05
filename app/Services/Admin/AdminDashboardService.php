<?php

namespace App\Services\Admin;

use App\Data\Api\V1\Admin\DashboardData;
use App\Enums\Role;
use App\Models\HoSoXuLy;
use App\Models\LichHen;
use App\Models\Nguoi;
use App\Models\TrangThaiHoSo;
use App\Services\Workflow\StaffApplicationScope;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AdminDashboardService
{
    /**
     * Build a role-scoped API payload without inheriting Blade view keys.
     *
     * @param  array{limit?: int, appointment_date?: string}  $filters
     */
    public function forApi(Nguoi $actor, array $filters = []): DashboardData
    {
        $role = Role::normalize($actor->vaiTro);
        if (! $role || (! $role->isStaff() && $role !== Role::Checkin)) {
            throw new AuthorizationException('Bạn không có quyền xem tổng quan nghiệp vụ.');
        }

        $limit = max(1, min((int) ($filters['limit'] ?? 10), 20));
        $appointments = $this->appointmentsFor($role, $filters, $limit);

        if ($role === Role::Checkin) {
            return new DashboardData($role, [], new Collection, $appointments, ['applications_by_status' => []]);
        }

        $scope = $this->applicationStatusScope($role);
        $applicationsQuery = $this->applicationQuery($scope);
        $applications = (clone $applicationsQuery)
            ->orderByDesc('ngayTiepNhan')
            ->orderByDesc('maHSXL')
            ->limit($limit)
            ->get();
        $counters = $this->countersFor($applicationsQuery);

        return new DashboardData(
            $role,
            $counters,
            $applications,
            $appointments,
            ['applications_by_status' => $counters],
        );
    }

    /** @param list<int>|null $statusScope */
    private function applicationQuery(?array $statusScope): Builder
    {
        return HoSoXuLy::query()
            ->with(['tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->when($statusScope !== null, fn (Builder $query): Builder => $query->whereIn('maTrangThai', $statusScope));
    }

    /** @return list<array{key: string, label: string, value: int}> */
    private function countersFor(Builder $query): array
    {
        $counts = (clone $query)
            ->selectRaw('maTrangThai, COUNT(*) as total')
            ->groupBy('maTrangThai')
            ->pluck('total', 'maTrangThai');
        $labels = TrangThaiHoSo::query()
            ->whereIn('maTrangThai', $counts->keys()->all())
            ->pluck('tenTrangThai', 'maTrangThai');

        return $counts
            ->sortKeys()
            ->map(fn ($total, $status): array => [
                'key' => 'status_'.(int) $status,
                'label' => (string) ($labels[(int) $status] ?? 'Trạng thái hồ sơ'),
                'value' => (int) $total,
            ])
            ->values()
            ->all();
    }

    /** @param array{appointment_date?: string} $filters */
    private function appointmentsFor(Role $role, array $filters, int $limit): Collection
    {
        if (! in_array($role, [Role::Administrator, Role::OneStopOfficer, Role::Checkin], true)) {
            return new Collection;
        }

        return LichHen::query()
            ->with('tthc')
            ->where('trangThai', '!=', 'Đã hủy')
            ->when(
                ($date = $filters['appointment_date'] ?? null) !== null,
                fn (Builder $query): Builder => $query->whereDate('thoiGianHen', $date),
            )
            ->orderBy('thoiGianHen')
            ->limit($limit)
            ->get();
    }

    /** @return list<int>|null */
    private function applicationStatusScope(Role $role): ?array
    {
        return StaffApplicationScope::statusesFor($role);
    }
}

<?php

namespace App\Data\Api\V1\Admin;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * API-specific dashboard payload for the standalone frontend.
 *
 * @phpstan-type Counter array{key: string, label: string, value: int}
 * @phpstan-type Series array{applications_by_status: list<Counter>}
 */
final readonly class DashboardData
{
    /**
     * @param  list<Counter>  $counters
     * @param  Collection<int, \App\Models\HoSoXuLy>  $applications
     * @param  Collection<int, \App\Models\LichHen>  $appointments
     * @param  Series  $series
     */
    public function __construct(
        public Role $role,
        public array $counters,
        public Collection $applications,
        public Collection $appointments,
        public array $series,
    ) {}

    public function isCheckinOnly(): bool
    {
        return $this->role === Role::Checkin;
    }
}

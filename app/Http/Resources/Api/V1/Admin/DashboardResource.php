<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Data\Api\V1\Admin\DashboardData;
use App\Models\LichHen;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DashboardData */
class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var DashboardData $dashboard */
        $dashboard = $this->resource;
        $appointments = $dashboard->appointments
            ->map(fn (LichHen $appointment): array => $this->appointment($appointment))
            ->values()
            ->all();

        if ($dashboard->isCheckinOnly()) {
            return ['appointments' => $appointments];
        }

        return [
            'role' => $dashboard->role->value,
            'counters' => $dashboard->counters,
            'application_queue' => $dashboard->applications
                ->map(fn ($application): array => (new WorkflowQueueResource($application))->resolve($request))
                ->values()
                ->all(),
            'appointments' => $appointments,
            'series' => $dashboard->series,
        ];
    }

    /** @return array<string, int|string|null> */
    private function appointment(LichHen $appointment): array
    {
        return [
            'id' => (string) $appointment->getKey(),
            'code' => $appointment->maLichHen,
            'procedure_id' => (int) $appointment->maTTHC,
            'procedure_name' => $appointment->tthc?->tenTTHC,
            'scheduled_at' => optional($appointment->thoiGianHen)->toIso8601String(),
            'status' => $appointment->trangThai,
            'counter_id' => $appointment->maQuayLamViec === null ? null : (int) $appointment->maQuayLamViec,
            'queue_number' => $appointment->soThuTu === null ? null : (int) $appointment->soThuTu,
            'checked_in_at' => optional($appointment->checkin_time)->toIso8601String(),
        ];
    }
}

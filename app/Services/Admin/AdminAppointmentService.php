<?php

namespace App\Services\Admin;

use App\Models\LichHen;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminAppointmentService
{
    /**
     * @param  array{search?: string|null, status?: string|null, trangThai?: string|null, date?: string|null, from_date?: string|null, to_date?: string|null, procedure_id?: int|string|null, maTTHC?: int|string|null}  $filters
     */
    public function paginate(
        array $filters = [],
        int $perPage = 20,
        bool $futureOnly = false,
        bool $ascending = false,
    ): LengthAwarePaginator {
        $query = LichHen::query()
            ->with(['congdan.nguoi', 'tthc']);

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('maLichHen', 'like', '%'.$search.'%')
                    ->orWhere('id', 'like', '%'.$search.'%')
                    ->orWhereHas('congdan.nguoi', function ($userQuery) use ($search): void {
                        $userQuery->where('hoTen', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('soDienThoai', 'like', '%'.$search.'%');
                    });
            });
        }

        $status = $filters['status'] ?? $filters['trangThai'] ?? null;
        if (is_string($status) && trim($status) !== '') {
            $query->where('trangThai', trim($status));
        }

        $procedureId = $filters['procedure_id'] ?? $filters['maTTHC'] ?? null;
        if ($procedureId !== null && $procedureId !== '') {
            $query->where('maTTHC', (int) $procedureId);
        }

        if (($date = $filters['date'] ?? null) !== null && $date !== '') {
            $query->whereDate('thoiGianHen', $date);
        }
        if (($fromDate = $filters['from_date'] ?? null) !== null && $fromDate !== '') {
            $query->whereDate('thoiGianHen', '>=', $fromDate);
        }
        if (($toDate = $filters['to_date'] ?? null) !== null && $toDate !== '') {
            $query->whereDate('thoiGianHen', '<=', $toDate);
        }

        $hasDateFilter = collect(['date', 'from_date', 'to_date'])
            ->contains(fn (string $key): bool => ($filters[$key] ?? '') !== '');
        if ($futureOnly && ! $hasDateFilter) {
            $query->whereDate('thoiGianHen', '>', CarbonImmutable::now(config('app.timezone', 'Asia/Ho_Chi_Minh'))->toDateString());
        }

        return $query
            ->orderBy('thoiGianHen', $ascending ? 'asc' : 'desc')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();
    }
}

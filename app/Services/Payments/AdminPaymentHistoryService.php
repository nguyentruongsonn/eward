<?php

namespace App\Services\Payments;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AdminPaymentHistoryService
{
    /**
     * @param  array{search?: string|null, loaiGD?: string|null, trangThai?: string|null, from_date?: string|null, to_date?: string|null}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->historyQuery($filters)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array{search?: string|null, loaiGD?: string|null, trangThai?: string|null, from_date?: string|null, to_date?: string|null}  $filters
     */
    public function historyQuery(array $filters = []): Builder
    {
        $query = DB::table('lichsuthanhtoan')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->select(
                'lichsuthanhtoan.*',
                'nguoi.hoTen',
                'nguoi.email',
                'nguoi.soDienThoai',
                'hosoxuly.tenChuHoSo',
            );

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $inner) use ($search): void {
                $inner->where('lichsuthanhtoan.maGD', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.hoTen', 'like', '%'.$search.'%')
                    ->orWhere('nguoi.email', 'like', '%'.$search.'%')
                    ->orWhere('hosoxuly.tenChuHoSo', 'like', '%'.$search.'%');
            });
        }

        foreach (['loaiGD', 'trangThai'] as $field) {
            $value = trim((string) ($filters[$field] ?? ''));
            if ($value !== '') {
                $query->where('lichsuthanhtoan.'.$field, $value);
            }
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '>=', $filters['from_date']);
        }
        if (! empty($filters['to_date'])) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '<=', $filters['to_date']);
        }

        return $query->orderByDesc('lichsuthanhtoan.ngayGD')->orderByDesc('lichsuthanhtoan.id');
    }

    /** @return array{total: float, today: float, this_month: float, count: int} */
    public function summary(): array
    {
        $successful = DB::table('lichsuthanhtoan')->where('trangThai', 'Thành công');

        return [
            'total' => (float) (clone $successful)->sum('soTien'),
            'today' => (float) (clone $successful)->whereDate('ngayGD', today())->sum('soTien'),
            'this_month' => (float) (clone $successful)
                ->whereMonth('ngayGD', now()->month)
                ->whereYear('ngayGD', now()->year)
                ->sum('soTien'),
            'count' => (int) (clone $successful)->count(),
        ];
    }

    /** @return array<string, mixed> */
    public function revenueOverview(): array
    {
        $now = now();
        $successful = DB::table('lichsuthanhtoan')->where('trangThai', 'Thành công');
        $dailyData = [];
        for ($index = 29; $index >= 0; $index--) {
            $date = $now->copy()->subDays($index);
            $dailyData[] = [
                'date' => $date->format('d/m'),
                'revenue' => (float) (clone $successful)->whereDate('ngayGD', $date->toDateString())->sum('soTien'),
            ];
        }

        $monthlyData = [];
        for ($index = 11; $index >= 0; $index--) {
            $date = $now->copy()->subMonths($index);
            $monthlyData[] = [
                'month' => $date->format('m/Y'),
                'revenue' => (float) (clone $successful)
                    ->whereMonth('ngayGD', $date->month)
                    ->whereYear('ngayGD', $date->year)
                    ->sum('soTien'),
            ];
        }

        $byPaymentType = (clone $successful)
            ->select('loaiGD', DB::raw('SUM(soTien) as total'))
            ->groupBy('loaiGD')
            ->orderByDesc('total')
            ->get();
        $topHoSos = (clone $successful)
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->select(
                'lichsuthanhtoan.maHSXL',
                'hosoxuly.tenChuHoSo',
                'nguoi.hoTen',
                DB::raw('SUM(lichsuthanhtoan.soTien) as total'),
            )
            ->groupBy('lichsuthanhtoan.maHSXL', 'hosoxuly.tenChuHoSo', 'nguoi.hoTen')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'totalRevenue' => (float) (clone $successful)->sum('soTien'),
            'todayRevenue' => (float) (clone $successful)->whereDate('ngayGD', $now->toDateString())->sum('soTien'),
            'thisMonthRevenue' => (float) (clone $successful)
                ->whereMonth('ngayGD', $now->month)
                ->whereYear('ngayGD', $now->year)
                ->sum('soTien'),
            'thisYearRevenue' => (float) (clone $successful)->whereYear('ngayGD', $now->year)->sum('soTien'),
            'dailyData' => $dailyData,
            'monthlyData' => $monthlyData,
            'byPaymentType' => $byPaymentType,
            'topHoSos' => $topHoSos,
        ];
    }
}

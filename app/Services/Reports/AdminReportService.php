<?php

namespace App\Services\Reports;

use App\Exceptions\ApiException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AdminReportService
{
    public function revenue(?string $from = null, ?string $to = null): array
    {
        [$start, $end] = $this->range($from, $to);
        $base = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereBetween('ngayGD', [$start, $end]);
        $summary = (clone $base)
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('COALESCE(SUM(soTien), 0) as total_amount')
            ->first();
        $daily = (clone $base)
            ->selectRaw('DATE(ngayGD) as date')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('COALESCE(SUM(soTien), 0) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(static fn (object $row): array => [
                'date' => $row->date,
                'transaction_count' => (int) $row->transaction_count,
                'total_amount' => (float) $row->total_amount,
            ])->values()->all();

        return [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'transaction_count' => (int) ($summary?->transaction_count ?? 0),
            'total_amount' => (float) ($summary?->total_amount ?? 0),
            'by_day' => $daily,
        ];
    }

    public function applications(?string $from = null, ?string $to = null): array
    {
        [$start, $end] = $this->range($from, $to);
        $base = DB::table('hosoxuly')->whereBetween('ngayTiepNhan', [$start, $end]);
        $byStatus = (clone $base)
            ->leftJoin('trangthaihoso', 'trangthaihoso.maTrangThai', '=', 'hosoxuly.maTrangThai')
            ->select('hosoxuly.maTrangThai as status_id', 'trangthaihoso.tenTrangThai as status_name')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('hosoxuly.maTrangThai', 'trangthaihoso.tenTrangThai')
            ->orderBy('hosoxuly.maTrangThai')
            ->get()
            ->map(static fn (object $row): array => [
                'status_id' => (int) $row->status_id,
                'status_name' => $row->status_name,
                'total' => (int) $row->total,
            ])->values()->all();
        $byProcedure = (clone $base)
            ->join('tthc', 'tthc.maTTHC', '=', 'hosoxuly.maTTHC')
            ->select('hosoxuly.maTTHC as procedure_id', 'tthc.tenTTHC as procedure_name')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('hosoxuly.maTTHC', 'tthc.tenTTHC')
            ->orderByDesc('total')
            ->get()
            ->map(static fn (object $row): array => [
                'procedure_id' => (int) $row->procedure_id,
                'procedure_name' => $row->procedure_name,
                'total' => (int) $row->total,
            ])->values()->all();

        return [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'total' => (int) $base->count(),
            'by_status' => $byStatus,
            'by_procedure' => $byProcedure,
        ];
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    private function range(?string $from, ?string $to): array
    {
        $timezone = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $start = $from
            ? CarbonImmutable::createFromFormat('Y-m-d', $from, $timezone)->startOfDay()
            : CarbonImmutable::now($timezone)->subDays(29)->startOfDay();
        $end = $to
            ? CarbonImmutable::createFromFormat('Y-m-d', $to, $timezone)->endOfDay()
            : CarbonImmutable::now($timezone)->endOfDay();

        if (! $start || ! $end || $start->isAfter($end)) {
            throw new ApiException('Khoảng ngày báo cáo không hợp lệ.', 'REPORT_DATE_RANGE_INVALID', 422);
        }

        return [$start, $end];
    }
}

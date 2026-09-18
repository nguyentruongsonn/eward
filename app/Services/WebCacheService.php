<?php

namespace App\Services;

use App\Models\CongDan;
use App\Models\HoSoXuLy;
use App\Models\LichHen;
use App\Models\ThongBao;
use App\Models\TTHC;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WebCacheService
{
    public function getOutstandingProcedures()
    {
        return $this->cache()->remember('tthc:outstanding', 600, function () {
            return TTHC::published()->with('doiTuongs')->orderBy('tenTTHC', 'asc')->get();
        });
    }

    public function getProcedureNames(): array
    {
        return $this->cache()->remember('tthc:names', 600, static fn (): array => TTHC::published()
            ->orderBy('tenTTHC')->pluck('tenTTHC', 'maTTHC')->toArray());
    }

    public function getProcedureDetail(string $maTTHC): array
    {
        return $this->cache()->remember("tthc:detail:{$maTTHC}", 600, function () use ($maTTHC) {
            $tthc = DB::table('tthc as t')
                ->leftJoin('linhvuc as l', 'l.maLinhVuc', '=', 't.maLinhVuc')
                ->select('t.maTTHC', 't.tenTTHC', 't.trinhTuThucHien', 't.coQuanThucHien', 't.yeuCauDieuKien', 't.canCuPhapLy', 't.ketQuaThucHien', 'l.tenLinhVuc')
                ->where('t.maTTHC', $maTTHC)
                ->where(fn ($query) => $query->where('t.trangThai', 'Công khai')->orWhereNull('t.trangThai'))
                ->first();

            if (! $tthc) {
                return ['tthc' => null];
            }

            $cachThucHiens = DB::table('cachthuchien')->where('maTTHC', $maTTHC)->select('kenh', 'thoiHanGiaiQuyet', 'moTaPhiLePhi', 'moTa')->get();
            $thanhPhanHoSos = DB::table('thanhphanhoso as tph')
                ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
                ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
                ->where('tph.maTTHC', $maTTHC)
                ->select('tph.maThanhPhan', 'tph.tenThanhPhan', 'gt.tenGiayTo', 'tpg.soLuongBanChinh', 'tpg.soLuongBanSao')
                ->get()
                ->groupBy('tenThanhPhan');
            $doiTuongs = DB::table('thutucdoituong as td')
                ->leftjoin('doituongthuchien as d', 'd.maDoiTuong', '=', 'td.maDoiTuong')
                ->where('td.maTTHC', $maTTHC)
                ->select('d.tenDoiTuong')
                ->get();

            return [
                'tthc' => $tthc,
                'cachThucHiens' => $cachThucHiens,
                'thanhPhanHoSos' => $thanhPhanHoSos,
                'doiTuongs' => $doiTuongs,
            ];
        });
    }

    public function getRatingsSummary()
    {
        return $this->cache()->remember('ratings:summary', 600, function () {
            return DB::table('danhgia')
                ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
                ->join('tthc', 'hosoxuly.maTTHC', '=', 'tthc.maTTHC')
                ->where(fn ($query) => $query->where('tthc.trangThai', 'Công khai')->orWhereNull('tthc.trangThai'))
                ->select('tthc.maTTHC', 'tthc.tenTTHC', DB::raw('AVG(danhgia.soDiem) as avg_score'), DB::raw('COUNT(danhgia.id) as total_ratings'))
                ->groupBy('tthc.maTTHC', 'tthc.tenTTHC')
                ->orderByDesc('avg_score')
                ->orderByDesc('total_ratings')
                ->get();
        });
    }

    public function getProcedureRatingStats(string $maTTHC)
    {
        return $this->cache()->remember("ratings:procedure:stats:{$maTTHC}", 600, function () use ($maTTHC) {
            return DB::table('danhgia')
                ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
                ->join('tthc', 'hosoxuly.maTTHC', '=', 'tthc.maTTHC')
                ->where('hosoxuly.maTTHC', $maTTHC)
                ->where(fn ($query) => $query->where('tthc.trangThai', 'Công khai')->orWhereNull('tthc.trangThai'))
                ->select(
                    DB::raw('AVG(danhgia.soDiem) as avg_score'),
                    DB::raw('COUNT(danhgia.id) as total_ratings'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 5 THEN 1 END) as five_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 4 THEN 1 END) as four_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 3 THEN 1 END) as three_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 2 THEN 1 END) as two_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 1 THEN 1 END) as one_star')
                )
                ->first();
        });
    }

    public function getCitizenSummary(int $IDCD): array
    {
        return $this->cache()->remember("user:{$IDCD}:summary", 300, function () use ($IDCD) {
            $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
            $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
            $unread = ThongBao::where('IDCD', $IDCD)->where('is_read', false)->count();

            return [
                'hoSoHoanThanh' => $hoSoHoanThanh,
                'hoSoDangXuLy' => $hoSoDangXuLy,
                'unreadCount' => $unread,
            ];
        });
    }

    public function getAdminDashboardStats(): array
    {
        return $this->cache()->remember('admin:dashboard:stats', 60, function () {
            $stats = [
                'total_hoso' => HoSoXuLy::count(),
                'hoso_moi' => HoSoXuLy::whereDate('ngayTiepNhan', today())->count(),
                'total_congdan' => CongDan::count(),
                'total_lichhen' => LichHen::count(),
                'lichhen_hom_nay' => LichHen::whereDate('thoiGianHen', today())->count(),
                'total_tthc' => TTHC::count(),
            ];

            $hososByMonth = HoSoXuLy::selectRaw('DATE_FORMAT(ngayTiepNhan, "%Y-%m") as month, COUNT(*) as total')
                ->whereNotNull('ngayTiepNhan')
                ->where('ngayTiepNhan', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')->orderBy('month')->get()->pluck('total', 'month')->toArray();

            $monthlyLabels = [];
            $monthlyValues = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyLabels[] = $date->format('m/Y');
                $monthlyValues[] = (int) ($hososByMonth[$key] ?? 0);
            }

            $hososByStatus = DB::table('trangthaihoso')
                ->leftJoin('hosoxuly', 'hosoxuly.maTrangThai', '=', 'trangthaihoso.maTrangThai')
                ->select('trangthaihoso.tenTrangThai as name', DB::raw('COUNT(hosoxuly.maHSXL) as total'))
                ->groupBy('trangthaihoso.maTrangThai', 'trangthaihoso.tenTrangThai')
                ->orderBy('trangthaihoso.maTrangThai')
                ->get();

            $appointmentsByDay = LichHen::selectRaw('DATE(thoiGianHen) as date, COUNT(*) as total')
                ->where('thoiGianHen', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('date')->orderBy('date')->get()->pluck('total', 'date')->toArray();

            $appointmentLabels = [];
            $appointmentValues = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $appointmentLabels[] = \Carbon\Carbon::parse($date)->format('d/m');
                $appointmentValues[] = (int) ($appointmentsByDay[$date] ?? 0);
            }

            $revenueByDay = DB::table('lichsuthanhtoan')
                ->selectRaw('DATE(ngayGD) as date, SUM(soTien) as total')
                ->where('trangThai', 'Thành công')
                ->where('ngayGD', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('date')->orderBy('date')->get()->pluck('total', 'date')->toArray();

            $revenueLabels = [];
            $revenueValues = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $revenueLabels[] = \Carbon\Carbon::parse($date)->format('d/m');
                $revenueValues[] = (float) ($revenueByDay[$date] ?? 0);
            }

            return [
                'stats' => $stats,
                'monthlyLabels' => $monthlyLabels,
                'monthlyValues' => $monthlyValues,
                'hososByStatus' => $hososByStatus,
                'appointmentLabels' => $appointmentLabels,
                'appointmentValues' => $appointmentValues,
                'revenueLabels' => $revenueLabels,
                'revenueValues' => $revenueValues,
            ];
        });
    }

    private function cache(): Repository
    {
        return Cache::store((string) config('cache.default', 'database'));
    }
}

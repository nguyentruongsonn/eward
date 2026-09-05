<?php

namespace App\Services\HoSo;

use App\Models\DanhGia;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CitizenApplicationDetailService
{
    public function findForUser(Nguoi $user, string $applicationId): HoSoXuLy
    {
        return HoSoXuLy::query()
            ->whereKey($applicationId)
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->with([
                'trangThai',
                'tthc.formConfig',
                'congdan.nguoi',
                'files',
            ])
            ->firstOrFail();
    }

    /**
     * Build the legacy view model from one scoped, eager-loaded application.
     * Database queries remain here so web controllers only coordinate a request.
     *
     * @return array<string, mixed>
     */
    public function pageData(HoSoXuLy $application): array
    {
        [$formConfig, $payload] = $this->formData($application);
        $documents = $application->relationLoaded('files') ? $application->files : $application->files()->get();
        $procedure = $application->tthc;

        $documentGroups = collect();
        if ($procedure) {
            $documentGroups = DB::table('thanhphanhoso as tph')
                ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
                ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
                ->where('tph.maTTHC', $procedure->getKey())
                ->select(
                    'tph.maThanhPhan',
                    'tph.tenThanhPhan',
                    'gt.maGiayTo',
                    'gt.tenGiayTo',
                    'tpg.soLuongBanChinh',
                    'tpg.soLuongBanSao',
                )
                ->get()
                ->groupBy('tenThanhPhan');
        }

        $rating = DanhGia::query()->where('maHSXL', $application->getKey())->first();
        $daysRemaining = 0;
        $canRate = false;
        if ((int) $application->maTrangThai === 10 && $application->ngayTra) {
            $daysSince = Carbon::parse($application->ngayTra)->diffInDays(now());
            $daysRemaining = 10 - $daysSince;
            $canRate = $daysSince <= 10 && ! $rating;
        }

        return [
            'hoSo' => $application,
            'cauHinhForm' => $formConfig,
            'dulieu' => $payload,
            'taiLieu' => $documents,
            'thanhPhanHoSos' => $documentGroups,
            'yeuCauBoSung' => $this->supplementRequest($application),
            'canRate' => $canRate,
            'daysRemaining' => $daysRemaining,
            'existingRating' => $rating,
            'lichSuThanhToan' => DB::table('lichsuthanhtoan')
                ->where('maHSXL', $application->getKey())
                ->get(),
        ];
    }

    /** @return array{0: array<int|string, mixed>, 1: array<int|string, mixed>} */
    private function formData(HoSoXuLy $application): array
    {
        $raw = is_array($application->dulieu)
            ? $application->dulieu
            : (json_decode((string) $application->dulieu, true) ?: []);
        $configured = $application->tthc?->formConfig?->cauHinhForm ?? [];

        if (isset($raw['cauHinhForm'], $raw['payload'])) {
            return [$raw['cauHinhForm'], $raw['payload']];
        }

        if (isset($raw[0]) && is_array($raw[0]) && isset($raw[0]['group'])) {
            return [$raw, []];
        }

        return [$configured, $raw];
    }

    private function supplementRequest(HoSoXuLy $application): ?array
    {
        if (! $application->yeu_cau_bo_sung) {
            return null;
        }

        return json_decode($application->yeu_cau_bo_sung, true) ?: null;
    }
}

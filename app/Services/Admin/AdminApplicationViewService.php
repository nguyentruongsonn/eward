<?php

namespace App\Services\Admin;

use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Illuminate\Database\Eloquent\Collection;

class AdminApplicationViewService
{
    public function findForAction(string $application): HoSoXuLy
    {
        return HoSoXuLy::query()->whereKey($application)->firstOrFail();
    }

    public function findForAdmin(string $application, bool $withMailHistory = false): HoSoXuLy
    {
        $item = HoSoXuLy::query()
            ->with([
                'congdan.nguoi',
                'tthc.linhVuc',
                'tthc.formConfig',
                'tthc.thanhPhanHoSos.giayTos',
                'tthc.lephis',
                'trangThai',
                'receiver',
                'approver',
                'files',
                'resultFiles',
            ])
            ->whereKey($application)
            ->firstOrFail();

        if ($withMailHistory) {
            $item->setRelation(
                'mailHistory',
                $item->mailHistory()->orderByDesc('sent_at')->orderByDesc('id')->get(),
            );
        }

        return $item;
    }

    public function findForPrint(string $application): HoSoXuLy
    {
        return HoSoXuLy::query()
            ->with(['tthc', 'congdan.nguoi'])
            ->whereKey($application)
            ->firstOrFail();
    }

    /** @return Collection<int, Nguoi> */
    public function leaders(): Collection
    {
        return Nguoi::query()
            ->whereRaw('TRIM(vaiTro) = ?', ['Lãnh đạo'])
            ->select('IDnguoiDung', 'hoTen', 'vaiTro')
            ->orderBy('hoTen')
            ->get();
    }
}

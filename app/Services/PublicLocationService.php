<?php

namespace App\Services;

use App\Models\LinhVuc;
use App\Models\Tinh;
use App\Models\Xa;
use Illuminate\Database\Eloquent\Collection;

class PublicLocationService
{
    /** @return Collection<int, Tinh> */
    public function provinces(): Collection
    {
        return Tinh::query()->orderBy('tenTinh')->get(['maTinh', 'tenTinh']);
    }

    /** @return Collection<int, Xa> */
    public function wards(int $province, bool $requireProvince = true): Collection
    {
        if (! $requireProvince) {
            return Xa::query()
                ->where('maTinh', $province)
                ->orderBy('tenXa')
                ->get(['maXa', 'tenXa', 'maTinh']);
        }

        $tinh = Tinh::query()->findOrFail($province);

        return $tinh->xas()->orderBy('tenXa')->get(['maXa', 'tenXa', 'maTinh']);
    }

    /** @return Collection<int, LinhVuc> */
    public function fields(): Collection
    {
        return LinhVuc::query()->orderBy('tenLinhVuc')->get(['maLinhVuc', 'tenLinhVuc']);
    }
}

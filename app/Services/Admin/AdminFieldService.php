<?php

namespace App\Services\Admin;

use App\Exceptions\ApiException;
use App\Models\LinhVuc;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminFieldService
{
    public function find(int $id): ?LinhVuc
    {
        return LinhVuc::query()->find($id);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = LinhVuc::query();
        if (isset($filters['search']) && trim((string) $filters['search']) !== '') {
            $query->where('tenLinhVuc', 'like', '%'.trim((string) $filters['search']).'%');
        }

        return $query->orderByDesc('maLinhVuc')->paginate(max(1, min($perPage, 100)))->withQueryString();
    }

    public function create(string $name): LinhVuc
    {
        return LinhVuc::query()->create(['tenLinhVuc' => $name]);
    }

    public function update(int $id, string $name): LinhVuc
    {
        return DB::transaction(function () use ($id, $name): LinhVuc {
            $field = LinhVuc::query()->whereKey($id)->lockForUpdate()->firstOrFail();
            $field->tenLinhVuc = $name;
            $field->save();

            return $field;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $field = LinhVuc::query()->whereKey($id)->lockForUpdate()->firstOrFail();
            $usageCount = DB::table('tthc')->where('maLinhVuc', $field->getKey())->count();
            if ($usageCount > 0) {
                throw new ApiException('Không thể xóa lĩnh vực này vì đang có '.$usageCount.' thủ tục hành chính sử dụng.', 'FIELD_IN_USE', 409);
            }

            $field->delete();
        });
    }
}

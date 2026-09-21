<?php

namespace App\Services\Admin;

use App\Exceptions\ApiException;
use App\Models\LinhVuc;
use App\Models\TTHC;
use App\Services\Search\ProcedureSearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminProcedureService
{
    public function __construct(private readonly ProcedureSearchService $procedureSearch) {}

    public function fields()
    {
        return LinhVuc::query()->orderBy('tenLinhVuc')->get();
    }

    /** @return array{linhVucs: \Illuminate\Support\Collection, quayLamViecs: \Illuminate\Support\Collection, doiTuongs: \Illuminate\Support\Collection, giayTos: \Illuminate\Support\Collection} */
    public function lookupOptions(): array
    {
        return [
            'linhVucs' => $this->fields(),
            'quayLamViecs' => DB::table('quaylamviec')->orderBy('maQuayLamViec')->get(),
            'doiTuongs' => DB::table('doituongthuchien')->orderBy('tenDoiTuong')->get(),
            'giayTos' => DB::table('giayto')->orderBy('tenGiayTo')->get(),
        ];
    }

    public function findModel(int $id, bool $withDetails = false): ?TTHC
    {
        $query = TTHC::query();
        if ($withDetails) {
            $query->with(['linhVuc', 'cachThucHiens', 'thanhPhanHoSos.giayTos', 'doiTuongs', 'lephis', 'formConfig']);
        }

        return $query->find($id);
    }

    public function find(int $id): ?object
    {
        return DB::table('tthc')->where('maTTHC', $id)->first();
    }

    /** @param array{search?: string|null, maLinhVuc?: int|string|null, trangThai?: string|null} $filters */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = DB::table('tthc')
            ->leftJoin('linhvuc', 'tthc.maLinhVuc', '=', 'linhvuc.maLinhVuc')
            ->leftJoin('quaylamviec', 'tthc.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->select('tthc.*', 'linhvuc.tenLinhVuc', 'quaylamviec.tenQuayLamViec');

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('tthc.tenTTHC', 'like', '%'.$search.'%')
                    ->orWhere('linhvuc.tenLinhVuc', 'like', '%'.$search.'%');
            });
        }

        if (($filters['maLinhVuc'] ?? '') !== '') {
            $query->where('tthc.maLinhVuc', (int) $filters['maLinhVuc']);
        }
        $status = trim((string) ($filters['trangThai'] ?? ''));
        if ($status !== '') {
            $query->where('tthc.trangThai', $status);
        }

        return $query->orderByDesc('tthc.maTTHC')->paginate($perPage)->withQueryString();
    }

    public function create(array $attributes, array $audienceIds = [], array $methods = [], array $fees = [], ?string $formConfig = null, array $components = []): TTHC
    {
        $created = DB::transaction(function () use ($attributes, $audienceIds, $methods, $fees, $formConfig, $components): TTHC {
            $procedureId = DB::table('tthc')->insertGetId([
                'tenTTHC' => $attributes['tenTTHC'],
                'maLinhVuc' => $attributes['maLinhVuc'],
                'maQuayLamViec' => $attributes['maQuayLamViec'] ?? null,
                'trinhTuThucHien' => $attributes['trinhTuThucHien'],
                'doiTuongThucHien' => $attributes['doiTuongThucHien'],
                'coQuanThucHien' => $attributes['coQuanThucHien'],
                'trangThai' => $attributes['trangThai'] ?? 'Chờ công khai',
                'yeuCauDieuKien' => $attributes['yeuCauDieuKien'],
                'canCuPhapLy' => $attributes['canCuPhapLy'],
                'ketQuaThucHien' => $attributes['ketQuaThucHien'],
            ]);

            $this->replaceRelatedConfig($procedureId, [
                'audience_ids' => $audienceIds,
                'methods' => $methods,
                'fees' => $fees,
                'form_config' => $formConfig,
                'components' => $components,
            ]);

            $created = TTHC::query()->findOrFail($procedureId);
            Cache::forget('chat_assistant_knowledge_context');

            return $created;
        });

        $this->procedureSearch->sync($created);

        return $created;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    /** @param array<string, mixed> $related */
    public function update(int $id, array $attributes, array $related = []): TTHC
    {
        $updated = DB::transaction(function () use ($id, $attributes, $related): TTHC {
            $procedure = DB::table('tthc')->where('maTTHC', $id)->lockForUpdate()->firstOrFail();
            DB::table('tthc')->where('maTTHC', $procedure->maTTHC)->update([
                'tenTTHC' => $attributes['tenTTHC'],
                'maLinhVuc' => $attributes['maLinhVuc'],
                'maQuayLamViec' => $attributes['maQuayLamViec'] ?? null,
                'trinhTuThucHien' => $attributes['trinhTuThucHien'],
                'doiTuongThucHien' => $attributes['doiTuongThucHien'],
                'coQuanThucHien' => $attributes['coQuanThucHien'],
                'trangThai' => $attributes['trangThai'] ?? 'Chờ công khai',
                'yeuCauDieuKien' => $attributes['yeuCauDieuKien'],
                'canCuPhapLy' => $attributes['canCuPhapLy'],
                'ketQuaThucHien' => $attributes['ketQuaThucHien'],
            ]);
            if ($related !== []) {
                $this->replaceRelatedConfig($id, $related);
            }

            $updated = TTHC::query()->findOrFail($id);
            Cache::forget('chat_assistant_knowledge_context');

            return $updated;
        });

        $this->procedureSearch->sync($updated);

        return $updated;
    }

    /** @param array<string, mixed> $related */
    private function replaceRelatedConfig(int $procedureId, array $related): void
    {
        if (array_key_exists('audience_ids', $related)) {
            DB::table('thutucdoituong')->where('maTTHC', $procedureId)->delete();
            $audienceIds = array_values(array_unique(array_map('intval', (array) $related['audience_ids'])));
            if ($audienceIds !== []) {
                DB::table('thutucdoituong')->insert(array_map(
                    static fn (int $audienceId): array => ['maTTHC' => $procedureId, 'maDoiTuong' => $audienceId],
                    $audienceIds,
                ));
            }
        }

        if (array_key_exists('methods', $related)) {
            DB::table('cachthuchien')->where('maTTHC', $procedureId)->delete();
            foreach ((array) $related['methods'] as $method) {
                DB::table('cachthuchien')->insert([
                    'maTTHC' => $procedureId,
                    'kenh' => $method['kenh'],
                    'thoiHanGiaiQuyet' => $method['thoiHanGiaiQuyet'] ?? null,
                    'moTaPhiLePhi' => $method['moTaPhiLePhi'] ?? null,
                    'thoiHan' => $method['thoiHan'] ?? 0,
                    'moTa' => $method['moTa'] ?? null,
                ]);
            }
        }

        if (array_key_exists('fees', $related)) {
            DB::table('lephi')->where('maTTHC', $procedureId)->delete();
            $fees = array_values((array) $related['fees']);
            if ($fees !== []) {
                DB::table('lephi')->insert(array_map(
                    static fn (array $fee): array => [
                        'loaiLePhi' => $fee['loaiLePhi'],
                        'maTTHC' => $procedureId,
                        'soTien' => $fee['soTien'] ?? 0,
                        'batBuoc' => $fee['batBuoc'] ?? null,
                        'moTa' => $fee['moTa'] ?? null,
                    ],
                    $fees,
                ));
            }
        }

        if (array_key_exists('form_config', $related)) {
            DB::table('formtructuyen')->where('maTTHC', $procedureId)->delete();
            $formConfig = $related['form_config'];
            if (is_string($formConfig) && trim($formConfig) !== '') {
                DB::table('formtructuyen')->insert(['maTTHC' => $procedureId, 'cauHinhForm' => $formConfig]);
            }
        }

        if (array_key_exists('components', $related)) {
            $componentIds = DB::table('thanhphanhoso')->where('maTTHC', $procedureId)->pluck('maThanhPhan')->all();
            if ($componentIds !== []) {
                DB::table('thanhphangiayto')->whereIn('maThanhPhan', $componentIds)->delete();
            }
            DB::table('thanhphanhoso')->where('maTTHC', $procedureId)->delete();
            foreach ((array) $related['components'] as $component) {
                $componentId = DB::table('thanhphanhoso')->insertGetId([
                    'maTTHC' => $procedureId,
                    'tenThanhPhan' => $component['tenThanhPhan'],
                ]);
                $documents = $component['giayTo'] ?? [];
                if ($documents !== []) {
                    DB::table('thanhphangiayto')->insert(array_map(
                        static fn (array $document): array => [
                            'maThanhPhan' => $componentId,
                            'maGiayTo' => $document['maGiayTo'],
                            'soLuongBanChinh' => $document['soLuongBanChinh'] ?? 0,
                            'soLuongBanSao' => $document['soLuongBanSao'] ?? 0,
                        ],
                        $documents,
                    ));
                }
            }
        }
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            DB::table('tthc')->where('maTTHC', $id)->lockForUpdate()->firstOrFail();
            $usageCount = DB::table('hosoxuly')->where('maTTHC', $id)->count();
            if ($usageCount > 0) {
                throw new ApiException('Không thể xóa thủ tục này vì đang có '.$usageCount.' hồ sơ sử dụng.', 'PROCEDURE_IN_USE', 409);
            }

            $componentIds = DB::table('thanhphanhoso')
                ->where('maTTHC', $id)
                ->pluck('maThanhPhan')
                ->all();
            if ($componentIds !== []) {
                DB::table('thanhphangiayto')->whereIn('maThanhPhan', $componentIds)->delete();
            }
            DB::table('thanhphanhoso')->where('maTTHC', $id)->delete();
            DB::table('thutucdoituong')->where('maTTHC', $id)->delete();
            DB::table('cachthuchien')->where('maTTHC', $id)->delete();
            DB::table('lephi')->where('maTTHC', $id)->delete();
            DB::table('formtructuyen')->where('maTTHC', $id)->delete();
            DB::table('tthc')->where('maTTHC', $id)->delete();
            Cache::forget('chat_assistant_knowledge_context');
        });

        $this->procedureSearch->remove($id);
    }
}

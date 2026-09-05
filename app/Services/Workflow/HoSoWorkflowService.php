<?php

namespace App\Services\Workflow;

use App\Enums\HoSoStatus;
use App\Enums\Role;
use App\Exceptions\ApiException;
use App\Models\CachThucHien;
use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HoSoWorkflowService
{
    /** @return list<string> */
    public static function availableActions(Nguoi $actor, HoSoXuLy $application): array
    {
        return self::availableActionsFor(
            Role::normalize($actor->vaiTro),
            HoSoStatus::tryFrom((int) $application->maTrangThai),
        );
    }

    /** @return list<string> */
    public static function availableActionsFor(?Role $role, ?HoSoStatus $status): array
    {
        if (! $role || ! $status) {
            return [];
        }

        $actionsForState = match ($status) {
            HoSoStatus::PendingReception => ['accept', 'reject'],
            HoSoStatus::Accepted => ['confirmReception', 'forward'],
            HoSoStatus::Processing => ['requestSupplement', 'approve'],
            HoSoStatus::Supplemented => ['requestSupplement'],
            HoSoStatus::Completed => ['deliver', 'rework'],
            HoSoStatus::DirectReception => ['completeDirectReception', 'uploadComponentFile'],
            HoSoStatus::ReworkRequested => ['forward', 'requestSupplement'],
            default => [],
        };

        if ($role === Role::Administrator) {
            return $actionsForState;
        }

        return match ($role) {
            Role::OneStopOfficer => array_values(array_intersect($actionsForState, [
                'accept',
                'reject',
                'confirmReception',
                'deliver',
                'completeDirectReception',
                'uploadComponentFile',
            ])),
            Role::CaseOfficer => array_values(array_intersect($actionsForState, [
                'forward',
                'requestSupplement',
            ])),
            Role::Leader => array_values(array_intersect($actionsForState, [
                'approve',
                'rework',
            ])),
            default => [],
        };
    }

    public function accept(HoSoXuLy $application, Nguoi $actor, ?Carbon $acceptedAt = null): HoSoXuLy
    {
        $acceptedAt ??= now();

        return DB::transaction(function () use ($application, $actor, $acceptedAt): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $current = HoSoStatus::tryFrom((int) $locked->maTrangThai);
            if ($current !== HoSoStatus::PendingReception) {
                throw new ApiException('Hồ sơ không ở trạng thái chờ tiếp nhận.', 'WORKFLOW_ACCEPT_INVALID', 409, [
                    'from' => $current?->value,
                ]);
            }

            $this->markAccepted($locked, $actor, $acceptedAt);
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function completeDirectReception(HoSoXuLy $application, Nguoi $actor, ?Carbon $acceptedAt = null): HoSoXuLy
    {
        $acceptedAt ??= now();

        return DB::transaction(function () use ($application, $actor, $acceptedAt): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if ((int) $locked->maTrangThai !== HoSoStatus::DirectReception->value) {
                throw new ApiException('Hồ sơ không ở trạng thái nhận trực tiếp.', 'WORKFLOW_DIRECT_RECEPTION_INVALID', 409, [
                    'from' => (int) $locked->maTrangThai,
                ]);
            }

            $requiredDocumentIds = DB::table('thanhphanhoso')
                ->join('thanhphangiayto', 'thanhphangiayto.maThanhPhan', '=', 'thanhphanhoso.maThanhPhan')
                ->join('giayto', 'giayto.maGiayTo', '=', 'thanhphangiayto.maGiayTo')
                ->where('thanhphanhoso.maTTHC', $locked->maTTHC)
                ->whereRaw("TRIM(COALESCE(giayto.yeuCau, '')) = ?", ['Bắt buộc'])
                ->pluck('thanhphangiayto.maGiayTo')
                ->map(static fn ($id): int => (int) $id)
                ->unique()
                ->values();
            if ($requiredDocumentIds->isNotEmpty()) {
                $uploadedDocumentIds = $locked->files()
                    ->whereIn('maGiayTo', $requiredDocumentIds->all())
                    ->pluck('maGiayTo')
                    ->map(static fn ($id): int => (int) $id)
                    ->unique();
                $missingDocumentIds = $requiredDocumentIds->diff($uploadedDocumentIds)->values()->all();
                if ($missingDocumentIds !== []) {
                    throw new ApiException('Chưa đủ giấy tờ bắt buộc để hoàn tất tiếp nhận trực tiếp.', 'DIRECT_RECEPTION_DOCUMENTS_INCOMPLETE', 422, [
                        'missing_document_ids' => $missingDocumentIds,
                    ]);
                }
            }

            $this->markAccepted($locked, $actor, $acceptedAt);
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function deliver(HoSoXuLy $application, Nguoi $actor): HoSoXuLy
    {
        return $this->transition($application, HoSoStatus::Delivered->value, $actor);
    }

    public function approve(HoSoXuLy $application, Nguoi $actor, ?string $note = null, ?string $approvalComment = null): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $actor, $note, $approvalComment): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if ((int) $locked->maTrangThai !== HoSoStatus::Processing->value) {
                throw new ApiException('Hồ sơ chưa ở trạng thái chờ phê duyệt.', 'WORKFLOW_APPROVAL_INVALID', 409, [
                    'from' => (int) $locked->maTrangThai,
                ]);
            }

            $locked->maTrangThai = HoSoStatus::Completed->value;
            $locked->nguoiDuyet = $actor->getKey();
            $locked->ngayDuyet = now();
            $locked->ngayKetThucXuLy = now()->toDateString();
            $locked->yKienDuyet = $approvalComment;
            if ($note !== null && trim($note) !== '') {
                $locked->ghiChu = ($locked->ghiChu ?? '')."\n[".now()->format('d/m/Y H:i').'] '.$note;
            }
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function requestRework(HoSoXuLy $application, Nguoi $actor, string $note): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $actor, $note): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if (! in_array((int) $locked->maTrangThai, [HoSoStatus::Processing->value, HoSoStatus::Completed->value], true)) {
                throw new ApiException('Chỉ có thể yêu cầu xử lý lại hồ sơ đang chờ phê duyệt hoặc đã phê duyệt.', 'WORKFLOW_REWORK_INVALID', 409, [
                    'from' => (int) $locked->maTrangThai,
                ]);
            }

            $locked->maTrangThai = HoSoStatus::ReworkRequested->value;
            $locked->nguoiDuyet = null;
            $locked->ngayDuyet = null;
            $locked->yKienDuyet = null;
            $locked->ghiChu = ($locked->ghiChu ?? '')."\n[".now()->format('d/m/Y H:i').'] '.$actor->vaiTro.' yêu cầu xử lý lại: '.$note;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function forwardForApproval(HoSoXuLy $application, Nguoi $actor, ?string $note = null): HoSoXuLy
    {
        return DB::transaction(function () use ($application, $note): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $current = HoSoStatus::tryFrom((int) $locked->maTrangThai);
            if (! in_array($current, [HoSoStatus::Accepted, HoSoStatus::ReworkRequested], true)) {
                throw new ApiException('Hồ sơ chưa sẵn sàng để chuyển lãnh đạo phê duyệt.', 'WORKFLOW_FORWARD_INVALID', 409, [
                    'from' => $current?->value,
                ]);
            }

            $locked->maTrangThai = HoSoStatus::Processing->value;
            if ($note !== null && trim($note) !== '') {
                $locked->ghiChu = ($locked->ghiChu ?? '')."\n[".now()->format('d/m/Y H:i').'] '.$note;
            }
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function transition(HoSoXuLy $application, int $targetStatus, Nguoi $actor, ?string $note = null): HoSoXuLy
    {
        $target = HoSoStatus::tryFrom($targetStatus);
        if (! $target) {
            throw new ApiException('Trạng thái hồ sơ không tồn tại.', 'WORKFLOW_STATUS_INVALID', 422);
        }

        return DB::transaction(function () use ($application, $target, $actor, $note): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            $current = HoSoStatus::tryFrom((int) $locked->maTrangThai);
            $role = Role::normalize($actor->vaiTro);
            if ($role?->isStaff() && (! $current || ! $role->canUseGenericTransition($current, $target))) {
                throw new ApiException('Bạn không có quyền quản trị.', 'FORBIDDEN', 403);
            }

            if ($target === HoSoStatus::DirectReception) {
                throw new ApiException('Hồ sơ nhận trực tiếp phải được tạo từ quy trình lịch hẹn.', 'WORKFLOW_DIRECT_RECEPTION_DEDICATED', 409);
            }

            if (! $current || ! in_array($target->value, $current->allowedTransitions(), true)) {
                throw new ApiException('Không thể chuyển hồ sơ sang trạng thái này.', 'WORKFLOW_TRANSITION_INVALID', 409, [
                    'from' => $current?->value,
                    'to' => $target->value,
                ]);
            }

            $locked->maTrangThai = $target->value;
            $locked->ghiChu = $note ?: $locked->ghiChu;

            if ($target === HoSoStatus::WithdrawalRequested && $locked->maTrangThai_backup === null) {
                $locked->maTrangThai_backup = $current->value;
            }

            if ($target === HoSoStatus::Accepted || $target === HoSoStatus::DirectReception) {
                $locked->nguoiTiepNhan = $actor->getKey();
            }

            if ($target === HoSoStatus::Delivered) {
                $locked->ngayTra = now()->toDateString();
            }

            if ($target === HoSoStatus::Completed) {
                $locked->ngayKetThucXuLy = now()->toDateString();
            }

            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    public function reject(HoSoXuLy $application, Nguoi $actor, string $reason): HoSoXuLy
    {
        $reason = trim($reason);
        if ($reason === '') {
            throw new ApiException('Lý do từ chối là bắt buộc.', 'REJECTION_REASON_REQUIRED', 422);
        }

        return DB::transaction(function () use ($application, $actor, $reason): HoSoXuLy {
            $locked = HoSoXuLy::query()->whereKey($application->getKey())->lockForUpdate()->firstOrFail();
            if ((int) $locked->maTrangThai !== HoSoStatus::PendingReception->value) {
                throw new ApiException('Chỉ có thể từ chối hồ sơ đang chờ tiếp nhận.', 'WORKFLOW_REJECT_INVALID', 409, [
                    'from' => (int) $locked->maTrangThai,
                ]);
            }

            $locked->maTrangThai = HoSoStatus::Rejected->value;
            $locked->nguoiTiepNhan = $actor->getKey();
            $locked->ngayTiepNhan ??= now()->toDateString();
            $line = '['.now()->format('d/m/Y H:i').'] '.$actor->hoTen.' từ chối hồ sơ: '.$reason;
            $locked->ghiChu = trim((string) $locked->ghiChu);
            $locked->ghiChu = $locked->ghiChu === '' ? $line : $locked->ghiChu."\n".$line;
            $locked->save();

            return $locked->fresh(['trangThai', 'tthc']);
        });
    }

    private function markAccepted(HoSoXuLy $application, Nguoi $actor, Carbon $acceptedAt): void
    {
        $application->maTrangThai = HoSoStatus::Accepted->value;
        $application->nguoiTiepNhan = $actor->getKey();
        $application->ngayTiepNhan = $acceptedAt->toDateString();

        $processingDays = CachThucHien::query()
            ->where('maTTHC', $application->maTTHC)
            ->value('thoiHan');
        if (is_numeric($processingDays)) {
            $application->ngayHenTra = $acceptedAt->copy()->addDays((int) $processingDays)->toDateString();
        }
    }
}

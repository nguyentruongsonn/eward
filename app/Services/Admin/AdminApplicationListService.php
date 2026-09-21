<?php

namespace App\Services\Admin;

use App\Enums\HoSoStatus;
use App\Models\HoSoXuLy;
use App\Models\TrangThaiHoSo;
use App\Services\Search\ApplicationSearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AdminApplicationListService
{
    public function __construct(private readonly ApplicationSearchService $applicationSearch) {}

    public const DEFAULT_SCOPE = 'default';

    public const ALL_SCOPE = 'all';

    public const RECEIVED_SCOPE = 'received';

    public const PROCESSING_SCOPE = 'processing';

    public const DIRECT_SCOPE = 'direct';

    public const SUPPLEMENT_SCOPE = 'supplement';

    public const COMPLETED_SCOPE = 'completed';

    public const DELIVERED_SCOPE = 'delivered';

    /** @return Collection<int, TrangThaiHoSo> */
    public function statuses(): Collection
    {
        return TrangThaiHoSo::query()
            ->orderBy('maTrangThai')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(
        array $filters,
        string $scope = self::DEFAULT_SCOPE,
        int $perPage = 20,
        ?string $sortColumn = null,
        string $direction = 'desc',
        ?array $visibleStatuses = null,
    ): LengthAwarePaginator {
        $query = HoSoXuLy::query()
            ->with(['congdan.nguoi', 'tthc', 'trangThai', 'paymentHistories', 'paymentIntents'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '');

        $searchIds = $this->searchIds($filters);
        if ($searchIds === null) {
            $this->applySearch($query, $filters);
        } elseif ($searchIds === []) {
            $query->whereRaw('1 = 0');
        } else {
            $query->whereIn('maHSXL', $searchIds);
        }
        $hasDateFilter = $this->applyDates($query, $filters);
        $this->applyOverdue($query, $filters);
        $this->applyScope($query, $filters, $scope, $hasDateFilter);
        if ($visibleStatuses !== null) {
            $query->whereIn('maTrangThai', $visibleStatuses);
        }
        $this->applyPaymentStatus($query, $filters);

        $sortColumn ??= match ($scope) {
            self::COMPLETED_SCOPE => 'ngayKetThucXuLy',
            self::DELIVERED_SCOPE => 'ngayTra',
            default => 'ngayTiepNhan',
        };
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        return $query
            ->orderBy($sortColumn, $direction)
            ->orderByDesc('maHSXL')
            ->paginate(max(1, min($perPage, 100)))
            ->withQueryString();
    }

    private function applySearch($query, array $filters): void
    {
        if (! $this->filled($filters, 'search')) {
            return;
        }

        $search = (string) $filters['search'];
        $query->where(function ($inner) use ($search): void {
            $inner->where('maHSXL', 'like', '%'.$search.'%')
                ->orWhere('tenChuHoSo', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')
                ->orWhere('soDienThoai', 'like', '%'.$search.'%');
        });
    }

    private function searchIds(array $filters): ?array
    {
        if (! $this->filled($filters, 'search')) {
            return null;
        }

        $result = $this->applicationSearch->searchApplicationIds((string) $filters['search']);

        return $result === null ? null : $result['ids'];
    }

    private function applyDates($query, array $filters): bool
    {
        $hasDateFilter = $this->filled($filters, 'ngayTiepNhan_from') || $this->filled($filters, 'ngayTiepNhan_to');
        if ($this->filled($filters, 'ngayTiepNhan_from')) {
            $query->whereDate('ngayTiepNhan', '>=', $filters['ngayTiepNhan_from']);
        }
        if ($this->filled($filters, 'ngayTiepNhan_to')) {
            $query->whereDate('ngayTiepNhan', '<=', $filters['ngayTiepNhan_to']);
        }

        return $hasDateFilter;
    }

    private function applyOverdue($query, array $filters): void
    {
        if (! $this->filled($filters, 'overdue')) {
            return;
        }

        $isOverdue = filter_var($filters['overdue'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($isOverdue === null) {
            $isOverdue = (string) $filters['overdue'] === '1';
        }

        if ($isOverdue) {
            $query->whereNotNull('ngayHenTra')
                ->where('ngayHenTra', '<', now())
                ->whereNotIn('maTrangThai', [
                    HoSoStatus::Completed->value,
                    HoSoStatus::Delivered->value,
                    HoSoStatus::Rejected->value,
                ]);
        }
    }

    private function applyScope($query, array $filters, string $scope, bool $hasDateFilter): void
    {
        if ($scope === self::ALL_SCOPE) {
            if ($this->filled($filters, 'maTrangThai')) {
                $query->where('maTrangThai', $filters['maTrangThai']);
            }

            return;
        }

        if ($scope === self::DEFAULT_SCOPE) {
            if ($this->filled($filters, 'maTrangThai')) {
                $query->where('maTrangThai', $filters['maTrangThai']);
            } elseif (! $hasDateFilter) {
                $query->where(static function ($inner): void {
                    $inner->where(static function ($pending): void {
                        $pending->where('maTrangThai', HoSoStatus::PendingReception->value)
                            ->where(static function ($payable): void {
                                $payable->where('lePhi', '<=', 0)
                                    ->orWhereExists(function ($paidHistory): void {
                                        $paidHistory->selectRaw('1')
                                            ->from('lichsuthanhtoan')
                                            ->whereColumn('lichsuthanhtoan.maHSXL', 'hosoxuly.maHSXL')
                                            ->where('lichsuthanhtoan.trangThai', 'Thành công');
                                    })
                                    ->orWhereExists(function ($paidIntent): void {
                                        $paidIntent->selectRaw('1')
                                            ->from('payment_intents')
                                            ->whereColumn('payment_intents.maHSXL', 'hosoxuly.maHSXL')
                                            ->where('payment_intents.status', 'paid');
                                    });
                            });
                    })
                        ->orWhere(static function ($withdrawn): void {
                            $withdrawn->where('maTrangThai', HoSoStatus::WithdrawalRequested->value)->whereNull('ngayTiepNhan');
                        });
                });
            }

            return;
        }

        match ($scope) {
            self::RECEIVED_SCOPE => $query->where(static function ($inner): void {
                $inner->where('maTrangThai', HoSoStatus::Accepted->value)
                    ->orWhere(static function ($withdrawn): void {
                        $withdrawn->where('maTrangThai', HoSoStatus::WithdrawalRequested->value)->whereNotNull('ngayTiepNhan');
                    });
            }),
            self::PROCESSING_SCOPE => $query->where(static function ($inner): void {
                $inner->where('maTrangThai', HoSoStatus::Processing->value)
                    ->orWhere(static function ($withdrawn): void {
                        $withdrawn->where('maTrangThai', HoSoStatus::WithdrawalRequested->value)->where('maTrangThai_backup', HoSoStatus::Processing->value);
                    });
            }),
            self::DIRECT_SCOPE => $query->where('maTrangThai', HoSoStatus::DirectReception->value),
            self::SUPPLEMENT_SCOPE => $query->where('maTrangThai', HoSoStatus::SupplementRequested->value),
            self::COMPLETED_SCOPE => $query->where('maTrangThai', HoSoStatus::Completed->value),
            self::DELIVERED_SCOPE => $query->where('maTrangThai', HoSoStatus::Delivered->value),
            default => null,
        };
    }

    private function applyPaymentStatus($query, array $filters): void
    {
        $status = trim((string) ($filters['payment_status'] ?? ''));
        if ($status === '' || $status === 'all') {
            return;
        }

        $paid = static function ($paidQuery): void {
            $paidQuery->where('lePhi', '<=', 0)
                ->orWhereExists(function ($paidHistory): void {
                    $paidHistory->selectRaw('1')
                        ->from('lichsuthanhtoan')
                        ->whereColumn('lichsuthanhtoan.maHSXL', 'hosoxuly.maHSXL')
                        ->where('lichsuthanhtoan.trangThai', 'Thành công');
                })
                ->orWhereExists(function ($paidIntent): void {
                    $paidIntent->selectRaw('1')
                        ->from('payment_intents')
                        ->whereColumn('payment_intents.maHSXL', 'hosoxuly.maHSXL')
                        ->where('payment_intents.status', 'paid');
                });
        };

        if ($status === 'paid') {
            $query->where($paid);

            return;
        }

        $query->where('lePhi', '>', 0)
            ->whereNotExists(function ($paidHistory): void {
                $paidHistory->selectRaw('1')
                    ->from('lichsuthanhtoan')
                    ->whereColumn('lichsuthanhtoan.maHSXL', 'hosoxuly.maHSXL')
                    ->where('lichsuthanhtoan.trangThai', 'Thành công');
            })
            ->whereNotExists(function ($paidIntent): void {
                $paidIntent->selectRaw('1')
                    ->from('payment_intents')
                    ->whereColumn('payment_intents.maHSXL', 'hosoxuly.maHSXL')
                    ->where('payment_intents.status', 'paid');
            });
    }

    private function filled(array $filters, string $key): bool
    {
        return array_key_exists($key, $filters) && $filters[$key] !== null && trim((string) $filters[$key]) !== '';
    }
}

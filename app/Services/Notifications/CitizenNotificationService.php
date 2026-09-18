<?php

namespace App\Services\Notifications;

use App\Models\Nguoi;
use App\Models\ThongBao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CitizenNotificationService
{
    public function paginateForUser(
        Nguoi $user,
        bool $onlyUnread = false,
        int $perPage = 15,
        ?int $page = null,
    ): LengthAwarePaginator {
        $query = ThongBao::query()
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($onlyUnread) {
            $query->where('is_read', false);
        }

        return $query->paginate(
            max(1, min($perPage, 100)),
            ['*'],
            'page',
            $page === null ? null : max(1, $page),
        )->withQueryString();
    }

    public function findForUser(Nguoi $user, int $notificationId): ThongBao
    {
        return ThongBao::query()
            ->whereKey($notificationId)
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->firstOrFail();
    }

    public function markAsRead(ThongBao $notification): ThongBao
    {
        if (! $notification->is_read) {
            $notification->forceFill(['is_read' => true])->save();
        }

        return $notification->refresh();
    }

    public function unreadCount(Nguoi $user): int
    {
        return ThongBao::query()
            ->whereIn('IDCD', $user->congDan()->select('IDCD'))
            ->where('is_read', false)
            ->count();
    }
}

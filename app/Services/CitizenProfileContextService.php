<?php

namespace App\Services;

use App\Models\CongDan;
use App\Models\Nguoi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CitizenProfileContextService
{
    /**
     * Resolve the authenticated web account and its citizen record once per request.
     *
     * @return array{user: User|Nguoi|null, nguoi: Nguoi, congDan: CongDan}
     */
    public function current(): array
    {
        $authUser = Auth::user();

        if ($authUser instanceof Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } elseif ($authUser instanceof User) {
            $user = $authUser;
            $nguoi = $authUser->nguoi;
        } else {
            abort(401, 'Yêu cầu đăng nhập.');
        }

        abort_unless($nguoi instanceof Nguoi, 404, 'Không tìm thấy thông tin người dùng.');

        $congDan = $nguoi->congDan;
        if (! $congDan) {
            $congDan = CongDan::create(['IDnguoiDung' => $nguoi->getKey()]);
        }

        return [
            'user' => $user,
            'nguoi' => $nguoi,
            'congDan' => $congDan,
        ];
    }
}

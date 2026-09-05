<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AdminApplicationCacheService
{
    public function forgetForCitizen(int|string $citizenId): void
    {
        try {
            $redisCache = Cache::store('redis_db0');
            $redisCache->forget("user:{$citizenId}:summary");

            $keys = Redis::keys("profile:{$citizenId}:services:*");
            if ($keys !== []) {
                Redis::del($keys);
            }
        } catch (\Throwable $exception) {
            Log::warning('Lỗi khi xóa cache hồ sơ', ['error' => $exception->getMessage()]);
        }
    }
}

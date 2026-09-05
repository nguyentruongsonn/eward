<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserRedisService
{
    private const PROFILE_PREFIX = 'users:profile:';

    private const DEFAULT_TTL = 3600;

    /**
     * Lưu thông tin user đã đăng nhập vào Redis
     */
    public function storeAuthenticatedUser(Authenticatable $user): void
    {
        $userId = $this->extractUserId($user);
        if (! $userId) {
            return;
        }

        $payload = $this->buildPayload($user);

        $this->execute(function () use ($userId, $payload) {
            Redis::setex($this->profileKey($userId), self::DEFAULT_TTL, json_encode($payload, JSON_UNESCAPED_UNICODE));
        });
    }

    /**
     * Xóa thông tin user khỏi Redis khi logout
     */
    public function forget(?Authenticatable $user = null): void
    {
        $user = $user ?: auth()->user();
        $userId = $this->extractUserId($user);
        if (! $userId) {
            return;
        }

        $this->execute(function () use ($userId) {
            Redis::del($this->profileKey($userId));
        });
    }

    /**
     * Lấy thông tin user từ cache, nếu không có thì lấy từ callback và lưu lại
     */
    public function rememberProfile(int|string $userId, callable $resolver, int $ttl = self::DEFAULT_TTL): array
    {
        $key = $this->profileKey($userId);

        $cached = $this->execute(function () use ($key) {
            return Redis::get($key);
        });

        if ($cached) {
            return json_decode($cached, true) ?: [];
        }

        $data = $resolver();
        if (! is_array($data)) {
            return [];
        }

        $this->execute(function () use ($key, $ttl, $data) {
            Redis::setex($key, $ttl, json_encode($data, JSON_UNESCAPED_UNICODE));
        });

        return $data;
    }

    private function extractUserId(?Authenticatable $user): ?int
    {
        if (! $user) {
            return null;
        }

        return $user->IDnguoiDung ?? $user->id ?? null;
    }

    private function buildPayload(Authenticatable $user): array
    {
        return [
            'id' => $this->extractUserId($user),
            'email' => $user->email ?? null,
            'name' => method_exists($user, 'getAttribute') ? ($user->getAttribute('hoTen') ?? $user->name ?? optional($user->nguoi)->hoTen) : null,
            'role' => $user->vaiTro ?? optional($user->nguoi)->vaiTro,
            'login_at' => now()->toDateTimeString(),
        ];
    }

    private function profileKey(int|string $userId): string
    {
        return self::PROFILE_PREFIX.$userId;
    }

    /**
     * Bọc thao tác Redis để đảm bảo không làm hỏng luồng chính
     */
    private function execute(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            Log::warning('UserRedisService error: '.$e->getMessage());

            return null;
        }
    }
}

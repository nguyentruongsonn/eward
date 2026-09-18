<?php

namespace App\Support;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class Idempotency
{
    /**
     * Execute a model-producing operation once for an actor and idempotency key.
     *
     * The distributed lock closes the read/create race when multiple retries
     * arrive at the same time. A payload fingerprint prevents accidental reuse
     * of one key for a different request.
     */
    public function rememberModel(
        string $scope,
        string|int $actorId,
        ?string $key,
        string $fingerprint,
        Closure $lookup,
        Closure $operation,
    ): Model {
        $key = trim((string) $key);
        if ($key === '') {
            return $operation();
        }

        $cacheKey = 'api:idempotency:'.$scope.':'.hash('sha256', (string) $actorId.':'.$key);

        try {
            return Cache::lock($cacheKey.':lock', 10)->block(5, function () use ($cacheKey, $fingerprint, $lookup, $operation): Model {
                $record = Cache::get($cacheKey);
                if ($record !== null) {
                    $recordId = is_array($record) ? ($record['id'] ?? null) : $record;
                    $recordFingerprint = is_array($record) ? ($record['fingerprint'] ?? null) : null;

                    if ($recordFingerprint !== null && ! hash_equals((string) $recordFingerprint, $fingerprint)) {
                        throw new ApiException(
                            'Idempotency-Key đã được sử dụng cho dữ liệu khác.',
                            'IDEMPOTENCY_KEY_REUSED',
                            409,
                        );
                    }

                    if ($recordId !== null && ($existing = $lookup($recordId)) instanceof Model) {
                        return $existing;
                    }

                    Cache::forget($cacheKey);
                }

                $result = $operation();
                Cache::put($cacheKey, [
                    'id' => $result->getKey(),
                    'fingerprint' => $fingerprint,
                ], now()->addHours(24));

                return $result;
            });
        } catch (LockTimeoutException) {
            throw new ApiException(
                'Yêu cầu trùng đang được xử lý, vui lòng thử lại.',
                'IDEMPOTENCY_IN_PROGRESS',
                409,
            );
        }
    }
}

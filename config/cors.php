<?php

$origins = array_values(array_filter(
    array_map(
        static fn (string $origin): string => trim($origin),
        explode(',', (string) env('FRONTEND_ORIGINS', 'http://localhost:5173')),
    ),
    static fn (string $origin): bool => $origin !== '' && $origin !== '*',
));

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => $origins,
    'allowed_origins_patterns' => [],
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'Idempotency-Key',
        'X-Request-Id',
    ],
    'exposed_headers' => ['X-Request-Id'],
    'max_age' => 0,
    // JWT is carried in Authorization, not a cross-origin browser cookie.
    // This keeps an explicit origin allowlist valid under the CORS spec.
    'supports_credentials' => false,
];

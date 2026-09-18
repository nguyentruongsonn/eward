<?php

return [
    /*
    | Optional structured log sink for a collector (stderr, syslog, or an
    | application-specific channel). The default logger remains unchanged so
    | local development and existing integrations keep receiving api.request.
    */
    'api_request_channel' => env('API_TELEMETRY_CHANNEL'),
];

<?php

return [
    'enabled' => filter_var(env('ELASTICSEARCH_ENABLED', false), FILTER_VALIDATE_BOOL),
    'hosts' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('ELASTICSEARCH_HOSTS', 'http://127.0.0.1:9200')),
    ))),
    'api_key' => env('ELASTICSEARCH_API_KEY'),
    'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', 'eward_'),
    'timeout' => (int) env('ELASTICSEARCH_TIMEOUT', 3),
];

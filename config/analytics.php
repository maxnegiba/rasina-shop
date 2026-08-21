<?php

return [
    'property_id' => env('ANALYTICS_PROPERTY_ID'),

    'service_account_credentials_json' => env(
        'ANALYTICS_CREDENTIALS_PATH',
        storage_path('app/analytics/service-account-credentials.json'),
    ),

    'cache_lifetime_in_minutes' => 60,

    'cache' => [
        'store' => env('ANALYTICS_CACHE_STORE', 'file'),
    ],
];

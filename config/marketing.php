<?php

return [
    'tracking_enabled' => (bool) env('MARKETING_TRACKING_ENABLED', false),

    'gtm' => [
        'container_id' => env('GTM_CONTAINER_ID'),
    ],

    'umami' => [
        'enabled' => (bool) env('UMAMI_ENABLED', false),
        'script_url' => env('UMAMI_SCRIPT_URL', 'https://analytics.mtdart.ro/script.js'),
        'website_id' => env('UMAMI_WEBSITE_ID'),
        'domains' => env('UMAMI_DOMAINS', 'mtdart.ro,www.mtdart.ro'),
    ],

    'meta' => [
        'pixel_id' => env('META_PIXEL_ID'),
        'capi_access_token' => env('META_CAPI_ACCESS_TOKEN'),
        'graph_api_version' => env('META_GRAPH_API_VERSION', 'v23.0'),
        'test_event_code' => env('META_CAPI_TEST_EVENT_CODE'),
    ],
];

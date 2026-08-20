<?php

return [
    'tracking_enabled' => (bool) env('MARKETING_TRACKING_ENABLED', false),

    'gtm' => [
        'container_id' => env('GTM_CONTAINER_ID'),
    ],

    'meta' => [
        'pixel_id' => env('META_PIXEL_ID'),
        'capi_access_token' => env('META_CAPI_ACCESS_TOKEN'),
        'graph_api_version' => env('META_GRAPH_API_VERSION', 'v23.0'),
    ],
];

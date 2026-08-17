<?php

return [
    'tracking_enabled' => (bool) env('MARKETING_TRACKING_ENABLED', false),

    'gtm' => [
        'container_id' => env('GTM_CONTAINER_ID'),
    ],
];

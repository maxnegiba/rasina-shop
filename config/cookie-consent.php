<?php

return [
    'theme' => 'default',
    'filament-nav-item-render-hook' => null,

    'cookie_key' => '__cookie_consent',
    'cookie_value_analytics' => '2',
    'cookie_value_marketing' => '3',
    'cookie_value_both' => 'true',
    'cookie_value_none' => 'false',
    'cookie_expiration_days' => '365',

    // Reserved for the GTM stage. No tracking tags are installed yet.
    'gtm_event' => 'cookie_refresh',
    'ignored_paths' => [],
    'skip_on_error_responses' => true,
    'cookie_secure' => env('COOKIE_CONSENT_SECURE', env('APP_ENV') === 'production'),

    'policy_url_ro' => env('COOKIE_POLICY_URL_RO', '/pagina/politica-de-confidentialitate'),
];

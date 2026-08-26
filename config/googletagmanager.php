<?php

return [
    /*
     * The package is used only as a server-side DataLayer abstraction.
     * GTM itself is injected by App\Http\Middleware\InjectGoogleTagManager,
     * which also guarantees our consent-mode bootstrap runs first.
     */
    'id' => env('GTM_CONTAINER_ID', ''),

    /*
     * Keep Spatie's own script rendering disabled so there is a single GTM
     * bootstrap path and no possibility of loading a duplicate container.
     */
    'enabled' => false,

    'sessionKey' => '_googleTagManager',

    'domain' => 'www.googletagmanager.com',

    'nonceEnabled' => false,
];

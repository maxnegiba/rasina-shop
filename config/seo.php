<?php

use RalphJSmit\Laravel\SEO\Models\SEO;

return [
    /**
     * The SEO model. You can use this setting to override the model used by the package.
     * Make sure to always extend the old model, so that you'll not lose functionality during upgrades.
     */
    'model' => SEO::class,

    /**
     * Use this setting to specify the site name that will be used in OpenGraph tags.
     */
    'site_name' => config('app.name'),

    /**
     * Use this setting to specify the path to the sitemap of your website. This exact path will outputted, so
     * you can use both a hardcoded url and a relative path. We recommend the latter.
     */
    'sitemap' => '/sitemap.xml',

    /**
     * Add self-referencing canonical tags to storefront pages.
     */
    'canonical_link' => true,

    'robots' => [
        'default' => 'max-snippet:-1,max-image-preview:large,max-video-preview:-1',
        'force_default' => false,
    ],

    'favicon' => null,

    'title' => [
        'infer_title_from_url' => true,
        'suffix' => '',
        'homepage_title' => 'MTD ART | Piese artizanale unicat din rășină',
    ],

    'description' => [
        'fallback' => 'Piese artizanale MTD ART din rășină, lemn și elemente naturale, create manual în România. Descoperă obiecte unicat, decorațiuni și cadouri cu semnificație.',
    ],

    'image' => [
        'fallback' => '/img/logo.png',
    ],

    'author' => [
        'fallback' => 'MTD ART',
    ],

    'twitter' => [
        '@username' => null,
    ],
];

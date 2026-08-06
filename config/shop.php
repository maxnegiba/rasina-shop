<?php

return [
    'brand_name' => env('SHOP_BRAND_NAME', 'MTD Art'),

    'legal' => [
        'business_name' => env('SHOP_LEGAL_BUSINESS_NAME', '[DENUMIREA LEGALĂ A OPERATORULUI]'),
        'tax_id' => env('SHOP_LEGAL_TAX_ID', '[CUI/CIF]'),
        'trade_register' => env('SHOP_LEGAL_TRADE_REGISTER', '[NR. REGISTRUL COMERȚULUI]'),
        'address' => env('SHOP_LEGAL_ADDRESS', '[ADRESA SEDIULUI SOCIAL]'),
        'email' => env('SHOP_LEGAL_EMAIL', 'contact@mtdart.ro'),
        'phone' => env('SHOP_LEGAL_PHONE', '[TELEFON]'),
        'iban' => env('SHOP_LEGAL_IBAN'),
        'bank' => env('SHOP_LEGAL_BANK'),
    ],

    'terms_version' => env('SHOP_TERMS_VERSION', '2026-08-06'),

    'checkout_reservation_minutes' => (int) env('SHOP_CHECKOUT_RESERVATION_MINUTES', 31),

    // The amount charged once per order, in RON. Keep this aligned with the
    // delivery promise shown in the cart, checkout and legal pages.
    'shipping_cost' => max(0, (float) env('SHOP_SHIPPING_COST', 0)),
];

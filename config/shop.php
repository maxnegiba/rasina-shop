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

    'terms_version' => env('SHOP_TERMS_VERSION', '2026-08-05'),
];

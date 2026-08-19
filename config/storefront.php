<?php

return [
    'base_currency' => env('STOREFRONT_BASE_CURRENCY', 'BDT'),

    'default_locale' => env('STOREFRONT_DEFAULT_LOCALE', env('APP_LOCALE', 'en')),
    'default_currency' => env('STOREFRONT_DEFAULT_CURRENCY', 'BDT'),

    'locales' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'short' => 'EN',
            'rtl' => false,
        ],
        'bn' => [
            'name' => 'Bangla',
            'native' => 'Bangla',
            'short' => 'BN',
            'rtl' => false,
        ],
        'hi' => [
            'name' => 'Hindi',
            'native' => 'Hindi',
            'short' => 'HI',
            'rtl' => false,
        ],
        'es' => [
            'name' => 'Spanish',
            'native' => 'Espanol',
            'short' => 'ES',
            'rtl' => false,
        ],
        'ar' => [
            'name' => 'Arabic',
            'native' => 'Al Arabia',
            'short' => 'AR',
            'rtl' => true,
        ],
    ],

    // Rates are relative to base currency (BDT by default).
    'currencies' => [
        'BDT' => [
            'name' => 'Bangladeshi Taka',
            'symbol' => 'Tk ',
            'rate' => 1,
            'decimals' => 0,
            'symbol_position' => 'prefix',
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'rate' => 0.0091,
            'decimals' => 2,
            'symbol_position' => 'prefix',
        ],
    ],
];

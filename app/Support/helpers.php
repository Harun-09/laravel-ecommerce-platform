<?php

use App\Support\StorefrontPreferences;

if (!function_exists('store_locale')) {
    function store_locale(): string
    {
        return StorefrontPreferences::activeLocale();
    }
}

if (!function_exists('store_locale_meta')) {
    function store_locale_meta(): array
    {
        return StorefrontPreferences::localeMeta();
    }
}

if (!function_exists('store_currency')) {
    function store_currency(): string
    {
        return StorefrontPreferences::activeCurrency();
    }
}

if (!function_exists('store_currency_meta')) {
    function store_currency_meta(): array
    {
        return StorefrontPreferences::currencyMeta();
    }
}

if (!function_exists('store_is_rtl')) {
    function store_is_rtl(): bool
    {
        return StorefrontPreferences::isRtl();
    }
}

if (!function_exists('store_money')) {
    function store_money($amount, ?int $decimals = null): string
    {
        return StorefrontPreferences::format($amount, null, $decimals);
    }
}

if (!function_exists('store_money_code')) {
    function store_money_code($amount, ?int $decimals = null): string
    {
        return StorefrontPreferences::formatWithCode($amount, null, $decimals);
    }
}

if (!function_exists('store_convert')) {
    function store_convert($amount): float
    {
        return StorefrontPreferences::convert($amount);
    }
}

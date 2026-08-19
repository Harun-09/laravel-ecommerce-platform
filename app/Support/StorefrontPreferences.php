<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class StorefrontPreferences
{
    public const SESSION_LOCALE_KEY = 'storefront.locale';
    public const SESSION_CURRENCY_KEY = 'storefront.currency';

    public static function locales(): array
    {
        return config('storefront.locales', []);
    }

    public static function currencies(): array
    {
        return config('storefront.currencies', []);
    }

    public static function baseCurrency(): string
    {
        return strtoupper((string) config('storefront.base_currency', 'BDT'));
    }

    public static function defaultLocale(): string
    {
        return self::resolveLocale((string) config('storefront.default_locale', config('app.locale', 'en')));
    }

    public static function defaultCurrency(): string
    {
        return self::resolveCurrency((string) config('storefront.default_currency', self::baseCurrency()));
    }

    public static function resolveLocale(?string $locale): string
    {
        $locale = trim((string) $locale);
        $locales = self::locales();

        if ($locale !== '' && array_key_exists($locale, $locales)) {
            return $locale;
        }

        $default = trim((string) config('storefront.default_locale', 'en'));
        if ($default !== '' && array_key_exists($default, $locales)) {
            return $default;
        }

        return array_key_first($locales) ?: 'en';
    }

    public static function resolveCurrency(?string $currency): string
    {
        $currency = strtoupper(trim((string) $currency));
        $currencies = self::currencies();

        if ($currency !== '' && array_key_exists($currency, $currencies)) {
            return $currency;
        }

        $default = strtoupper(trim((string) config('storefront.default_currency', self::baseCurrency())));
        if ($default !== '' && array_key_exists($default, $currencies)) {
            return $default;
        }

        return array_key_first($currencies) ?: self::baseCurrency();
    }

    public static function applyToRequest(Request $request): array
    {
        $locale = self::resolveLocale($request->session()->get(self::SESSION_LOCALE_KEY));
        $currency = self::resolveCurrency($request->session()->get(self::SESSION_CURRENCY_KEY));

        $request->session()->put(self::SESSION_LOCALE_KEY, $locale);
        $request->session()->put(self::SESSION_CURRENCY_KEY, $currency);

        App::setLocale($locale);

        return [
            'locale' => $locale,
            'currency' => $currency,
            'locale_meta' => self::localeMeta($locale),
            'currency_meta' => self::currencyMeta($currency),
            'locales' => self::locales(),
            'currencies' => self::currencies(),
            'is_rtl' => self::isRtl($locale),
            'base_currency' => self::baseCurrency(),
        ];
    }

    public static function activeLocale(?Request $request = null): string
    {
        $request ??= request();

        if (!$request instanceof Request) {
            return self::defaultLocale();
        }

        return self::resolveLocale($request->session()->get(self::SESSION_LOCALE_KEY));
    }

    public static function activeCurrency(?Request $request = null): string
    {
        $request ??= request();

        if (!$request instanceof Request) {
            return self::defaultCurrency();
        }

        return self::resolveCurrency($request->session()->get(self::SESSION_CURRENCY_KEY));
    }

    public static function localeMeta(?string $locale = null): array
    {
        $locale = self::resolveLocale($locale ?: self::activeLocale());
        return self::locales()[$locale] ?? [];
    }

    public static function currencyMeta(?string $currency = null): array
    {
        $currency = self::resolveCurrency($currency ?: self::activeCurrency());
        return self::currencies()[$currency] ?? [];
    }

    public static function isRtl(?string $locale = null): bool
    {
        $meta = self::localeMeta($locale ?: self::activeLocale());
        return (bool) ($meta['rtl'] ?? false);
    }

    public static function convert($amount, ?string $currency = null): float
    {
        $value = is_numeric($amount) ? (float) $amount : 0.0;
        $meta = self::currencyMeta($currency ?: self::activeCurrency());
        $rate = (float) ($meta['rate'] ?? 1);

        return $value * $rate;
    }

    public static function format($amount, ?string $currency = null, ?int $decimals = null): string
    {
        $currency = self::resolveCurrency($currency ?: self::activeCurrency());
        $meta = self::currencyMeta($currency);
        $converted = self::convert($amount, $currency);
        $precision = $decimals ?? (int) ($meta['decimals'] ?? 2);
        $number = number_format($converted, $precision, '.', ',');

        $symbol = (string) ($meta['symbol'] ?? ($currency . ' '));
        $position = (string) ($meta['symbol_position'] ?? 'prefix');

        return $position === 'suffix'
            ? $number . $symbol
            : $symbol . $number;
    }

    public static function formatWithCode($amount, ?string $currency = null, ?int $decimals = null): string
    {
        $currency = self::resolveCurrency($currency ?: self::activeCurrency());
        return $currency . ' ' . self::format($amount, $currency, $decimals);
    }

    public static function toClientConfig(?string $currency = null): array
    {
        $currency = self::resolveCurrency($currency ?: self::activeCurrency());
        $meta = self::currencyMeta($currency);

        return [
            'code' => $currency,
            'symbol' => (string) ($meta['symbol'] ?? ($currency . ' ')),
            'rate' => (float) ($meta['rate'] ?? 1),
            'decimals' => (int) ($meta['decimals'] ?? 2),
            'symbol_position' => (string) ($meta['symbol_position'] ?? 'prefix'),
            'base_currency' => self::baseCurrency(),
        ];
    }
}

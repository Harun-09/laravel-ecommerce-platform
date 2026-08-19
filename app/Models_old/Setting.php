<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("setting_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        }

        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]
        );

        Cache::forget("setting_{$key}");
    }

    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(fn($setting) => [$setting->key => self::get($setting->key)])
            ->toArray();
    }

    // Common settings helpers
    public static function siteName(): string
    {
        return self::get('site_name', config('app.name'));
    }

    public static function siteLogo(): ?string
    {
        $logo = self::get('site_logo');
        return $logo ? asset('storage') . '/' . $logo : null;
    }

    public static function currency(): string
    {
        return self::get('currency', 'BDT');
    }

    public static function currencySymbol(): string
    {
        return self::get('currency_symbol', '৳');
    }

    public static function globalCommissionRate(): float
    {
        return (float) self::get('global_commission_rate', 10);
    }

    public static function minPayoutAmount(): float
    {
        return (float) self::get('min_payout_amount', 500);
    }
}

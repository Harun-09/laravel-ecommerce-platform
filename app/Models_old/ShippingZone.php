<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'regions',
        'is_active',
        'order',
    ];

    protected $casts = [
        'regions' => 'array',
        'is_active' => 'boolean',
    ];

    public function methods()
    {
        return $this->hasMany(ShippingMethod::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public static function resolveByCity(?string $city): ?self
    {
        $city = trim((string) $city);

        if ($city !== '') {
            $zone = self::query()
                ->active()
                ->whereJsonContains('regions', $city)
                ->first();

            if ($zone) {
                return $zone;
            }
        }

        return self::query()
            ->active()
            ->where('code', 'outside_dhaka')
            ->first() ?: self::query()->active()->ordered()->first();
    }

    public function isInsideDhaka(): bool
    {
        if ($this->code === 'inside_dhaka') {
            return true;
        }

        return str_contains(strtolower($this->name), 'dhaka')
            && !str_contains(strtolower($this->name), 'outside');
    }
}

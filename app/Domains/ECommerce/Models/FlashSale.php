<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FlashSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flashSale) {
            if (empty($flashSale->slug)) {
                $flashSale->slug = Str::slug($flashSale->name);
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')
            ->withPivot('discount_price', 'quantity_limit', 'sold_count')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRunning($query)
    {
        return $query->active()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->active()
            ->where('starts_at', '>', now());
    }

    public function isRunning(): bool
    {
        return $this->is_active &&
            $this->starts_at <= now() &&
            $this->ends_at >= now();
    }

    public function getTimeRemainingAttribute(): ?int
    {
        if (!$this->isRunning()) {
            return null;
        }
        return now()->diffInSeconds($this->ends_at, false);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }
}


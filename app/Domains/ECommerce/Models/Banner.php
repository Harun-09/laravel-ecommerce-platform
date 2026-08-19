<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'mobile_image',
        'link',
        'button_text',
        'position',
        'order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    
    public function getImageUrlAttribute(): string
    {
        $path = ltrim((string)$this->image, '/');

        if ($path === '' || $path === 'images/no-banner-image.svg') {
            return asset('images') . '/placeholders/no-banner-image.svg';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Search in root directories
        $candidates = [
            $path,
            'images/' . $path,
            'storage/' . $path,
            'images/banners/' . $path,
        ];

        foreach ($candidates as $candidate) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                return asset('storage') . '/' . ltrim($candidate, '/');
            }
        }

        foreach ($candidates as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset(ltrim($candidate, '/'));
            }
        }

        return asset('images') . '/placeholders/no-banner-image.svg';
    }

    public function getMobileImageUrlAttribute(): ?string
    {
        $path = $this->mobile_image;

        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $candidates = [
            $path,
            'images/' . $path,
            'storage/' . $path,
            'images/banners/' . $path,
        ];

        foreach ($candidates as $candidate) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                return asset('storage') . '/' . ltrim($candidate, '/');
            }
        }

        foreach ($candidates as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset(ltrim($candidate, '/'));
            }
        }

        return null;
    }
}

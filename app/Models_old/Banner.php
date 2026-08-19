<?php

namespace App\Models;

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
            $clean = ltrim($candidate, '/');
            if (file_exists(base_path($clean)) || file_exists(public_path($clean))) {
                return asset($clean) . '?v=' . time();
            }
        }

        return asset('images') . '/placeholders/no-banner-image.svg' . '?v=' . time();
    }

    public function getMobileImageUrlAttribute(): ?string
    {
        $path = $this->normalizeImagePath($this->mobile_image);

        if ($path === '') {
            return null;
        }

        if ($this->isExternalUrl($path)) {
            return $path;
        }

        foreach ($this->storageCandidates($path) as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return asset('storage') . '/' . ltrim($candidate, '/');
            }
        }

        foreach ($this->publicCandidates($path) as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset(ltrim($candidate, '/'));
            }
        }

        return null;
    }

    private function normalizeImagePath(?string $path): string
    {
        return ltrim(str_replace('\\', '/', trim((string) $path)), '/');
    }

    private function isExternalUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }

    private function storageCandidates(string $path): array
    {
        $candidates = [$path];

        if (str_starts_with($path, 'storage/')) {
            $candidates[] = ltrim(substr($path, strlen('storage/')), '/');
        }

        if (str_starts_with($path, 'public/')) {
            $candidates[] = ltrim(substr($path, strlen('public/')), '/');
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function publicCandidates(string $path): array
    {
        $candidates = [$path];

        if (!str_starts_with($path, 'storage/')) {
            $candidates[] = 'storage/' . ltrim($path, '/');
        }

        return array_values(array_unique(array_filter($candidates)));
    }
}

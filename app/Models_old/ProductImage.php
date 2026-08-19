<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'image',
        'alt_text',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        $rawImage = trim((string) $this->image);
        if ($rawImage === '') {
            return asset('images') . '/placeholders/no-product-image.svg';
        }

        if (preg_match('#^https?://#i', $rawImage) === 1) {
            return $rawImage;
        }

        $path = ltrim($rawImage, '/');

        if ($path === '' || $path === 'images/no-product-image.svg') {
            return asset('images') . '/placeholders/no-product-image.svg';
        }

        // 1. Try public disk (storage/app/public)
        if (Storage::disk('public')->exists($path)) {
            return asset('storage') . '/' . $path;
        }

        // 2. Try direct public folder
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        // 3. Try to find in category subfolder (if path is images/products/filename.jpg)
        $parts = explode('/', $path);
        if (count($parts) === 3 && str_starts_with($path, 'images/products/')) {
            $filename = end($parts);
            $categorySlug = $this->product?->category?->slug;
            if ($categorySlug) {
                $categoryCandidates = [
                    "images/products/$categorySlug/$filename",
                    "storage/images/products/$categorySlug/$filename",
                ];
                foreach ($categoryCandidates as $candidate) {
                    if (file_exists(public_path($candidate))) {
                        return asset($candidate);
                    }
                    if (Storage::disk('public')->exists($candidate)) {
                        return asset('storage') . '/' . $candidate;
                    }
                }
            }
        }

        // 4. Try common storage variations
        // 4. Try common storage and gallery variations
        $productId = $this->product_id;
        $storageVariations = [
            $path,
            'products/' . $path,
            'images/products/' . $path,
            "products/gallery/$productId/02-focus-center.jpg",
            "products/gallery/$productId/primary.jpg",
            "products/gallery/$productId/01.jpg",
        ];

        foreach ($storageVariations as $variation) {
            $cleanVariation = ltrim($variation, '/');
            
            // Try standard public storage
            if (Storage::disk('public')->exists($cleanVariation)) {
                return asset('storage') . '/' . $cleanVariation . '?v=' . time();
            }
            
            // Try direct public folder
            if (file_exists(public_path($cleanVariation))) {
                return asset($cleanVariation) . '?v=' . time();
            }

            // Try the absolute storage/app/public path found in debugger
            $appStoragePath = storage_path('app/public/' . $cleanVariation);
            if (file_exists($appStoragePath)) {
                return asset('storage') . '/' . $cleanVariation . '?v=' . time();
            }
        }

        // 5. Product-specific curated fallback from storage/products/openverse/product-{id}-*.jpg
        $openverseFallback = $this->resolveOpenverseFallbackPath();
        if ($openverseFallback !== null && Storage::disk('public')->exists($openverseFallback)) {
            return asset('storage') . '/' . $openverseFallback . '?v=' . time();
        }

        return asset('images') . '/placeholders/no-product-image.svg' . '?v=' . time();
    }

    private function resolveOpenverseFallbackPath(): ?string
    {
        $productId = (int) $this->product_id;
        if ($productId <= 0) {
            return null;
        }

        static $fallbackMap = null;
        if (!is_array($fallbackMap)) {
            $fallbackMap = [];

            foreach (Storage::disk('public')->files('products/openverse') as $filePath) {
                $cleanPath = ltrim(trim((string) $filePath), '/');
                if ($cleanPath === '') {
                    continue;
                }

                if (preg_match('/^products\/openverse\/product-(\d+)-.+\.(jpg|jpeg|png|webp)$/i', $cleanPath, $matches) !== 1) {
                    continue;
                }

                $matchedProductId = (int) ($matches[1] ?? 0);
                if ($matchedProductId <= 0 || isset($fallbackMap[$matchedProductId])) {
                    continue;
                }

                $fallbackMap[$matchedProductId] = $cleanPath;
            }
        }

        return $fallbackMap[$productId] ?? null;
    }

    private function shouldPreferOpenverseFallback(string $path): bool
    {
        $path = ltrim(trim($path), '/');
        if ($path === '') {
            return false;
        }

        if (str_starts_with($path, 'images/products/')) {
            return true;
        }

        if (str_starts_with($path, 'products/generated/')) {
            return true;
        }

        return preg_match('/^products\/gallery\/\d+\/primary-[^\/]+\.(jpg|jpeg|png|webp)$/i', $path) === 1;
    }

    private function isRenderableImage(string $absolutePath): bool
    {
        if (!is_file($absolutePath)) {
            return false;
        }

        $size = @filesize($absolutePath);
        if (!is_int($size) || $size < 256) {
            return false;
        }

        $info = @getimagesize($absolutePath);
        if (!is_array($info)) {
            return false;
        }

        return (int) ($info[0] ?? 0) > 1 && (int) ($info[1] ?? 0) > 1;
    }
}

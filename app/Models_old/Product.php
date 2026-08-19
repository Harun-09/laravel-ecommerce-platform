<?php

namespace App\Models;

use App\Domains\ECommerce\Models\Concerns\EnforcesVendorIsolation;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, EnforcesVendorIsolation;

    protected array $auditPriceChangeSnapshot = [];

    protected $fillable = [
        'vendor_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'quantity',
        'low_stock_threshold',
        'track_quantity',
        'allow_backorder',
        'weight',
        'weight_unit',
        'length',
        'width',
        'height',
        'dimension_unit',
        'status',
        'rejection_reason',
        'featured',
        'is_digital',
        'digital_file',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views',
        'sales_count',
        'rating',
        'reviews_count',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'rating' => 'decimal:2',
        'track_quantity' => 'boolean',
        'allow_backorder' => 'boolean',
        'featured' => 'boolean',
        'is_digital' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function (self $product): void {
            $snapshot = [];

            foreach (['price', 'compare_price', 'cost_price'] as $field) {
                if (!$product->isDirty($field)) {
                    continue;
                }

                $snapshot[$field] = [
                    'old' => $product->getOriginal($field),
                    'new' => $product->{$field},
                ];
            }

            $product->auditPriceChangeSnapshot = $snapshot;
        });

        static::updated(function (self $product): void {
            if (empty($product->auditPriceChangeSnapshot)) {
                return;
            }

            $oldValues = [];
            $newValues = [];

            foreach ($product->auditPriceChangeSnapshot as $field => $diff) {
                $oldValues[$field] = $diff['old'];
                $newValues[$field] = $diff['new'];
            }

            app(AuditLogService::class)->log(
                'product.price_changed',
                $product,
                $oldValues,
                $newValues,
                [
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'vendor_id' => (int) ($product->vendor_id ?? 0),
                ],
            );

            $product->auditPriceChangeSnapshot = [];
        });
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function questions()
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)
            ->where('is_approved', true)
            ->where('is_verified_purchase', true);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function flashSales()
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_products')
            ->withPivot('discount_price', 'quantity_limit', 'sold_count')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('quantity', '>', 0)
                ->orWhere('allow_backorder', true)
                ->orWhere('track_quantity', false);
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%");
        });
    }

    public function scopePriceRange($query, ?float $min = null, ?float $max = null)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    // Helpers
    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return $primary->image_url;
        }

        $first = $this->images->first();
        if ($first) {
            return $first->image_url;
        }

        return asset('images') . '/placeholders/no-product-image.svg';
    }

    public function getListingImageUrlAttribute(): string
    {
        // Prefer product's primary image for listing cards to keep product-image mapping consistent.
        $primary = $this->primaryImage;
        if ($primary) {
            $resolvedUrl = trim((string) $primary->image_url);
            if ($this->isResolvedListingImageUrl($resolvedUrl)) {
                return $resolvedUrl;
            }

            if ($this->imageExistsOnPublicDisk($primary->image)) {
                return $this->storageAssetUrlWithVersion((string) $primary->image);
            }
        }

        $first = $this->images->first();
        if ($first) {
            $resolvedUrl = trim((string) $first->image_url);
            if ($this->isResolvedListingImageUrl($resolvedUrl)) {
                return $resolvedUrl;
            }

            if ($this->imageExistsOnPublicDisk($first->image)) {
                return $this->storageAssetUrlWithVersion((string) $first->image);
            }
        }

        // Backward-compatibility fallback for legacy generated listing variants.
        $variantPaths = [
            'products/gallery/' . $this->id . '/02-focus-center.jpg',
            'products/gallery/' . $this->id . '/primary.jpg',
            'products/gallery/' . $this->id . '/01.jpg',
            'products/gallery/' . $this->id . '/03-focus-left.jpg',
            'products/gallery/' . $this->id . '/04-focus-right.jpg',
        ];

        $seed = (string) ($this->slug ?: $this->id);
        $startIndex = abs(crc32($seed)) % count($variantPaths);

        $orderedPaths = [$variantPaths[$startIndex]];
        foreach ($variantPaths as $path) {
            $orderedPaths[] = $path;
        }

        foreach (array_values(array_unique($orderedPaths)) as $path) {
            if ($this->imageExistsOnPublicDisk($path)) {
                return $this->storageAssetUrlWithVersion($path);
            }
        }

        return $this->primary_image_url;
    }

    private function isResolvedListingImageUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        return !str_contains($url, '/images/placeholders/no-product-image.svg')
            && !str_contains($url, '/images/no-product-image.svg');
    }

    private function imageExistsOnPublicDisk(?string $path): bool
    {
        $path = ltrim(trim((string) $path), '/');
        if ($path === '') {
            return false;
        }

        return Storage::disk('public')->exists($path) || file_exists(public_path($path));
    }

    private function storageAssetUrlWithVersion(string $path): string
    {
        $path = ltrim(trim($path), '/');
        if ($path === '') {
            return asset('images') . '/placeholders/no-product-image.svg';
        }

        $url = asset('storage') . '/' . $path;
        
        // If not in storage but in public folder directly
        if (!Storage::disk('public')->exists($path) && file_exists(public_path($path))) {
            $url = asset($path);
        }

        static $versionCache = [];

        if (isset($versionCache[$path])) {
            return $url . '?v=' . $versionCache[$path];
        }

        try {
            $disk = Storage::disk('public');
            $absolutePath = '';
            
            if ($disk->exists($path)) {
                $absolutePath = $disk->path($path);
            } elseif (file_exists(public_path($path))) {
                $absolutePath = public_path($path);
            }

            if ($absolutePath !== '' && is_file($absolutePath)) {
                $version = '';

                try {
                    $hash = md5_file($absolutePath);
                    if (is_string($hash) && $hash !== '') {
                        $version = substr($hash, 0, 12);
                    }
                } catch (\Throwable $e) { /* fallback */ }

                if ($version === '') {
                    $mtime = filemtime($absolutePath);
                    $size = filesize($absolutePath);
                    $version = $mtime . '-' . $size;
                }

                if ($version !== '') {
                    $versionCache[$path] = $version;
                    return $url . '?v=' . $version;
                }
            }
        } catch (\Throwable $e) {
            // Return plain URL if filesystem metadata lookup fails.
        }

        return $url;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return null;
    }

    public function getDisplayDescriptionAttribute(): string
    {
        $short = trim(strip_tags((string) $this->short_description));
        if ($short !== '') {
            return $short;
        }

        $description = trim(strip_tags((string) $this->description));
        if ($description !== '') {
            return $description;
        }

        return 'No description available.';
    }

    public function getFinalPriceAttribute(): float
    {
        // Check for flash sale
        $flashSale = $this->getCurrentFlashSale();
        if ($flashSale) {
            return $flashSale->pivot->discount_price;
        }
        return $this->price;
    }

    public function getCurrentFlashSale()
    {
        return $this->flashSales()
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    public function isInStock(): bool
    {
        if (!$this->track_quantity) {
            return true;
        }
        if ($this->allow_backorder) {
            return true;
        }
        return $this->quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->track_quantity && $this->quantity <= $this->low_stock_threshold && $this->quantity > 0;
    }

    public function decrementStock(int $quantity = 1): bool
    {
        if (!$this->track_quantity) {
            return true;
        }
        if ($this->quantity >= $quantity) {
            $this->decrement('quantity', $quantity);
            return true;
        }
        return false;
    }

    public function incrementStock(int $quantity = 1): void
    {
        if ($this->track_quantity) {
            $this->increment('quantity', $quantity);
        }
    }

    public function updateRating(): void
    {
        $this->rating = $this->approvedReviews()->avg('rating') ?? 0;
        $this->reviews_count = $this->approvedReviews()->count();
        $this->save();
    }
}

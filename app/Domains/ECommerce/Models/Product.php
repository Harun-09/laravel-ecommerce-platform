<?php

namespace App\Domains\ECommerce\Models;

use App\Domains\ECommerce\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'sku',
        'name',
        'slug',
        'description',
        'tags',
        'base_price',
        'moq',
        'stock_quantity',
        'reserved_quantity',
        'status',
        'published_at',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'tags' => 'array',
        'published_at' => 'datetime',
        'status' => ProductStatus::class,
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order')->orderBy('id');
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(PricingTier::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function availableStock(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function lowStockThreshold(): int
    {
        return max(10, (int) $this->moq);
    }

    public function isLowStock(): bool
    {
        return $this->availableStock() <= $this->lowStockThreshold();
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->orderByDesc('is_primary')->orderBy('order')->orderBy('id');
    }

    public function primaryImageUrl(): string
    {
        return $this->primaryImage?->url() ?: asset('images/landing/deal-imac.jpg');
    }

    /**
     * @return array<int, array{id:int,url:string,alt:string,is_primary:bool}>
     */
    public function galleryImages(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->map(function (ProductImage $image): array {
            return [
                'id' => $image->id,
                'url' => $image->url() ?: asset('images/landing/deal-imac.jpg'),
                'alt' => $image->alt_text ?: $this->name,
                'is_primary' => (bool) $image->is_primary,
            ];
        })->values()->all();
    }
    // --- Legacy / UI Compatibility Methods ---

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
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
            ->withPivot(['flash_price', 'flash_quantity', 'sold_quantity'])
            ->withTimestamps();
    }

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
            $q->where('stock_quantity', '>', 0)
                ->orWhere('allow_backorder', true)
                ->orWhere('track_quantity', false);
        });
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['active', 'published'])
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
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('tags', 'like', "%{$term}%");
        });
    }

    public function scopePriceRange($query, ?float $min = null, ?float $max = null)
    {
        if ($min !== null) {
            $query->where('base_price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('base_price', '<=', $max);
        }
        return $query;
    }

    public function getCurrentFlashSale()
    {
        return $this->flashSales()
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();
    }

    public function isInStock(): bool
    {
        if (!($this->track_quantity ?? true)) {
            return true;
        }
        return ($this->stock_quantity ?? 0) > 0 || ($this->allow_backorder ?? false);
    }

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
        $primary = $this->primaryImage;
        if ($primary) {
            $resolvedUrl = trim((string) $primary->image_url);
            if ($this->isResolvedListingImageUrl($resolvedUrl)) {
                return $resolvedUrl;
            }

            if ($this->imageExistsOnPublicDisk($primary->image)) {
                return asset('storage/' . $primary->image);
            }
        }

        $first = $this->images->first();
        if ($first) {
            $resolvedUrl = trim((string) $first->image_url);
            if ($this->isResolvedListingImageUrl($resolvedUrl)) {
                return $resolvedUrl;
            }

            if ($this->imageExistsOnPublicDisk($first->image)) {
                return asset('storage/' . $first->image);
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
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return true;
        }
        return false;
    }

    private function imageExistsOnPublicDisk(?string $path): bool
    {
        $path = trim((string) $path);
        if ($path === '') {
            return false;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
    }

    public function getPriceAttribute($value): float
    {
        return (float) ($value ?? $this->base_price ?? 0);
    }

    public function getComparePriceAttribute($value): ?float
    {
        return $value ? (float) $value : null;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        $price = $this->price;
        $comparePrice = $this->compare_price;
        
        if ($comparePrice && $comparePrice > $price) {
            return round((($comparePrice - $price) / $comparePrice) * 100);
        }
        return null;
    }

    public function getDisplayDescriptionAttribute(): string
    {
        $short = trim(strip_tags((string) $this->short_description));
        if ($short !== '') return $short;

        $description = trim(strip_tags((string) $this->description));
        if ($description !== '') return $description;

        return 'No description available.';
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->price;
    }
}









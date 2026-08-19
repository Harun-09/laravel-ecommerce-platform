<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'commission_rate',
        'order',
        'is_active',
        'featured',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'commission_rate' => 'decimal:2',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getFullPathAttribute(): string
    {
        $path = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $path->implode(' > ');
    }

    public function getAllProductsCount(): int
    {
        $count = $this->products()->active()->count();

        foreach ($this->children as $child) {
            $count += $child->getAllProductsCount();
        }

        return $count;
    }
    // --- Legacy / UI Compatibility Methods ---

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getImageUrlAttribute(): string
    {
        $path = ltrim((string) $this->image, '/');

        if ($path === '' || $path === 'images/placeholders/no-category-image.svg') {
            return asset('images') . '/placeholders/no-category-image.svg';
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage') . '/' . $path;
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('images') . '/placeholders/no-category-image.svg';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
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

    // Helpers
    public function getImageUrlAttribute(): string
    {
        $path = ltrim((string) $this->image, '/');

        if ($path === '' || $path === 'images/placeholders/no-category-image.svg') {
            return asset('images') . '/placeholders/no-category-image.svg';
        }

        // 1. Try public disk (storage/app/public)
        if (Storage::disk('public')->exists($path)) {
            return asset('storage') . '/' . $path;
        }

        // 2. Try direct public folder
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('images') . '/placeholders/no-category-image.svg';
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
}

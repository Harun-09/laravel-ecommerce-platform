<?php

namespace App\Services;

use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function search(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::with(['vendor', 'category', 'brand', 'primaryImage'])
            ->select('products.*')
            ->distinct()
            ->published()
            ->inStock();

        // Search term
        if (!empty($filters['q'])) {
            $query->search($filters['q']);
        }

        // Category filter
        if (!empty($filters['category'])) {
            $category = Category::where('slug', $filters['category'])->first();
            if ($category) {
                $categoryIds = [$category->id];
                // Include child categories
                $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Brand filter
        if (!empty($filters['brand'])) {
            $brands = is_array($filters['brand']) ? $filters['brand'] : [$filters['brand']];
            $query->whereHas('brand', fn($q) => $q->whereIn('slug', $brands));
        }

        // Price range
        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null);
        }

        // Vendor filter
        if (!empty($filters['vendor'])) {
            $query->whereHas('vendor', fn($q) => $q->where('slug', $filters['vendor']));
        }

        // Rating filter
        if (!empty($filters['rating'])) {
            $query->where('rating', '>=', (float) $filters['rating']);
        }

        // Featured filter
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        // Deal of the day filter (running flash sale products only)
        $dealFilter = (string) ($filters['deal'] ?? '');
        if ($dealFilter === 'today' || !empty($filters['deal_of_the_day'])) {
            $query->whereHas('flashSales', function (Builder $flashSaleQuery): void {
                $flashSaleQuery->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_low' => $query->orderBy('base_price', 'asc'),
            'price_high' => $query->orderBy('base_price', 'desc'),
            'popular' => $query->orderBy('sales_count', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return $query->paginate($perPage);
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        $candidatePoolSize = max(40, $limit * 8);

        $candidates = Product::with(['vendor', 'category.parent', 'primaryImage'])
            ->published()
            ->inStock()
            ->featured()
            ->orderByDesc('sales_count')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->limit($candidatePoolSize)
            ->get();

        return $this->pickDiverseProducts($candidates, $limit);
    }

    public function getNewArrivals(int $limit = 8)
    {
        $candidatePoolSize = max(40, $limit * 8);

        $candidates = Product::with(['vendor', 'category.parent', 'primaryImage'])
            ->published()
            ->inStock()
            ->orderBy('created_at', 'desc')
            ->limit($candidatePoolSize)
            ->get();

        return $this->pickDiverseProducts($candidates, $limit);
    }

    public function getBestSellers(int $limit = 8)
    {
        $candidatePoolSize = max(50, $limit * 10);

        $candidates = Product::with(['vendor', 'category.parent', 'primaryImage'])
            ->published()
            ->inStock()
            ->orderBy('sales_count', 'desc')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->limit($candidatePoolSize)
            ->get();

        return $this->pickDiverseProducts($candidates, $limit);
    }

    public function getRelatedProducts(Product $product, int $limit = 4)
    {
        return Product::with(['vendor', 'primaryImage'])
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                    ->orWhere('brand_id', $product->brand_id);
            })
            ->published()
            ->inStock()
            ->limit($limit)
            ->get();
    }

    public function getProductsByCategory(Category $category, array $filters = [], int $perPage = 20)
    {
        $categoryIds = [$category->id];
        $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());

        $query = Product::with(['vendor', 'category', 'brand', 'primaryImage'])
            ->select('products.*')
            ->distinct()
            ->whereIn('category_id', $categoryIds)
            ->published()
            ->inStock();

        if (!empty($filters['q'])) {
            $query->search((string) $filters['q']);
        }

        if (!empty($filters['brand'])) {
            $brands = is_array($filters['brand']) ? $filters['brand'] : [$filters['brand']];
            $query->whereHas('brand', fn(Builder $builder) => $builder->whereIn('slug', $brands));
        }

        if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            $query->priceRange(
                isset($filters['min_price']) ? (float) $filters['min_price'] : null,
                isset($filters['max_price']) ? (float) $filters['max_price'] : null
            );
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', '>=', (float) $filters['rating']);
        }

        $sort = (string) ($filters['sort'] ?? 'best_match');
        match ($sort) {
            'price_low' => $query->orderBy('base_price', 'asc'),
            'price_high' => $query->orderBy('base_price', 'desc'),
            'popular' => $query->orderBy('sales_count', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('featured', 'desc')->orderBy('sales_count', 'desc')->orderBy('created_at', 'desc'),
        };

        return $query->paginate($perPage);
    }

    public function getProductDetails(string $slug)
    {
        return Product::with([
            'vendor',
            'category',
            'brand',
            'images',
            'variations.attributeValues.attribute',
            'approvedReviews.user',
        ])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();
    }

    private function pickDiverseProducts(Collection $candidates, int $limit): Collection
    {
        $limit = max(1, $limit);
        if ($candidates->isEmpty()) {
            return collect();
        }

        $selected = collect();
        $usedImageSignatures = [];
        $parentCategoryCount = [];

        $passes = [
            ['max_per_parent' => 2, 'enforce_image_unique' => true],
            ['max_per_parent' => 3, 'enforce_image_unique' => true],
            ['max_per_parent' => PHP_INT_MAX, 'enforce_image_unique' => true],
            ['max_per_parent' => PHP_INT_MAX, 'enforce_image_unique' => false],
        ];

        foreach ($passes as $pass) {
            foreach ($candidates as $candidate) {
                if ($selected->count() >= $limit) {
                    break 2;
                }

                if ($selected->contains(fn(Product $product): bool => (int) $product->id === (int) $candidate->id)) {
                    continue;
                }

                $parentKey = $this->resolveParentCategoryKey($candidate);
                $maxPerParent = (int) ($pass['max_per_parent'] ?? PHP_INT_MAX);
                if (($parentCategoryCount[$parentKey] ?? 0) >= $maxPerParent) {
                    continue;
                }

                $signature = $this->resolveListingImageSignature($candidate);
                $enforceImageUnique = (bool) ($pass['enforce_image_unique'] ?? true);
                if ($enforceImageUnique && $signature !== '' && isset($usedImageSignatures[$signature])) {
                    continue;
                }

                $selected->push($candidate);
                $parentCategoryCount[$parentKey] = ($parentCategoryCount[$parentKey] ?? 0) + 1;

                if ($signature !== '') {
                    $usedImageSignatures[$signature] = true;
                }
            }
        }

        return $selected->values();
    }

    private function resolveParentCategoryKey(Product $product): string
    {
        $parentId = (int) ($product->category?->parent_id ?? 0);
        if ($parentId > 0) {
            return 'parent:' . $parentId;
        }

        $categoryId = (int) ($product->category_id ?? 0);
        if ($categoryId > 0) {
            return 'category:' . $categoryId;
        }

        return 'global';
    }

    private function resolveListingImageSignature(Product $product): string
    {
        $imageUrl = trim((string) $product->listing_image_url);
        if ($imageUrl === '') {
            $imageUrl = trim((string) $product->primary_image_url);
        }

        if ($imageUrl === '' || str_contains($imageUrl, '/images/no-product-image.svg')) {
            return '';
        }

        $urlPath = ltrim((string) parse_url($imageUrl, PHP_URL_PATH), '/');
        if ($urlPath === '') {
            return $imageUrl;
        }

        $storagePrefix = 'storage/';
        $storagePos = strpos($urlPath, $storagePrefix);
        if ($storagePos !== false) {
            return trim((string) substr($urlPath, $storagePos + strlen($storagePrefix)), '/');
        }

        return trim($urlPath, '/');
    }
}


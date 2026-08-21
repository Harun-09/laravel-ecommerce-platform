<?php

namespace Database\Seeders;

use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageGallerySeeder extends Seeder
{
    private const TARGET_IMAGES_PER_PRODUCT = 4;
    private const MAX_PATH_USES_PER_CATEGORY_SOFT = 2;
    private const MAX_PATH_USES_GLOBAL_SOFT = 4;
    private array $globalPathUsage = [];
    private array $categoryPathUsage = [];
    private array $lastAssignedPathByCategory = [];
    private array $recentAssignedPathsByCategory = [];
    private const RECENT_REPEAT_BLOCK_SIZE = 3;
    private const AUTO_PRIMARY_PATH_PREFIXES = [
        'products/openverse/',
        'products/generated/',
        'products/gallery/',
    ];

    public function run(): void
    {
        $this->globalPathUsage = [];
        $this->categoryPathUsage = [];
        $this->lastAssignedPathByCategory = [];
        $this->recentAssignedPathsByCategory = [];

        $sourceImagePool = $this->buildSourceImagePool();

        $processCollection = function (Collection $products) use ($sourceImagePool): void {
            foreach ($products as $product) {
                $this->syncPrimaryImage($product, $sourceImagePool);
                $this->normalizeGalleryForProduct($product);
            }
        };

        Product::query()
            ->with(['images', 'category.parent'])
            ->whereNotNull('category_id')
            ->orderBy('category_id')
            ->orderByDesc('featured')
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->chunk(200, $processCollection);

        Product::query()
            ->with(['images', 'category.parent'])
            ->whereNull('category_id')
            ->orderByDesc('featured')
            ->orderByDesc('sales_count')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->chunk(200, $processCollection);
    }

    private function syncPrimaryImage(Product $product, Collection $sourceImagePool): void
    {
        if ($sourceImagePool->isEmpty()) {
            return;
        }

        $images = $product->images
            ->sortBy([
                ['order', 'asc'],
                ['id', 'asc'],
            ])
            ->values();

        /** @var ProductImage|null $primary */
        $primary = $images->first(fn(ProductImage $image) => (bool) $image->is_primary)
            ?? $images->first();

        $currentPath = ltrim(trim((string) ($primary?->image ?? '')), '/');
        $hasManualPrimary = $currentPath !== '' && !$this->isAutoManagedPrimaryPath($currentPath);

        if ($hasManualPrimary) {
            return;
        }

        $selectedSourcePath = $this->pickBestSourceImagePath($product, $sourceImagePool);
        if ($selectedSourcePath === null || $selectedSourcePath === '') {
            return;
        }

        // Use the curated source image directly for primary to preserve realistic product photos.
        $selectedPath = $selectedSourcePath;

        if (!$primary) {
            ProductImage::query()->create([
                'product_id' => $product->id,
                'image' => $selectedPath,
                'alt_text' => $product->name . ' image 1',
                'order' => 0,
                'is_primary' => true,
            ]);

            $this->rememberAssignment($product, $selectedSourcePath);
            return;
        }

        $needsUpdate = $currentPath !== $selectedPath
            || !$primary->is_primary
            || (int) $primary->order !== 0
            || trim((string) $primary->alt_text) === '';

        if ($needsUpdate) {
            $primary->update([
                'image' => $selectedPath,
                'alt_text' => $product->name . ' image 1',
                'order' => 0,
                'is_primary' => true,
            ]);
        }

        $this->rememberAssignment($product, $selectedSourcePath);
    }

    private function isAutoManagedPrimaryPath(string $path): bool
    {
        $path = ltrim(trim($path), '/');
        if ($path === '') {
            return false;
        }

        foreach (self::AUTO_PRIMARY_PATH_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function rememberAssignment(Product $product, string $path): void
    {
        $path = ltrim(trim($path), '/');
        if ($path === '') {
            return;
        }

        $categoryKey = $this->buildCategoryUsageKey($product);

        $this->globalPathUsage[$path] = ($this->globalPathUsage[$path] ?? 0) + 1;
        $this->categoryPathUsage[$categoryKey][$path] = ($this->categoryPathUsage[$categoryKey][$path] ?? 0) + 1;
        $this->lastAssignedPathByCategory[$categoryKey] = $path;

        $recent = $this->recentAssignedPathsByCategory[$categoryKey] ?? [];
        $recent[] = $path;
        if (count($recent) > self::RECENT_REPEAT_BLOCK_SIZE) {
            $recent = array_slice($recent, -self::RECENT_REPEAT_BLOCK_SIZE);
        }
        $this->recentAssignedPathsByCategory[$categoryKey] = $recent;
    }

    private function buildCategoryUsageKey(Product $product): string
    {
        $parentCategoryId = (int) ($product->category?->parent_id ?? 0);
        if ($parentCategoryId > 0) {
            return 'parent:' . $parentCategoryId;
        }

        $categoryId = (int) ($product->category_id ?? 0);
        if ($categoryId > 0) {
            return 'category:' . $categoryId;
        }

        return 'global';
    }

    private function buildSourceImagePool(): Collection
    {
        $paths = collect(Storage::disk('public')->files('products/openverse'))
            ->map(fn(string $path) => ltrim(trim($path), '/'))
            ->filter(fn(string $path) => $path !== '' && Storage::disk('public')->exists($path))
            ->unique()
            ->values();

        if ($paths->isEmpty()) {
            return collect();
        }

        $sourceProductIds = $paths
            ->map(fn(string $path): int => $this->extractSourceProductIdFromPath($path))
            ->filter(fn(int $id): bool => $id > 0)
            ->unique()
            ->values();

        $sourceProductById = Product::query()
            ->with(['category.parent'])
            ->whereIn('id', $sourceProductIds->all())
            ->get()
            ->keyBy('id');

        return $paths
            ->values()
            ->map(function (string $path) use ($sourceProductById): array {
                $sourceProductId = $this->extractSourceProductIdFromPath($path);
                /** @var Product|null $sourceProduct */
                $sourceProduct = $sourceProductById->get($sourceProductId);

                $fileStem = (string) pathinfo($path, PATHINFO_FILENAME);
                $fileLabel = preg_replace('/^product-\d+-/', '', $fileStem) ?: $fileStem;
                $tokenSeed = trim(((string) ($sourceProduct?->name ?? '')) . ' ' . $fileLabel);

                return [
                    'path' => $path,
                    'category_id' => (int) ($sourceProduct?->category_id ?? 0),
                    'parent_category_id' => (int) ($sourceProduct?->category?->parent_id ?? 0),
                    'brand_id' => (int) ($sourceProduct?->brand_id ?? 0),
                    'source_product_id' => $sourceProductId,
                    'tokens' => $this->extractSearchTokens($tokenSeed),
                ];
            });
    }

    private function extractSourceProductIdFromPath(string $path): int
    {
        $filename = strtolower((string) pathinfo($path, PATHINFO_BASENAME));
        if (preg_match('/^product-(\d+)-/', $filename, $matches) === 1) {
            return (int) ($matches[1] ?? 0);
        }

        return 0;
    }

    private function pickBestSourceImagePath(Product $product, Collection $sourceImagePool): ?string
    {
        $targetTokens = $this->extractSearchTokens((string) $product->name);
        $categoryId = (int) ($product->category_id ?? 0);
        $parentCategoryId = (int) ($product->category?->parent_id ?? 0);
        $brandId = (int) ($product->brand_id ?? 0);

        $categoryKey = $this->buildCategoryUsageKey($product);

        $scored = $sourceImagePool
            ->map(function (array $source) use ($targetTokens, $categoryId, $parentCategoryId, $brandId, $categoryKey, $product): array {
                $sourceTokens = (array) ($source['tokens'] ?? []);
                $overlap = count(array_intersect($targetTokens, $sourceTokens));
                $path = (string) ($source['path'] ?? '');
                $globalUsage = (int) ($this->globalPathUsage[$path] ?? 0);
                $categoryUsage = (int) ($this->categoryPathUsage[$categoryKey][$path] ?? 0);
                $isRecentInCategory = (($this->lastAssignedPathByCategory[$categoryKey] ?? null) === $path);

                $score = 0;
                if ($categoryId > 0 && (int) ($source['category_id'] ?? 0) === $categoryId) {
                    $score += 90;
                }
                if ($parentCategoryId > 0 && (int) ($source['parent_category_id'] ?? 0) === $parentCategoryId) {
                    $score += 60;
                }
                if ($brandId > 0 && (int) ($source['brand_id'] ?? 0) === $brandId) {
                    $score += 25;
                }
                if ((int) ($source['source_product_id'] ?? 0) === (int) $product->id) {
                    $score += 500;
                }
                $score += $overlap * 52;
                if ($categoryUsage === 0) {
                    $score += 24;
                }
                if ($globalUsage === 0) {
                    $score += 8;
                }
                $score -= $globalUsage * 16;
                $score -= $categoryUsage * 44;
                if ($isRecentInCategory) {
                    $score -= 120;
                }

                return [
                    'path' => $path,
                    'score' => $score,
                    'overlap' => $overlap,
                    'global_usage' => $globalUsage,
                    'category_usage' => $categoryUsage,
                    'source_category_id' => (int) ($source['category_id'] ?? 0),
                    'source_parent_category_id' => (int) ($source['parent_category_id'] ?? 0),
                    'source_product_id' => (int) ($source['source_product_id'] ?? 0),
                ];
            })
            ->filter(fn(array $item): bool => $item['path'] !== '')
            ->values();

        if ($scored->isEmpty()) {
            return null;
        }

        $exactMatch = $scored
            ->first(fn(array $item): bool => (int) ($item['source_product_id'] ?? 0) === (int) $product->id);
        if (is_array($exactMatch) && ($exactMatch['path'] ?? '') !== '') {
            return (string) $exactMatch['path'];
        }

        if ($categoryId > 0) {
            $sameCategoryCandidates = $scored
                ->filter(fn(array $item): bool => (int) ($item['source_category_id'] ?? 0) === $categoryId)
                ->values();

            if ($sameCategoryCandidates->isNotEmpty()) {
                $scored = $sameCategoryCandidates;
            } elseif ($parentCategoryId > 0) {
                $sameParentCandidates = $scored
                    ->filter(fn(array $item): bool => (int) ($item['source_parent_category_id'] ?? 0) === $parentCategoryId)
                    ->values();

                if ($sameParentCandidates->isNotEmpty()) {
                    $scored = $sameParentCandidates;
                }
            }
        }

        $underSoftCap = $scored
            ->filter(function (array $item): bool {
                return (int) $item['category_usage'] < self::MAX_PATH_USES_PER_CATEGORY_SOFT
                    && (int) $item['global_usage'] < self::MAX_PATH_USES_GLOBAL_SOFT;
            })
            ->values();

        if ($underSoftCap->isNotEmpty()) {
            $scored = $underSoftCap;
        }

        $maxOverlap = (int) $scored->max('overlap');
        if ($maxOverlap >= 2) {
            $scored = $scored
                ->filter(fn(array $item): bool => (int) $item['overlap'] === $maxOverlap)
                ->values();
        }

        $maxScore = (int) $scored->max('score');
        $candidates = $scored
            ->filter(fn(array $item): bool => $item['score'] >= ($maxScore - 28))
            ->values();

        if ($candidates->isEmpty()) {
            return null;
        }

        $minCategoryUsage = (int) $candidates->min('category_usage');
        $candidates = $candidates
            ->filter(fn(array $item): bool => (int) $item['category_usage'] === $minCategoryUsage)
            ->values();

        $minGlobalUsage = (int) $candidates->min('global_usage');
        $candidates = $candidates
            ->filter(fn(array $item): bool => (int) $item['global_usage'] === $minGlobalUsage)
            ->values();

        $recentPaths = array_values(array_filter((array) ($this->recentAssignedPathsByCategory[$categoryKey] ?? [])));
        if (!empty($recentPaths) && $candidates->count() > 1) {
            $withoutRecent = $candidates
                ->reject(fn(array $item): bool => in_array((string) ($item['path'] ?? ''), $recentPaths, true))
                ->values();

            if ($withoutRecent->isNotEmpty()) {
                $candidates = $withoutRecent;
            }
        }

        $pickIndex = ((int) $product->id + count($targetTokens) + $minCategoryUsage + $minGlobalUsage) % max(1, $candidates->count());

        return (string) ($candidates->get($pickIndex)['path'] ?? '');
    }

    /**
     * @return array<int, string>
     */
    private function extractSearchTokens(string $value): array
    {
        $normalized = (string) Str::of($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->trim();

        if ($normalized === '') {
            return [];
        }

        $stopWords = [
            'the', 'and', 'for', 'with', 'set', 'pack', 'series', 'edition', 'model', 'size',
            'premium', 'classic', 'modern', 'smart', 'daily', 'pro', 'max', 'ultra',
            'product', 'products',
        ];

        $aliases = [
            'tee' => ['shirt'],
            'tshirt' => ['shirt'],
            'polo' => ['shirt'],
            'oxford' => ['shirt'],
            'flannel' => ['shirt'],
            'jacket' => ['outerwear'],
            'hoodie' => ['outerwear'],
            'blazer' => ['outerwear'],
            'coat' => ['outerwear'],
            'chino' => ['pant'],
            'chinos' => ['pant'],
            'trouser' => ['pant'],
            'trousers' => ['pant'],
            'jogger' => ['pant'],
            'joggers' => ['pant'],
            'jeans' => ['pant'],
            'denim' => ['pant'],
            'laptop' => ['computer'],
            'notebook' => ['book'],
            'printer' => ['office'],
            'rice' => ['grocery'],
        ];

        $tokens = collect(explode(' ', $normalized))
            ->map(fn(string $token) => trim($token))
            ->filter(fn(string $token): bool => $token !== '' && strlen($token) >= 3 && !in_array($token, $stopWords, true))
            ->unique()
            ->values()
            ->all();

        return collect($tokens)
            ->flatMap(function (string $token) use ($aliases): array {
                $expanded = [$token];
                foreach ((array) ($aliases[$token] ?? []) as $alias) {
                    $alias = trim((string) $alias);
                    if ($alias !== '') {
                        $expanded[] = $alias;
                    }
                }

                return $expanded;
            })
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeGalleryForProduct(Product $product): void
    {
        $images = $product->images()->orderBy('order')->orderBy('id')->get();
        if ($images->isEmpty()) {
            return;
        }

        $primary = $this->ensureSinglePrimaryImage($images);
        if (!$primary) {
            return;
        }

        $primaryPath = trim((string) $primary->image);
        if ($primaryPath === '') {
            return;
        }

        $variantPaths = collect($this->buildVariantImages((int) $product->id, $primaryPath))
            ->map(fn($path) => trim((string) $path))
            ->filter(fn(string $path) => $path !== '' && $path !== $primaryPath)
            ->unique()
            ->values()
            ->all();

        $primary->update([
            'order' => 0,
            'is_primary' => true,
            'alt_text' => $primary->alt_text ?: ($product->name . ' image 1'),
        ]);

        $secondaryImages = $product->images()
            ->where('id', '!=', $primary->id)
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->values();

        $secondaryTargetCount = self::TARGET_IMAGES_PER_PRODUCT - 1;
        $generatedPrefix = 'products/gallery/' . $product->id . '/';

        $existingRealSecondaryPaths = $secondaryImages
            ->map(fn(ProductImage $image) => ltrim(trim((string) $image->image), '/'))
            ->filter(function (string $path) use ($generatedPrefix, $primaryPath): bool {
                return $path !== '' && $path !== ltrim($primaryPath, '/') && !str_starts_with($path, $generatedPrefix);
            })
            ->unique()
            ->values();

        $selectedSecondaryPaths = $existingRealSecondaryPaths
            ->concat(
                collect($variantPaths)
                    ->map(fn(string $path) => ltrim(trim($path), '/'))
                    ->filter(fn(string $path) => $path !== '' && !$existingRealSecondaryPaths->contains($path))
            )
            ->take($secondaryTargetCount)
            ->values();

        for ($index = 0; $index < $selectedSecondaryPaths->count(); $index++) {
            $targetPath = (string) $selectedSecondaryPaths->get($index);
            $targetOrder = $index + 1;
            $targetAlt = $product->name . ' image ' . ($targetOrder + 1);

            /** @var ProductImage|null $existing */
            $existing = $secondaryImages->get($index);

            if ($existing) {
                $existing->update([
                    'image' => $targetPath,
                    'alt_text' => $targetAlt,
                    'order' => $targetOrder,
                    'is_primary' => false,
                ]);
                continue;
            }

            ProductImage::query()->create([
                'product_id' => $product->id,
                'image' => $targetPath,
                'alt_text' => $targetAlt,
                'order' => $targetOrder,
                'is_primary' => false,
            ]);
        }

        if ($secondaryImages->count() > $selectedSecondaryPaths->count()) {
            $overflowStartOrder = $selectedSecondaryPaths->count() + 1;
            $secondaryImages
                ->slice($selectedSecondaryPaths->count())
                ->values()
                ->each(function (ProductImage $image, int $offset) use ($overflowStartOrder): void {
                    $image->update([
                        'order' => $overflowStartOrder + $offset,
                        'is_primary' => false,
                    ]);
                });
        }
    }

    private function buildDedicatedPrimaryPath(Product $product, string $sourcePath): ?string
    {
        $sourcePath = ltrim(trim($sourcePath), '/');
        if ($sourcePath === '') {
            return null;
        }

        $absoluteSourcePath = storage_path('app/public/' . $sourcePath);
        if (!is_file($absoluteSourcePath)) {
            return null;
        }

        $source = $this->loadImageResource($absoluteSourcePath);
        if (!$source) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width < 1 || $height < 1) {
            imagedestroy($source);
            return null;
        }

        $seed = abs(crc32((string) $product->id . '|' . (string) $product->name));
        $jitter = static function (int $shift, float $range) use ($seed): float {
            $unit = (($seed >> $shift) & 0xff) / 255;
            return ($unit * 2 - 1) * $range;
        };

        $variant = $this->makeFocusCropVariant(
            $source,
            $width,
            $height,
            0.50 + $jitter(0, 0.18),
            0.50 + $jitter(8, 0.16),
            1.10 + $jitter(16, 0.45),
            6 + (int) round($jitter(24, 10)),
            -6 + (int) round($jitter(4, 10)),
        );

        imagedestroy($source);

        if (!$variant) {
            return null;
        }

        $this->applyPrimaryLabelOverlay($variant, $product);

        $relativeDir = 'products/generated/' . (int) $product->id;
        $absoluteDir = storage_path('app/public/' . $relativeDir);
        File::ensureDirectoryExists($absoluteDir);

        $relativePath = $relativeDir . '/01-primary.jpg';
        $absolutePath = storage_path('app/public/' . $relativePath);

        $saved = $this->saveAsJpeg($variant, $absolutePath, 90);
        imagedestroy($variant);

        return $saved ? $relativePath : null;
    }

    private function applyPrimaryLabelOverlay($image, Product $product): void
    {
        $width = imagesx($image);
        $height = imagesy($image);
        if ($width < 120 || $height < 90) {
            return;
        }

        $seed = abs(crc32((string) $product->id . '|' . (string) $product->name));

        $tintR = ((($seed >> 0) & 0x3f) - 31);
        $tintG = ((($seed >> 6) & 0x3f) - 31);
        $tintB = ((($seed >> 12) & 0x3f) - 31);
        imagefilter($image, IMG_FILTER_COLORIZE, $tintR, $tintG, $tintB);

        $bandHeight = max(36, (int) round($height * 0.28));
        $bandTop = max(0, $height - $bandHeight);
        $bandColor = imagecolorallocatealpha($image, 8, 12, 24, 38);
        imagefilledrectangle($image, 0, $bandTop, $width, $height, $bandColor);

        $tag = $this->buildProductVisualTag($product);
        $tagWidth = min($width - 14, max(84, 12 + (strlen($tag) * 9)));
        $tagHeight = 26;
        $tagX = 8;
        $tagY = 8;

        $tagR = 72 + (($seed >> 3) & 0x7f);
        $tagG = 72 + (($seed >> 10) & 0x7f);
        $tagB = 72 + (($seed >> 17) & 0x7f);
        $tagBg = imagecolorallocatealpha($image, $tagR, $tagG, $tagB, 18);
        imagefilledrectangle($image, $tagX, $tagY, $tagX + $tagWidth, $tagY + $tagHeight, $tagBg);

        $tagText = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 4, $tagX + 8, $tagY + 6, $tag, $tagText);

        $name = preg_replace('/\s+/', ' ', trim((string) $product->name)) ?: 'Product';
        $titleMaxChars = max(12, (int) floor(($width - 16) / 9));
        $lines = $this->splitLabelLines($name, $titleMaxChars, 2);

        $titleColor = imagecolorallocate($image, 247, 250, 255);
        $line1Y = $bandTop + 10;
        $line2Y = min($height - 16, $line1Y + 18);

        imagestring($image, 5, 8, $line1Y, $lines[0] ?? $name, $titleColor);
        if (!empty($lines[1])) {
            imagestring($image, 4, 8, $line2Y, $lines[1], $titleColor);
        }
    }

    /**
     * @return array<int, string>
     */
    private function splitLabelLines(string $label, int $maxCharsPerLine, int $maxLines = 2): array
    {
        $label = trim(preg_replace('/\s+/', ' ', $label) ?? '');
        if ($label === '') {
            return ['Product'];
        }

        $words = array_values(array_filter(explode(' ', $label), fn(string $word): bool => trim($word) !== ''));
        if (empty($words)) {
            return ['Product'];
        }

        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : ($current . ' ' . $word);
            if (strlen($candidate) <= $maxCharsPerLine) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $lines[] = substr($word, 0, max(1, $maxCharsPerLine));
                $current = '';
            }

            if (count($lines) >= $maxLines) {
                break;
            }
        }

        if (count($lines) < $maxLines && $current !== '') {
            $lines[] = $current;
        }

        $lines = array_slice($lines, 0, max(1, $maxLines));
        if (count($lines) === $maxLines) {
            $remaining = trim(implode(' ', array_slice($words, 0)));
            $rendered = trim(implode(' ', $lines));
            if (strlen($remaining) > strlen($rendered)) {
                $lastIndex = count($lines) - 1;
                $last = $lines[$lastIndex] ?? '';
                if (strlen($last) > max(4, $maxCharsPerLine - 3)) {
                    $last = substr($last, 0, max(1, $maxCharsPerLine - 3));
                }
                $lines[$lastIndex] = rtrim($last) . '...';
            }
        }

        return $lines;
    }

    private function buildProductVisualTag(Product $product): string
    {
        $name = strtolower((string) $product->name);
        $category = strtolower((string) ($product->category?->name ?? ''));
        $seed = $name . ' ' . $category;

        $rules = [
            'Serum' => ['serum', 'niacinamide', 'vitamin c'],
            'Face Wash' => ['face wash', 'cleanser', 'foaming'],
            'Cream' => ['cream', 'moisturizer', 'hydration'],
            'Mask' => ['mask', 'clay'],
            'Toner' => ['toner'],
            'Lotion' => ['lotion'],
            'Apparel' => ['shirt', 'hoodie', 'jacket', 'jeans', 'blazer', 'pant', 'trouser'],
            'Furniture' => ['chair', 'table', 'cabinet', 'shelf', 'desk', 'sofa', 'bed'],
            'Kitchen' => ['cookware', 'kettle', 'knife', 'pan', 'lunch', 'plate', 'chopping'],
            'Fitness' => ['dumbbell', 'yoga', 'gym', 'roller', 'band', 'kettlebell'],
            'Stationery' => ['pen', 'paper', 'notebook', 'folder', 'marker', 'stapler', 'calculator'],
            'Grocery' => ['rice', 'flour', 'lentil', 'honey', 'oil', 'oats', 'tea', 'sugar'],
        ];

        foreach ($rules as $label => $keywords) {
            foreach ($keywords as $keyword) {
                if ($this->matchesTagKeyword($seed, $keyword)) {
                    return $label;
                }
            }
        }

        $fallback = trim((string) ($product->category?->name ?? 'Product'));
        if ($fallback === '') {
            $fallback = 'Product';
        }

        return (string) Str::of($fallback)->limit(14, '');
    }

    private function matchesTagKeyword(string $haystack, string $keyword): bool
    {
        $haystack = strtolower(trim($haystack));
        $keyword = strtolower(trim($keyword));

        if ($haystack === '' || $keyword === '') {
            return false;
        }

        // Match by word boundary so terms like "desk" don't accidentally match "desktop".
        $pattern = '/\b' . preg_quote($keyword, '/') . '\b/u';
        return preg_match($pattern, $haystack) === 1;
    }

    private function ensureSinglePrimaryImage(Collection $images): ?ProductImage
    {
        /** @var ProductImage|null $primary */
        $primary = $images->first(fn(ProductImage $image) => (bool) $image->is_primary)
            ?? $images->first();

        if (!$primary) {
            return null;
        }

        ProductImage::query()
            ->where('product_id', $primary->product_id)
            ->where('id', '!=', $primary->id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        if (!$primary->is_primary) {
            $primary->update(['is_primary' => true]);
        }

        return $primary->fresh();
    }

    /**
     * @return array<int, string>
     */
    private function buildVariantImages(int $productId, string $primaryPath): array
    {
        $absolutePrimaryPath = storage_path('app/public/' . ltrim($primaryPath, '/'));
        if (!is_file($absolutePrimaryPath)) {
            return [];
        }

        $source = $this->loadImageResource($absolutePrimaryPath);
        if (!$source) {
            return [];
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width < 1 || $height < 1) {
            imagedestroy($source);
            return [];
        }

        $relativeDir = 'products/gallery/' . $productId;
        $absoluteDir = storage_path('app/public/' . $relativeDir);
        File::ensureDirectoryExists($absoluteDir);

        $seed = abs(crc32((string) $productId));
        $jitter = static function (int $shift, float $range) use ($seed): float {
            $unit = (($seed >> $shift) & 0xff) / 255;
            return ($unit * 2 - 1) * $range;
        };

        $variants = [
            [
                'file' => '02-focus-center.jpg',
                'resource' => $this->makeFocusCropVariant(
                    $source,
                    $width,
                    $height,
                    0.50 + $jitter(0, 0.12),
                    0.45 + $jitter(8, 0.10),
                    1.35 + $jitter(16, 0.30),
                    8 + (int) round($jitter(24, 8)),
                    -8 + (int) round($jitter(4, 8)),
                ),
            ],
            [
                'file' => '03-focus-left.jpg',
                'resource' => $this->makeFocusCropVariant(
                    $source,
                    $width,
                    $height,
                    0.25 + $jitter(2, 0.12),
                    0.35 + $jitter(10, 0.10),
                    1.75 + $jitter(18, 0.35),
                    12 + (int) round($jitter(26, 8)),
                    -14 + (int) round($jitter(6, 8)),
                ),
            ],
            [
                'file' => '04-focus-right.jpg',
                'resource' => $this->makeFocusCropVariant(
                    $source,
                    $width,
                    $height,
                    0.75 + $jitter(1, 0.12),
                    0.65 + $jitter(9, 0.10),
                    1.75 + $jitter(17, 0.35),
                    6 + (int) round($jitter(25, 8)),
                    -6 + (int) round($jitter(5, 8)),
                ),
            ],
        ];

        $paths = [];

        foreach ($variants as $variant) {
            $resource = $variant['resource'];
            if (!$resource) {
                continue;
            }

            $relativePath = $relativeDir . '/' . $variant['file'];
            $absolutePath = storage_path('app/public/' . $relativePath);

            if ($this->saveAsJpeg($resource, $absolutePath, 90)) {
                $paths[] = $relativePath;
            }

            imagedestroy($resource);
        }

        imagedestroy($source);

        return $paths;
    }

    private function loadImageResource(string $absolutePath)
    {
        $binary = @file_get_contents($absolutePath);
        if ($binary === false) {
            return null;
        }

        $image = @imagecreatefromstring($binary);
        if (!$image) {
            return null;
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function makeFocusCropVariant(
        $source,
        int $width,
        int $height,
        float $anchorX,
        float $anchorY,
        float $zoom,
        int $brightness = 0,
        int $contrast = 0,
    )
    {
        $canvas = $this->createCanvas($width, $height);
        if (!$canvas) {
            return null;
        }

        $zoom = max(1.0, $zoom);
        $cropWidth = max(1, (int) floor($width / $zoom));
        $cropHeight = max(1, (int) floor($height / $zoom));

        $maxX = max(0, $width - $cropWidth);
        $maxY = max(0, $height - $cropHeight);
        $srcX = (int) round($maxX * max(0, min(1, $anchorX)));
        $srcY = (int) round($maxY * max(0, min(1, $anchorY)));

        imagecopyresampled($canvas, $source, 0, 0, $srcX, $srcY, $width, $height, $cropWidth, $cropHeight);

        if ($brightness !== 0) {
            imagefilter($canvas, IMG_FILTER_BRIGHTNESS, $brightness);
        }
        if ($contrast !== 0) {
            imagefilter($canvas, IMG_FILTER_CONTRAST, $contrast);
        }

        return $canvas;
    }

    private function createCanvas(int $width, int $height)
    {
        $canvas = imagecreatetruecolor(max(1, $width), max(1, $height));
        if (!$canvas) {
            return null;
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $white);

        return $canvas;
    }

    private function saveAsJpeg($resource, string $path, int $quality = 90): bool
    {
        return (bool) @imagejpeg($resource, $path, $quality);
    }
}

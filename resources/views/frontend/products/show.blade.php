@extends('layouts.app')

@push('styles')
<style>
    .ai-assistant { border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; background: #f8fafc; margin-top: 24px; }
    .ai-kicker { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #1e293b; margin-bottom: 16px; font-size: 1.1rem; }
    .ai-kicker i { color: #6366f1; font-size: 1.2rem; }
    .ai-suggestions { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .ai-chip { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 6px 14px; font-size: 0.875rem; color: #475569; cursor: pointer; transition: all 0.2s; }
    .ai-chip:hover { border-color: #6366f1; color: #6366f1; background: #f5f3ff; }
    .ai-ask-row { display: flex; gap: 8px; margin-bottom: 16px; }
    .ai-ask-row input { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; outline: none; transition: border-color 0.2s; }
    .ai-ask-row input:focus { border-color: #6366f1; }
    .ai-ask-row button { background: #6366f1; color: #fff; border: none; border-radius: 8px; padding: 0 20px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .ai-ask-row button:hover { background: #4f46e5; }
    .ai-answer { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; position: relative; }
    .ai-answer-title { font-weight: 600; color: #6366f1; margin-bottom: 8px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }
    #ai-answer-text { color: #334155; line-height: 1.6; font-size: 1rem; }
    .ai-typing { color: #94a3b8; font-style: italic; display: flex; align-items: center; gap: 8px; }
    .ai-typing::after { content: ''; width: 4px; height: 4px; border-radius: 50%; background: #94a3b8; animation: ai-dot 1.4s infinite linear; box-shadow: 8px 0 #94a3b8, 16px 0 #94a3b8; margin-left: 4px; }
    @keyframes ai-dot { 0% { opacity: 0.2; } 20% { opacity: 1; } 100% { opacity: 0.2; } }
    .ai-answer.hidden { display: none; }
</style>
@endpush

@section('content')
@php
    $fallbackImage = asset('images') . '/placeholders/no-product-image.svg';
    $variations = $product->variations->where('is_active', true)->values();
    $images = $product->images->sortBy('order')->values();
    $gallery = $images->map(fn($img) => [
        'url' => $img->image_url,
        'alt' => $img->alt_text ?: $product->name,
    ]);

    $gallery = $gallery->concat(
        $variations
            ->map(function ($variation) use ($product) {
                $url = (string) ($variation->image_url ?? '');
                if ($url === '') {
                    return null;
                }

                $option = trim((string) ($variation->variation_name ?: $variation->sku ?: ''));
                return [
                    'url' => $url,
                    'alt' => $option !== '' ? ($product->name . ' - ' . $option) : $product->name,
                ];
            })
            ->filter()
    )->push([
        'url' => (string) $product->primary_image_url,
        'alt' => $product->name,
    ])->filter(fn($img) => ($img['url'] ?? '') !== '' && $img['url'] !== $fallbackImage)
        ->unique('url')
        ->values();

    if ($gallery->isEmpty()) {
        $gallery = collect([['url' => $fallbackImage, 'alt' => $product->name]]);
    }

    $defaultVariation = $variations->firstWhere('is_default', true) ?? $variations->first();

    $attrGroups = [];
    foreach ($variations as $variation) {
        foreach ($variation->attributeValues as $val) {
            $attrId = (int) ($val->pivot->attribute_id ?? $val->attribute?->id ?? 0);
            if (!$attrId) continue;
            $attrGroups[$attrId]['id'] = $attrId;
            $attrGroups[$attrId]['name'] = $val->attribute?->name ?? 'Option';
            $attrGroups[$attrId]['values'][$val->id] = ['id' => (int)$val->id, 'label' => (string)$val->value];
        }
    }
    $attrGroups = collect($attrGroups)->map(function ($g) {
        $g['values'] = array_values($g['values'] ?? []);
        return $g;
    })->values();

    $colorNameMap = [
        'black' => '#111827',
        'white' => '#ffffff',
        'off white' => '#f8fafc',
        'gray' => '#9ca3af',
        'grey' => '#9ca3af',
        'silver' => '#cbd5e1',
        'red' => '#dc2626',
        'maroon' => '#7f1d1d',
        'orange' => '#f97316',
        'yellow' => '#facc15',
        'gold' => '#ca8a04',
        'green' => '#16a34a',
        'olive' => '#4d7c0f',
        'teal' => '#0f766e',
        'blue' => '#2563eb',
        'navy' => '#1e3a8a',
        'sky blue' => '#38bdf8',
        'purple' => '#7c3aed',
        'pink' => '#ec4899',
        'brown' => '#92400e',
        'beige' => '#d6d3c4',
        'cream' => '#fef9c3',
    ];
    $resolveSwatchColor = function (string $label) use ($colorNameMap): string {
        $value = trim($label);
        if ($value === '') {
            return '#94a3b8';
        }

        if (preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
            return $value;
        }

        if (preg_match('/^(?:rgb|hsl)a?\([^)]+\)$/i', $value)) {
            return $value;
        }

        $normalized = preg_replace('/\s+/', ' ', strtolower($value));
        return $colorNameMap[$normalized] ?? '#94a3b8';
    };

    $colorAttrGroup = $attrGroups->first(function ($group) {
        $name = strtolower((string) ($group['name'] ?? ''));
        return str_contains($name, 'color') || str_contains($name, 'colour');
    });
    $optionGroups = $attrGroups;
    if ($colorAttrGroup) {
        $optionGroups = $attrGroups
            ->reject(fn($group) => (int) $group['id'] === (int) $colorAttrGroup['id'])
            ->values();
    }

    $valueToAttr = [];
    foreach ($attrGroups as $group) foreach ($group['values'] as $v) $valueToAttr[$v['id']] = $group['id'];

    $defaultSelection = [];
    if ($defaultVariation) {
        foreach ($defaultVariation->attributeValues as $val) {
            $attrId = (int) ($val->pivot->attribute_id ?? $val->attribute?->id ?? 0);
            if ($attrId) $defaultSelection[$attrId] = (int)$val->id;
        }
    }
    $selectedColorLabel = null;
    if ($colorAttrGroup) {
        $selectedColorValueId = (int) ($defaultSelection[$colorAttrGroup['id']] ?? 0);
        foreach ($colorAttrGroup['values'] as $value) {
            if ((int) $value['id'] === $selectedColorValueId) {
                $selectedColorLabel = (string) $value['label'];
                break;
            }
        }
    }

    $variationPayload = $variations->map(fn($v) => [
        'id' => (int) $v->id,
        'sku' => (string) ($v->sku ?: ''),
        'price' => (float) ($v->price ?? $product->price),
        'compare_price' => $v->compare_price !== null ? (float) $v->compare_price : ($product->compare_price !== null ? (float) $product->compare_price : null),
        'quantity' => (int) $v->quantity,
        'image_url' => $v->image_url,
        'attribute_value_ids' => $v->attributeValues->pluck('id')->map(fn($id) => (int)$id)->values()->all(),
    ])->values()->all();

    $reviews = $product->approvedReviews->sortByDesc('created_at')->values();
    $reviewCount = $reviews->count();
    $existingReview = $existingUserReview ?? null;
    $rating = (float) $product->rating;
    $short = trim(strip_tags((string)$product->short_description));
    $desc = trim(strip_tags((string)$product->description));
    $descriptionHtml = trim((string) $product->description);
    $parts = $short !== '' ? preg_split('/[\r\n|]+/', $short) : preg_split('/(?<=[.!?])\s+/', $desc);
    $highlights = collect($parts ?: [])->map(fn($p) => trim((string)$p))->filter()->unique()->take(6)->values();
    if ($highlights->isEmpty()) $highlights = collect(['Fast delivery', 'Secure checkout', 'Genuine product']);

    $dimensions = ($product->length && $product->width && $product->height)
        ? $product->length . ' x ' . $product->width . ' x ' . $product->height . ' ' . $product->dimension_unit
        : 'N/A';
    $specs = [
        ['Brand', $product->brand?->name ?? 'N/A'],
        ['Category', $product->category->name ?? 'N/A'],
        ['Vendor', $product->vendor->shop_name ?? 'NovaMart'],
        ['SKU', $product->sku ?: 'N/A'],
        ['Weight', $product->weight ? $product->weight . ' ' . $product->weight_unit : 'N/A'],
        ['Dimensions', $dimensions],
    ];
    $quickSpecs = collect($specs)->take(4)
        ->push(['Views', number_format((int) $product->views)])
        ->push(['Sold', number_format((int) $product->sales_count)])
        ->values();
    $promoTags = collect(explode(',', (string) $product->meta_keywords))
        ->map(fn($tag) => trim((string) $tag))
        ->filter()
        ->take(6)
        ->values();
    if ($promoTags->isEmpty()) {
        $promoTags = collect(['Secure checkout', 'Fast shipping', 'Authentic product']);
    }
    $monthlyFinance = store_money(round((float) $product->final_price / 12, 2));
    $installmentFinance = store_money(round((float) $product->final_price / 4, 2));
    $vendor = $product->vendor;
    $sellerRating = number_format((float) ($vendor?->rating ?? 0), 1);
    $sellerReviews = number_format((int) ($vendor?->total_reviews ?? 0));
    $sellerOrders = number_format((int) ($vendor?->total_orders ?? 0));
    $sellerLocation = collect([(string) ($vendor?->city ?? ''), (string) ($vendor?->country ?? '')])
        ->filter(fn($value) => trim($value) !== '')
        ->implode(', ');
    if ($sellerLocation === '') {
        $sellerLocation = 'Bangladesh';
    }
    $storeFollowerCountDisplay = number_format((int) ($storeFollowerCount ?? 0));
    $questionCount = (int) ($questionCount ?? 0);
    $answeredQuestionCount = (int) ($answeredQuestionCount ?? 0);
    $questions = $questions ?? collect();
    $priceHistory = $priceHistory ?? collect();
    $relatedProducts = $relatedProducts ?? collect();
    $relatedByCategoryProducts = $relatedByCategoryProducts ?? collect();
    $sellerMoreProducts = $sellerMoreProducts ?? collect();
    $compareProducts = $compareProducts ?? collect();
    $moreBuyingOptions = $moreBuyingOptions ?? collect();
    $deliveryStart = now()->addDays(5);
    $deliveryEnd = now()->addDays(10);
    $deliveryWindow = $deliveryStart->format('M d')
        . ($deliveryStart->isSameMonth($deliveryEnd) ? ' - ' . $deliveryEnd->format('d') : ' - ' . $deliveryEnd->format('M d'));
    $shareUrl = route('products.show', $product->slug);
    $likeCount = (int) $product->wishlists()->count();
    $likeCountDisplay = $likeCount >= 1000
        ? rtrim(rtrim(number_format($likeCount / 1000, 1), '0'), '.') . 'K'
        : (string) $likeCount;
    $currentProductId = (int) $product->id;
    $currentCategoryId = (int) ($product->category_id ?? 0);
    $currentBrandId = (int) ($product->brand_id ?? 0);
    $currentParentCategoryId = (int) ($product->category?->parent_id ?: $currentCategoryId);
    $resolveParentCategoryId = function ($item): int {
        $parentId = (int) ($item->category?->parent_id ?? 0);
        if ($parentId > 0) {
            return $parentId;
        }

        return (int) ($item->category_id ?? 0);
    };
    $matchesRelatedContext = function ($item) use (
        $currentCategoryId,
        $currentBrandId,
        $currentParentCategoryId,
        $resolveParentCategoryId
    ): bool {
        $itemCategoryId = (int) ($item->category_id ?? 0);
        $itemBrandId = (int) ($item->brand_id ?? 0);
        $itemParentCategoryId = $resolveParentCategoryId($item);

        if ($currentCategoryId > 0 && $itemCategoryId === $currentCategoryId) {
            return true;
        }

        if ($currentParentCategoryId > 0 && $itemParentCategoryId === $currentParentCategoryId) {
            return true;
        }

        if (
            $currentBrandId > 0
            && $itemBrandId === $currentBrandId
            && $itemParentCategoryId === $currentParentCategoryId
        ) {
            return true;
        }

        return false;
    };

    $allRecommendationPool = collect()
        ->merge($relatedByCategoryProducts)
        ->merge($relatedProducts)
        ->merge($sellerMoreProducts)
        ->merge($compareProducts)
        ->merge($moreBuyingOptions)
        ->filter()
        ->reject(fn($item) => (int) ($item->id ?? 0) === $currentProductId)
        ->unique('id')
        ->values();

    $takeUniqueProducts = function ($source, int $limit, bool $onlyContextual = false) use ($currentProductId, $matchesRelatedContext) {
        $items = collect($source)
            ->filter()
            ->reject(fn($item) => (int) ($item->id ?? 0) === $currentProductId)
            ->unique('id');

        if ($onlyContextual) {
            $items = $items->filter($matchesRelatedContext);
        }

        return $items->take($limit)->values();
    };

    $contextRelatedSeed = collect()
        ->merge($relatedByCategoryProducts)
        ->merge($relatedProducts)
        ->merge($allRecommendationPool)
        ->filter($matchesRelatedContext);

    $similarSeed = collect()
        ->merge($relatedByCategoryProducts)
        ->merge($relatedProducts)
        ->merge($allRecommendationPool)
        ->filter(fn($item) => $currentCategoryId > 0 && (int) ($item->category_id ?? 0) === $currentCategoryId);

    if ($similarSeed->isEmpty()) {
        $similarSeed = $contextRelatedSeed;
    }

    $similarProducts = $takeUniqueProducts($similarSeed, 8);
    if ($similarProducts->count() < 8) {
        $similarProducts = $takeUniqueProducts($similarSeed->merge($contextRelatedSeed), 8);
    }

    $relatedSeed = $contextRelatedSeed;

    $relatedTopicProducts = $takeUniqueProducts($relatedSeed, 12);

    $comparisonSeed = collect()
        ->merge($compareProducts)
        ->merge($relatedByCategoryProducts)
        ->merge($relatedProducts)
        ->merge($allRecommendationPool)
        ->filter($matchesRelatedContext);

    $comparisonProducts = collect([$product])
        ->merge($takeUniqueProducts($comparisonSeed, 5))
        ->unique('id')
        ->take(6)
        ->values();

    $sellerSeed = collect()->merge($sellerMoreProducts);
    $sellerShowcaseProducts = $takeUniqueProducts($sellerSeed, 9);

    $buyingSeed = collect($moreBuyingOptions)
        ->reject(fn($item) => (int) ($item->id ?? 0) === $currentProductId)
        ->merge($allRecommendationPool)
        ->filter($matchesRelatedContext);

    $buyingUniqueProducts = $takeUniqueProducts($buyingSeed, 7);
    $moreBuyingOptions = collect([$product])->merge($buyingUniqueProducts)->unique('id')->take(8)->values();

    $relatedTopics = collect(['All'])
        ->merge($relatedTopicProducts->pluck('brand.name')->filter()->unique())
        ->merge($relatedTopicProducts->pluck('category.name')->filter()->unique())
        ->unique()
        ->take(8)
        ->values();

    $sellerCategoryLinks = $sellerShowcaseProducts
        ->pluck('category')
        ->filter()
        ->unique('id')
        ->take(8)
        ->values();

    $buyingOfferRows = $moreBuyingOptions
        ->map(function ($item, $index) use ($product) {
            $etaRange = match ((int) $index) {
                0 => '3-7 days',
                1 => '4-9 days',
                2 => '5-11 days',
                default => '4-10 days',
            };

            $deliveryCopy = $index === 0
                ? 'Free shipping'
                : 'Shipping may vary by location';

            return [
                'item' => $item,
                'is_current' => (int) $item->id === (int) $product->id,
                'condition' => 'New',
                'delivery' => $deliveryCopy,
                'eta' => $etaRange,
            ];
        })
        ->take(8)
        ->values();
    $buyingOptionCount = $buyingOfferRows->count();
    $lowestBuyingPrice = $buyingOfferRows
        ->map(fn($row) => (float) ($row['item']->final_price ?? 0))
        ->filter(fn($price) => $price > 0)
        ->min();
    $extractByRegex = function (string $source, array $patterns, string $default = 'N/A'): string {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $source, $matches)) {
                return trim((string) ($matches[1] ?? $matches[0] ?? $default));
            }
        }

        return $default;
    };

    $comparisonRowLabels = [
        'price' => 'Price',
        'rating' => 'Rating',
        'sold_by' => 'Sold By',
        'brand' => 'Brand',
        'series' => 'Series',
        'gpu' => 'GPU',
        'directx' => 'DirectX',
        'model' => 'Model',
        'memory_size' => 'Memory Size',
        'memory_interface' => 'Memory Interface',
        'memory_type' => 'Memory Type',
        'interface' => 'Interface',
    ];

    $comparisonColumns = $comparisonProducts
        ->map(function ($item) use ($extractByRegex, $product) {
            $brandName = trim((string) ($item->brand?->name ?? ''));
            $nameText = trim((string) ($item->name ?? ''));
            $descriptionText = trim(strip_tags((string) ($item->short_description ?? '') . ' ' . (string) ($item->description ?? '')));
            $specSource = trim($nameText . ' ' . $descriptionText);

            $series = 'N/A';
            if ($brandName !== '') {
                $seriesSeed = trim((string) preg_replace('/\b' . preg_quote($brandName, '/') . '\b/i', '', $nameText));
                $series = collect(preg_split('/\s+/', $seriesSeed) ?: [])
                    ->filter()
                    ->take(2)
                    ->implode(' ');
            }
            if ($series === '') {
                $series = 'N/A';
            }

            $gpu = $extractByRegex($specSource, [
                '/(Radeon\s+RX\s*\d{3,4}\s*(?:XT|XTX|GRE)?)/i',
                '/(GeForce\s+RTX\s*\d{3,4}\s*(?:Ti|SUPER|Ti SUPER)?)/i',
                '/(RTX\s*\d{3,4}\s*(?:Ti|SUPER|Ti SUPER)?)/i',
                '/(GTX\s*\d{3,4}\s*(?:Ti)?)/i',
            ]);
            $directx = $extractByRegex($specSource, [
                '/(DirectX\s*\d{1,2}(?:\s*Ultimate)?)/i',
            ]);
            $memorySize = $extractByRegex($specSource, [
                '/(\d+\s*GB)/i',
            ]);
            $memoryInterface = $extractByRegex($specSource, [
                '/(\d{2,3}\s*-\s*Bit)/i',
                '/(\d{2,3}\s*Bit)/i',
            ]);
            $memoryType = strtoupper($extractByRegex($specSource, [
                '/(GDDR[0-9X]+|DDR[0-9X]+|HBM[0-9E]*)/i',
            ]));
            $interface = $extractByRegex($specSource, [
                '/(PCI(?:e| Express)?\s*[\d.]+\s*x\d+)/i',
                '/(PCI(?:e| Express)?\s*[\d.]+)/i',
            ]);

            return [
                'product' => $item,
                'is_current' => (int) $item->id === (int) $product->id,
                'is_best_seller' => (int) ($item->sales_count ?? 0) >= 50,
                'rows' => [
                    'price' => store_money((float) $item->final_price),
                    'rating' => (float) ($item->rating ?? 0),
                    'reviews_count' => (int) ($item->reviews_count ?? 0),
                    'sold_by' => $item->vendor?->shop_name ?? 'NovaMart',
                    'brand' => $brandName !== '' ? $brandName : 'N/A',
                    'series' => $series,
                    'gpu' => $gpu,
                    'directx' => $directx,
                    'model' => $item->sku ?: 'N/A',
                    'memory_size' => $memorySize,
                    'memory_interface' => $memoryInterface,
                    'memory_type' => $memoryType,
                    'interface' => $interface,
                ],
            ];
        })
        ->values();
    $descriptionParagraphs = collect();
    if ($short !== '') {
        $descriptionParagraphs->push($short);
    }

    if ($desc !== '') {
        $descriptionParts = collect(preg_split('/\r\n|\r|\n/', $desc) ?: [])
            ->map(fn($part) => trim((string) $part))
            ->filter()
            ->values();

        if ($descriptionParts->isEmpty()) {
            $descriptionParts = collect(preg_split('/(?<=[.!?])\s+/', $desc) ?: [])
                ->map(fn($part) => trim((string) $part))
                ->filter()
                ->values();
        }

        foreach ($descriptionParts as $part) {
            if ($part === $short) {
                continue;
            }

            $descriptionParagraphs->push($part);
            if ($descriptionParagraphs->count() >= 4) {
                break;
            }
        }
    }

    if ($descriptionParagraphs->isEmpty()) {
        $descriptionParagraphs = collect([
            $product->name . ' is selected for reliable quality, comfort, and daily usability.',
            'This product is sold by ' . ($vendor?->shop_name ?? 'NovaMart') . ' with secure checkout and delivery support.',
            'Review the full details below to confirm fit, specifications, and shipping before placing your order.',
        ]);
    }

    $availabilityText = !$product->track_quantity
        ? 'In stock (inventory not limited)'
        : ($product->allow_backorder
            ? 'Backorder available'
            : ((int) $product->quantity > 0 ? 'In stock (' . (int) $product->quantity . ')' : 'Out of stock'));

    $optionSummary = $attrGroups->map(function ($group) {
        $valueText = collect($group['values'] ?? [])
            ->pluck('label')
            ->map(fn($value) => trim((string) $value))
            ->filter()
            ->implode(', ');

        return [
            'label' => (string) ($group['name'] ?? 'Option'),
            'value' => $valueText,
        ];
    })->filter(fn($row) => $row['value'] !== '')->values();

    $hasComparePrice = $product->compare_price !== null && (float) $product->compare_price > (float) $product->price;
    $detailFacts = collect([
        ['Product Name', $product->name],
        ['Brand', $product->brand?->name ?? 'N/A'],
        ['Category', $product->category->name ?? 'N/A'],
        ['SKU', $product->sku ?: 'N/A'],
        ['Current Price', store_money($product->final_price)],
        ['Regular Price', $hasComparePrice ? store_money($product->compare_price) : 'N/A'],
        ['Discount', $product->discount_percentage ? $product->discount_percentage . '% OFF' : 'No active discount'],
        ['Availability', $availabilityText],
        ['Total Variations', $variations->isNotEmpty() ? (string) $variations->count() : 'No variation'],
        ['Rating', number_format($rating, 1) . ' / 5'],
        ['Total Reviews', number_format($reviewCount)],
        ['Total Views', number_format((int) $product->views)],
        ['Total Sold', number_format((int) $product->sales_count)],
        ['Weight', $product->weight ? $product->weight . ' ' . $product->weight_unit : 'N/A'],
        ['Dimensions', $dimensions],
        ['Delivery Window', $deliveryWindow],
        ['Seller', $vendor?->shop_name ?? 'NovaMart'],
        ['Ships From', $sellerLocation],
    ])->values();
    $aiSuggestedQuestions = collect([
        'What are the key features of this product?',
        'Is this product in stock and how fast is delivery?',
        'What should I check before buying this product?',
    ])->values();
    $aiKnowledgeText = collect([
        $product->name,
        $short,
        $desc,
        $highlights->implode('. '),
    ])->map(fn($value) => trim((string) $value))
        ->filter(fn($value) => $value !== '')
        ->implode(' ');

    $productPageConfig = [
        'data' => [
            'id' => (int) $product->id,
            'price' => (float) $product->price,
            'compare' => $product->compare_price !== null ? (float) $product->compare_price : null,
            'sku' => (string) ($product->sku ?: ''),
            'qty' => (int) $product->quantity,
            'track' => (bool) $product->track_quantity,
            'backorder' => (bool) $product->allow_backorder,
        ],
        'variations' => $variationPayload,
        'valueToAttr' => $valueToAttr,
        'defaultSelection' => $defaultSelection,
        'endpoints' => [
            'addToCart' => route('cart.add'),
            'cartIndex' => route('cart.index'),
            'followStore' => $vendor ? route('vendors.follow.toggle', $vendor->id) : null,
            'dealsSubscribe' => route('deals.subscribe'),
            'aiAssistantQuery' => route('ai-assistant.query'),
        ],
        'aiAssistant' => [
            'productName' => (string) $product->name,
            'knowledgeText' => $aiKnowledgeText,
            'highlights' => $highlights->values()->all(),
            'specs' => [
                'brand' => $product->brand?->name ?? 'N/A',
                'category' => $product->category->name ?? 'N/A',
                'seller' => $vendor?->shop_name ?? 'NovaMart',
                'sku' => $product->sku ?: 'N/A',
                'weight' => $product->weight ? $product->weight . ' ' . $product->weight_unit : 'N/A',
                'dimensions' => $dimensions,
                'deliveryWindow' => $deliveryWindow,
            ],
            'stats' => [
                'rating' => (float) $rating,
                'reviewCount' => (int) $reviewCount,
                'soldCount' => (int) $product->sales_count,
            ],
        ],
    ];
@endphp

<div class="container section pdp">
    <nav class="pdp-breadcrumb">
        <a href="{{ route('home') }}">Home</a><span>/</span>
        <a href="{{ route('products.index') }}">Products</a>
        @if($product->category)<span>/</span><a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>@endif
    </nav>

    <div class="pdp-grid">
        <div class="pdp-main">
            <section class="card pdp-gallery">
                <div class="thumbs">
                    @foreach($gallery as $i => $img)
                        <button type="button" class="thumb {{ $i === 0 ? 'is-active' : '' }}" data-thumb data-image="{{ $img['url'] }}">
                            <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-product-image.svg';">
                        </button>
                    @endforeach
                </div>
                <div class="hero">
                    @if($product->discount_percentage)<span class="badge">-{{ $product->discount_percentage }}%</span>@endif
                    <img id="main-image" src="{{ $gallery->first()['url'] }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-product-image.svg';">
                </div>
            </section>

            <section class="pdp-info">
                <div class="store-row">
                    @if($product->brand)
                        <a href="{{ route('products.search', ['q' => $product->brand?->name]) }}">Shop all {{ $product->brand?->name }} products</a>
                        <span>|</span>
                    @endif
                    <span>Sold by {{ $vendor?->shop_name ?? 'NovaMart' }}</span>
                    <button type="button" class="store-follow-btn {{ $isFollowingStore ? 'is-following' : '' }}"
                        id="follow-store-btn" data-vendor-id="{{ $vendor?->id }}"
                        aria-pressed="{{ $isFollowingStore ? 'true' : 'false' }}">
                        {{ $isFollowingStore ? 'Following' : 'Follow' }}
                    </button>
                    <small><span id="store-follower-count">{{ $storeFollowerCountDisplay }}</span> followers</small>
                </div>
                <h1>{{ $product->name }}</h1>
                <div class="meta">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" style="color: {{ $rating >= $i ? '#f59e0b' : '#cbd5e1' }}"></i>
                        @endfor
                    </div>
                    <span>{{ number_format($rating, 1) }}</span>
                    <button type="button" class="link-btn" data-open-reviews>{{ $reviewCount }} reviews</button>
                    <button type="button" class="link-btn" data-open-qa>{{ $questionCount }} questions</button>
                    <span>{{ $answeredQuestionCount }} answered</span>
                </div>
                <p class="sku-line">SKU: <strong id="sku-node">{{ $product->sku ?: 'N/A' }}</strong></p>
                @if($short !== '')<p class="short">{{ $short }}</p>@endif

                <div class="promo-tags">
                    @foreach($promoTags as $tag)
                        <span class="promo-tag">{{ $tag }}</span>
                    @endforeach
                </div>

                @if($attrGroups->isNotEmpty())
                    <div class="variants">
                        @if($colorAttrGroup)
                            <div class="variant-group color-group">
                                <p>Color: <strong id="selected-color-node">{{ $selectedColorLabel ?: 'Not selected' }}</strong></p>
                                <div class="variant-options color-options">
                                    @foreach($colorAttrGroup['values'] as $value)
                                        @php
                                            $swatchColor = $resolveSwatchColor((string) $value['label']);
                                            $isLightSwatch = in_array(strtolower($swatchColor), ['#fff', '#ffffff', 'white', '#f8fafc', '#f1f5f9'], true);
                                        @endphp
                                        <button type="button" class="variant-btn color-swatch {{ $isLightSwatch ? 'light' : '' }}"
                                            style="--swatch-color: {{ $swatchColor }};" data-option
                                            data-attr-id="{{ $colorAttrGroup['id'] }}" data-value-id="{{ $value['id'] }}"
                                            aria-label="Color {{ $value['label'] }}">
                                            <span class="swatch-dot" aria-hidden="true"></span>
                                            <span class="swatch-label">{{ $value['label'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @foreach($optionGroups as $group)
                            <div class="variant-group">
                                <p>{{ $group['name'] }}</p>
                                <div class="variant-options">
                                    @foreach($group['values'] as $value)
                                        <button type="button" class="variant-btn" data-option data-attr-id="{{ $group['id'] }}" data-value-id="{{ $value['id'] }}">{{ $value['label'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="highlights">
                    <h2>Highlights</h2>
                    <ul>@foreach($highlights as $item)<li>{{ $item }}</li>@endforeach</ul>
                </div>

                <div class="quick-spec-grid">
                    @foreach($quickSpecs as $row)
                        <div class="quick-spec-item">
                            <span>{{ $row[0] }}</span>
                            <strong>{{ $row[1] }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="card ai-assistant">
                <p class="ai-kicker"><i class="fas fa-robot" aria-hidden="true"></i> Ask AI Assistant</p>
                <div class="ai-suggestions">
                    @foreach($aiSuggestedQuestions as $question)
                        <button type="button" class="ai-chip" data-ai-question="{{ $question }}">{{ $question }}</button>
                    @endforeach
                </div>
                <div class="ai-ask-row">
                    <input type="text" id="ai-question-input" placeholder="Ask something else about this product">
                    <button type="button" id="ai-ask-btn">Ask</button>
                </div>
                <div class="ai-answer hidden" id="ai-answer-box">
                    <p class="ai-answer-title">AI Answer</p>
                    <p id="ai-answer-text"></p>
                </div>
            </section>
        </div>

        <aside class="card buy">
            <div class="price-wrap">
                <p class="price" id="price-node">{{ store_money($product->final_price) }}</p>
                <p class="old {{ $product->compare_price && $product->compare_price > $product->price ? '' : 'hidden' }}" id="old-price-node">
                    @if($product->compare_price && $product->compare_price > $product->price){{ store_money($product->compare_price) }}@endif
                </p>
                <p class="save {{ $product->compare_price && $product->compare_price > $product->price ? '' : 'hidden' }}" id="save-node">
                    @if($product->compare_price && $product->compare_price > $product->price)Save {{ store_money($product->compare_price - $product->price) }}@endif
                </p>
            </div>
            <div class="finance-box">
                <p><strong>{{ $monthlyFinance }}</strong>/month estimated payment (12 months)</p>
                <p>or 4 interest-free payments of <strong>{{ $installmentFinance }}</strong></p>
            </div>
            <p class="stock" id="stock-node">
                @if(!$product->track_quantity)In stock
                @elseif($product->allow_backorder)Backorder available
                @elseif($product->quantity > 0)In stock ({{ $product->quantity }})
                @else Out of stock @endif
            </p>
            <div class="policy-list">
                <div class="policy-item">
                    <span class="policy-icon" aria-hidden="true"><i class="fas fa-truck"></i></span>
                    <span class="policy-copy">
                        <strong>Free shipping</strong>
                        <small>Delivery: {{ $deliveryWindow }}</small>
                    </span>
                    <span class="policy-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                </div>
                <a href="{{ route('page.show', 'return-refund-policy') }}" class="policy-item">
                    <span class="policy-icon" aria-hidden="true"><i class="fas fa-rotate-left"></i></span>
                    <span class="policy-copy">
                        <strong>Return & refund policy</strong>
                        <small>7-day easy return on eligible products.</small>
                    </span>
                    <span class="policy-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                </a>
                <a href="{{ route('page.show', 'privacy-policy') }}" class="policy-item">
                    <span class="policy-icon" aria-hidden="true"><i class="fas fa-shield-alt"></i></span>
                    <span class="policy-copy">
                        <strong>Security & Privacy</strong>
                        <small>Protected checkout and encrypted payment.</small>
                    </span>
                    <span class="policy-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                </a>
            </div>
            <div class="qty">
                <button type="button" data-dec aria-label="Decrease quantity">-</button>
                <input type="number" id="qty-node" value="1" min="1" step="1" inputmode="numeric">
                <button type="button" data-inc aria-label="Increase quantity">+</button>
            </div>
            <button type="button" class="btn btn-primary" id="add-btn">Add to Cart</button>
            <button type="button" class="btn btn-secondary" id="buy-btn">Buy Now</button>
            <button type="button" class="wish-btn" onclick="toggleWishlist({{ $product->id }}, this)"><i class="fas fa-heart"></i> Save</button>
            <div class="assist-actions">
                <button type="button" class="assist-btn" data-buy-action="compare"><i class="far fa-clone"></i> Compare</button>
                <button type="button" class="assist-btn" data-buy-action="alert"><i class="far fa-bell"></i> Price alert</button>
                <button type="button" class="assist-btn" data-buy-action="report"><i class="far fa-flag"></i> Report listing</button>
            </div>
            <div class="social-row">
                <button type="button" class="social-btn" id="share-product-btn" data-share-url="{{ $shareUrl }}"
                    data-share-title="{{ $product->name }}">
                    <i class="fas fa-share-alt"></i>
                    <span>Share</span>
                </button>
                <button type="button" class="social-btn" onclick="toggleWishlist({{ $product->id }}, this)">
                    <i class="far fa-heart"></i>
                    <span>{{ $likeCountDisplay }}</span>
                </button>
            </div>
            <div class="seller-box">
                <p class="seller-title">Sold by {{ $vendor?->shop_name ?? 'NovaMart' }}</p>
                <p class="seller-rating"><i class="fas fa-star"></i> {{ $sellerRating }} ({{ $sellerReviews }} reviews)</p>
                <p class="seller-meta">{{ $sellerOrders }} orders completed</p>
                <p class="seller-meta">Ships from {{ $sellerLocation }}</p>
            </div>
        </aside>
    </div>

    <section class="card tabs">
        <div class="tab-head">
            <button type="button" class="tab-btn is-active" data-tab="desc">Description</button>
            <button type="button" class="tab-btn" data-tab="spec">Specifications</button>
            <button type="button" class="tab-btn" id="qa-tab-btn" data-tab="qa">Q&amp;A ({{ $questionCount }})</button>
            <button type="button" class="tab-btn" data-tab="price-history">Price History</button>
            <button type="button" class="tab-btn" id="reviews-tab-btn" data-tab="review">Reviews ({{ $reviewCount }})</button>
        </div>
        <div class="tab-body">
            <div class="tab-pane is-active" data-pane="desc">
                <div class="desc-rich">
                    <section class="desc-block">
                        <h3>Product Overview</h3>
                        @foreach($descriptionParagraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </section>

                    @if($highlights->isNotEmpty())
                        <section class="desc-block">
                            <h3>Key Highlights</h3>
                            <ul class="desc-bullets">
                                @foreach($highlights as $highlight)
                                    <li>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if($optionSummary->isNotEmpty())
                        <section class="desc-block">
                            <h3>Available Options</h3>
                            <div class="desc-fact-grid">
                                @foreach($optionSummary as $row)
                                    <p>{{ $row['label'] }}</p>
                                    <strong>{{ $row['value'] }}</strong>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="desc-block">
                        <h3>Complete Product Details</h3>
                        <div class="desc-fact-grid">
                            @foreach($detailFacts as $row)
                                <p>{{ $row[0] }}</p>
                                <strong>{{ $row[1] }}</strong>
                            @endforeach
                        </div>
                    </section>

                    <section class="desc-block">
                        <h3>Shipping, Return, and Support</h3>
                        <ul class="desc-bullets">
                            <li>Estimated delivery window: {{ $deliveryWindow }}.</li>
                            <li>Return and refund: 7-day return on eligible products as per store policy.</li>
                            <li>Checkout: secure payment flow with account-level order tracking.</li>
                            <li>Seller support: {{ $vendor?->shop_name ?? 'NovaMart' }} and support team handle product queries.</li>
                        </ul>
                    </section>

                    @if($descriptionHtml !== '' && str_contains($descriptionHtml, '<'))
                        <section class="desc-block">
                            <h3>Original Seller Description</h3>
                            <div class="desc-original">
                                {!! $descriptionHtml !!}
                            </div>
                        </section>
                    @endif
                </div>
            </div>
            <div class="tab-pane" data-pane="spec">
                <div class="spec-grid">@foreach($specs as $row)<p>{{ $row[0] }}</p><strong>{{ $row[1] }}</strong>@endforeach</div>
            </div>
            <div class="tab-pane" data-pane="qa">
                <div class="qa-list">
                    @forelse($questions as $question)
                        <article class="qa-item">
                            <p class="qa-question"><strong>Q:</strong> {{ $question->question }}</p>
                            <p class="qa-meta">
                                Asked by {{ $question->user->name ?? 'Customer' }}
                                on {{ $question->created_at?->format('d M Y') }}
                            </p>
                            @if($question->answer)
                                <p class="qa-answer"><strong>A:</strong> {{ $question->answer }}</p>
                                <p class="qa-meta">
                                    Answered {{ $question->answered_at?->format('d M Y') ?: $question->updated_at?->format('d M Y') }}
                                </p>
                            @else
                                <p class="qa-pending">Awaiting answer from seller/support.</p>
                            @endif
                        </article>
                    @empty
                        <p>No questions yet.</p>
                    @endforelse
                </div>

                <div class="qa-form-wrap">
                    <h3>Ask a Question</h3>
                    @auth
                        <form method="POST" action="{{ route('products.questions.store', $product->slug) }}" class="qa-form">
                            @csrf
                            <div class="form-group">
                                <label for="question-input">Your Question</label>
                                <textarea id="question-input" name="question" class="form-control" rows="3"
                                    minlength="10" maxlength="1200" required>{{ old('question') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Question</button>
                        </form>
                    @else
                        <p class="review-note">
                            <a href="{{ route('login') }}">Sign in</a> to ask a question about this product.
                        </p>
                    @endauth
                </div>
            </div>
            <div class="tab-pane" data-pane="price-history">
                @if($priceHistory->isEmpty())
                    <p>No price changes recorded yet.</p>
                @else
                    <div class="price-history-wrap">
                        <table class="price-history-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Compare Price</th>
                                    <th>Changed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($priceHistory as $row)
                                    <tr>
                                        <td>{{ $row['changed_at']?->format('d M Y, h:i A') ?? 'N/A' }}</td>
                                        <td>
                                            {{ $row['old_price'] !== null ? store_money($row['old_price']) : 'N/A' }}
                                            <span class="arrow">&rarr;</span>
                                            {{ $row['new_price'] !== null ? store_money($row['new_price']) : 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $row['old_compare_price'] !== null ? store_money($row['old_compare_price']) : 'N/A' }}
                                            <span class="arrow">&rarr;</span>
                                            {{ $row['new_compare_price'] !== null ? store_money($row['new_compare_price']) : 'N/A' }}
                                        </td>
                                        <td>{{ $row['changed_by'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="tab-pane" data-pane="review">
                @forelse($reviews->take(10) as $r)
                    <article class="review">
                        <p>
                            <strong>{{ $r->user->name ?? 'Customer' }}</strong>
                            <span>{{ $r->created_at?->format('d M Y') }}</span>
                            @if($r->is_verified_purchase)
                                <span class="review-badge">Verified Purchase</span>
                            @endif
                        </p>
                        <p>{{ $r->title ?: 'Review' }}</p>
                        @if($r->comment)<p>{{ $r->comment }}</p>@endif
                    </article>
                @empty
                    <p>No reviews yet.</p>
                @endforelse

                <div class="review-form-wrap">
                    <h3>{{ $existingReview ? 'Update Your Review' : 'Write a Review' }}</h3>

                    @if($errors->any())
                        <div class="alert alert-error" style="margin-top: 8px;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $errors->first('rating') ?: ($errors->first('comment') ?: $errors->first('title')) }}</span>
                        </div>
                    @endif

                    @auth
                        @if($canReview)
                            <form method="POST" action="{{ route('products.reviews.store', $product->slug) }}" class="review-form">
                                @csrf
                                <div class="review-grid">
                                    <div class="form-group">
                                        <label for="review-rating">Rating</label>
                                        <select id="review-rating" name="rating" class="form-control" required>
                                            @for($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}"
                                                    {{ (int) old('rating', $existingReview->rating ?? 5) === $i ? 'selected' : '' }}>
                                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="review-title">Title (optional)</label>
                                        <input id="review-title" type="text" name="title" class="form-control"
                                            maxlength="120" value="{{ old('title', $existingReview->title ?? '') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="review-comment">Comment</label>
                                    <textarea id="review-comment" name="comment" class="form-control" rows="4"
                                        minlength="10" maxlength="2000" required>{{ old('comment', $existingReview->comment ?? '') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    {{ $existingReview ? 'Update Review' : 'Submit Review' }}
                                </button>
                            </form>
                        @else
                            <p class="review-note">{{ $reviewAccessMessage }}</p>
                        @endif
                    @else
                        <p class="review-note">
                            <a href="{{ route('login') }}">Sign in</a> as a customer and purchase this product to leave a verified review.
                        </p>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <section class="pdp-section">
        <div class="section-title section-title-inline">
            <h2>Similar Products</h2>
            <a href="{{ route('products.index') }}">Shop All Products</a>
        </div>
        @if($similarProducts->isNotEmpty())
            <div class="grid grid-4">
                @foreach($similarProducts as $related)
                    @include('frontend.products.partials.product-card', ['product' => $related])
                @endforeach
            </div>
        @else
            <p class="section-empty">No similar products available right now.</p>
        @endif
    </section>

    @if($relatedTopicProducts->isNotEmpty())
        <section class="pdp-section">
            <div class="section-title">
                <h2>Products Related To This Item</h2>
            </div>
            <div class="topic-pills">
                @foreach($relatedTopics as $index => $topic)
                    <span class="topic-pill {{ $index === 0 ? 'is-active' : '' }}">{{ $topic }}</span>
                @endforeach
            </div>
            <div class="grid grid-4">
                @foreach($relatedTopicProducts as $related)
                    @include('frontend.products.partials.product-card', ['product' => $related])
                @endforeach
            </div>
        </section>
    @endif

    @if($comparisonColumns->isNotEmpty())
        <section class="pdp-section card compare-section">
            <div class="compare-head">
                <h2>Compare With Similar Products</h2>
                <a href="{{ route('products.index') }}" class="compare-cta">Compare similar products</a>
            </div>
            <div class="compare-table-wrap">
                <table class="compare-matrix">
                    <thead>
                        <tr>
                            <th class="compare-label-head">Products</th>
                            @foreach($comparisonColumns as $column)
                                <th class="compare-product-head {{ $column['is_current'] ? 'is-current' : '' }}">
                                    @if($column['is_current'])
                                        <span class="compare-current-tag">Currently Viewing</span>
                                    @endif
                                    <a href="{{ route('products.show', $column['product']->slug) }}" class="compare-thumb">
                                        <img src="{{ $column['product']->primary_image_url }}" alt="{{ $column['product']->name }}"
                                            onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-product-image.svg') }}';">
                                    </a>
                                    <p class="compare-name">
                                        <a href="{{ route('products.show', $column['product']->slug) }}">
                                            {{ \Illuminate\Support\Str::limit($column['product']->name, 60) }}
                                        </a>
                                    </p>
                                    <button type="button" class="compare-add-btn"
                                        onclick="event.preventDefault();addToCart({{ (int) $column['product']->id }})">
                                        Add to cart
                                    </button>
                                    @if($column['is_best_seller'])
                                        <span class="compare-seller-tag">Best Seller</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comparisonRowLabels as $rowKey => $rowLabel)
                            <tr>
                                <th class="compare-row-label">{{ $rowLabel }}</th>
                                @foreach($comparisonColumns as $column)
                                    <td class="compare-cell">
                                        @if($rowKey === 'rating')
                                            <div class="compare-rating-line">
                                                <span class="compare-rating-stars" aria-hidden="true">
                                                    @for($star = 1; $star <= 5; $star++)
                                                        <span class="dot {{ $column['rows']['rating'] >= $star ? 'is-on' : '' }}"></span>
                                                    @endfor
                                                </span>
                                                <span class="compare-rating-count">({{ number_format((int) $column['rows']['reviews_count']) }})</span>
                                            </div>
                                        @elseif($rowKey === 'sold_by')
                                            <a href="{{ route('products.search', ['q' => $column['rows']['sold_by']]) }}" class="compare-seller-link">
                                                {{ $column['rows']['sold_by'] }}
                                            </a>
                                        @else
                                            {{ $column['rows'][$rowKey] ?? 'N/A' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if($sellerShowcaseProducts->isNotEmpty())
        <section class="pdp-section card seller-more-section">
            <div class="section-title">
                <h2>More From This Seller</h2>
            </div>
            <div class="seller-more-layout">
                <aside class="seller-more-sidebar">
                    <div class="seller-card">
                        <img src="{{ $vendor?->logo_url ?? asset('images') . '/no-product-image.svg' }}" alt="{{ $vendor?->shop_name ?? 'NovaMart' }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-product-image.svg') }}';">
                        <strong>{{ $vendor?->shop_name ?? 'NovaMart' }}</strong>
                    </div>
                    <div class="seller-cat-list">
                        @forelse($sellerCategoryLinks as $category)
                            <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                        @empty
                            <span>More categories coming soon</span>
                        @endforelse
                    </div>
                    <a href="{{ route('products.search', ['q' => $vendor?->shop_name]) }}" class="seller-more-link">Click for More Products</a>
                </aside>
                <div class="seller-more-products grid grid-3">
                    @foreach($sellerShowcaseProducts as $sellerProduct)
                        @include('frontend.products.partials.product-card', ['product' => $sellerProduct])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="pdp-section card warranty-section">
        <div class="section-title">
            <h2>Warranty & Returns</h2>
        </div>
        <div class="warranty-grid">
            <article class="warranty-card">
                <h3>Warranty</h3>
                <p>Please contact the seller directly for warranty information. Manufacturer support may also apply by brand.</p>
                <a href="{{ route('contact') }}">Contact seller support</a>
            </article>
            <article class="warranty-card">
                <h3>Return Policies</h3>
                <p>Eligible products can be returned as per store policy window and condition checks.</p>
                <a href="{{ route('page.show', 'return-refund-policy') }}">Read return policy</a>
            </article>
            <article class="warranty-card">
                <h3>Manufacturer Contact</h3>
                <p>Need brand documentation or support center details for this item.</p>
                <a href="{{ route('products.search', ['q' => $product->brand?->name ?? $product->name]) }}">View manufacturer products</a>
            </article>
        </div>
    </section>

    @if($buyingOfferRows->isNotEmpty())
        <section class="pdp-section card buying-options-section">
            <div class="section-title">
                <h2>More Buying Options</h2>
                <p>{{ $buyingOptionCount }} option{{ $buyingOptionCount > 1 ? 's' : '' }} from {{ store_money($lowestBuyingPrice ?: (float) $product->final_price) }}</p>
            </div>
            <div class="buying-options-wrap">
                <table class="buying-options-table">
                    <thead>
                        <tr>
                            <th>Condition</th>
                            <th>Delivery</th>
                            <th>Seller</th>
                            <th>Price + Shipping</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($buyingOfferRows as $row)
                            <tr>
                                <td>{{ $row['condition'] }}</td>
                                <td>
                                    <p>{{ $row['delivery'] }}</p>
                                    <small>Arrives in {{ $row['eta'] }}</small>
                                </td>
                                <td>{{ $row['item']->vendor->shop_name ?? 'NovaMart' }}</td>
                                <td>{{ store_money($row['item']->final_price) }}</td>
                                <td>
                                    @if($row['is_current'])
                                        <button type="button" class="buying-btn"
                                            onclick="event.preventDefault();addToCart({{ (int) $row['item']->id }})">
                                            Add to cart
                                        </button>
                                    @else
                                        <a href="{{ route('products.show', $row['item']->slug) }}" class="buying-btn">View item</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section class="pdp-section deals-signup">
        <div class="deals-copy">
            <p class="deals-kicker">Deals Just For You</p>
            <h3>Sign up to receive exclusive offers in your inbox.</h3>
            <p>Get product drops, price cuts, and category-specific weekly deals.</p>
            <div class="deals-form">
                <input type="email" id="deals-email-input" placeholder="Enter your email address">
                <button type="button" id="deals-signup-btn">Sign Up</button>
            </div>
        </div>
        <div class="deals-art" aria-hidden="true">
            <i class="fas fa-paper-plane"></i>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script type="application/json" id="product-page-config">
    @json($productPageConfig)
</script>
@endpush

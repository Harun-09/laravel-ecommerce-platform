<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageAcknowledgementMail;
use App\Mail\ContactMessageSubmittedMail;
use App\Domains\ECommerce\Models\Banner;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\ContactMessage;
use App\Domains\ECommerce\Models\DealSubscription;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\FlashSale;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class HomeController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $banners = Banner::active()->position('hero')->ordered()->get();
        $featuredCategories = Category::active()
            ->parents()
            ->featured()
            ->with('children')
            ->ordered()
            ->take(8)
            ->get();

        if ($featuredCategories->count() < 8) {
            $additionalCategories = Category::active()
                ->parents()
                ->whereNotIn('id', $featuredCategories->pluck('id'))
                ->with('children')
                ->ordered()
                ->take(8 - $featuredCategories->count())
                ->get();

            $categories = $featuredCategories->concat($additionalCategories);
        } else {
            $categories = $featuredCategories;
        }

        $featuredProducts = $this->productService->getFeaturedProducts(8);
        $newArrivals = $this->productService->getNewArrivals(8);
        $bestSellers = $this->productService->getBestSellers(8);
        $seededShowcaseProductIds = $featuredProducts
            ->concat($newArrivals)
            ->concat($bestSellers)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $seededShowcaseImageUrls = $featuredProducts
            ->concat($newArrivals)
            ->concat($bestSellers)
            ->map(fn(Product $product): string => (string) $product->primary_image_url)
            ->filter(fn(string $url): bool => $url !== '' && !str_contains($url, '/images/no-product-image.svg'))
            ->unique()
            ->values()
            ->all();

        $homeShowcaseGroups = $this->buildHomeShowcaseGroups($seededShowcaseProductIds, $seededShowcaseImageUrls);
        $homeProductSections = $this->buildHomeProductSections();

        $flashSale = FlashSale::running()
            ->with(['products' => fn($q) => $q->with('primaryImage')->take(8)])
            ->first();

        $accountEmail = (string) (auth()->user()?->email ?? '');
        $newsletterAccountSubscribed = false;

        if ($accountEmail !== '') {
            $newsletterAccountSubscribed = DealSubscription::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($accountEmail)])
                ->where('is_active', true)
                ->exists();
        }

        return view('frontend.home', compact(
            'banners',
            'categories',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'flashSale',
            'homeShowcaseGroups',
            'homeProductSections',
            'newsletterAccountSubscribed',
        ));
    }

    private function buildHomeShowcaseGroups(array $seededUsedProductIds = [], array $seededUsedImageUrls = []): array
    {
        $groups = [
            [
                'title' => __('Your Go-to Destination for Electronics!'),
                'view_all_url' => $this->buildListingUrl('electronics', ['sort' => 'popular']),
                'items' => [
                    [
                        'title' => __('Electronics & Appliances'),
                        'subtitle' => __('Official Warranty | EMI with 33 Banks'),
                        'category_slugs' => ['electronics', 'home-living'],
                        'keywords' => ['laptop', 'phone', 'refrigerator', 'washer'],
                        'url' => $this->buildListingUrl('electronics', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Official Smartphones'),
                        'subtitle' => __('Display Insurance | Fast Delivery'),
                        'category_slugs' => ['mobile-phones'],
                        'keywords' => ['iphone', 'galaxy', 'xiaomi', 'oneplus'],
                        'url' => $this->buildListingUrl('mobile-phones', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Gadgets & Accessories'),
                        'subtitle' => __('Brand Warranty | Same-day Delivery'),
                        'category_slugs' => ['accessories'],
                        'keywords' => ['headphone', 'watch', 'earbuds', 'mouse'],
                        'url' => $this->buildListingUrl('accessories', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Kitchen Appliances'),
                        'subtitle' => __('Top Brands | Best Prices'),
                        'category_slugs' => ['kitchen-dining', 'home-living'],
                        'keywords' => ['cooker', 'fryer', 'microwave', 'blender'],
                        'url' => $this->buildListingUrl('kitchen-dining', ['sort' => 'price_low']),
                    ],
                    [
                        'title' => __('Lifestyle Essentials'),
                        'subtitle' => __('Free Delivery | Same-day Delivery'),
                        'category_slugs' => ['fashion', 'beauty-health'],
                        'keywords' => ['shirt', 'hoodie', 'serum', 'face wash'],
                        'url' => $this->buildListingUrl('fashion', ['sort' => 'popular']),
                    ],
                ],
            ],
            [
                'title' => __('Upgrade Your Home & Lifestyle Today!'),
                'view_all_url' => $this->buildListingUrl('home-living', ['sort' => 'popular']),
                'items' => [
                    [
                        'title' => __('Laptops & Computers'),
                        'subtitle' => __('Official Warranty | Fast Delivery'),
                        'category_slugs' => ['laptops-computers'],
                        'keywords' => ['laptop', 'computer', 'macbook', 'inspiron'],
                        'url' => $this->buildListingUrl('laptops-computers', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Refrigerators & Freezers'),
                        'subtitle' => __('Top Brands | Best Prices'),
                        'category_slugs' => ['home-living'],
                        'keywords' => ['refrigerator', 'fridge', 'freezer'],
                        'url' => $this->buildListingUrl('home-living', ['q' => 'refrigerator', 'sort' => 'popular']),
                    ],
                    [
                        'title' => __('Kitchen Essentials'),
                        'subtitle' => __('Free Delivery | Easy EMI'),
                        'category_slugs' => ['kitchen-dining'],
                        'keywords' => ['cooker', 'fryer', 'microwave', 'blender'],
                        'url' => $this->buildListingUrl('kitchen-dining', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Furniture & Living'),
                        'subtitle' => __('Durable Build | Smart Design'),
                        'category_slugs' => ['furniture', 'home-living'],
                        'keywords' => ['sofa', 'dining table', 'furniture'],
                        'url' => $this->buildListingUrl('furniture', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Beauty & Skincare'),
                        'subtitle' => __('Authentic Products | Fast Delivery'),
                        'category_slugs' => ['beauty-health', 'skincare'],
                        'keywords' => ['serum', 'face wash', 'skincare'],
                        'url' => $this->buildListingUrl('beauty-health', ['sort' => 'popular']),
                    ],
                    [
                        'title' => __('Fitness Essentials'),
                        'subtitle' => __('Top Picks | Best Performance'),
                        'category_slugs' => ['sports-equipment', 'fitness'],
                        'keywords' => ['dumbbell', 'yoga', 'fitness'],
                        'url' => $this->buildListingUrl('sports-outdoors', ['sort' => 'popular']),
                    ],
                ],
            ],
            [
                'title' => __('Buy Official Smartphones with Brand Warranty!'),
                'view_all_url' => $this->buildListingUrl('mobile-phones', ['sort' => 'popular']),
                'items' => [
                    [
                        'title' => __('OnePlus Smartphones'),
                        'subtitle' => __('Lifetime Display Warranty (Green Line)'),
                        'category_slugs' => ['mobile-phones'],
                        'keywords' => ['oneplus'],
                        'url' => $this->buildListingUrl('mobile-phones', ['q' => 'oneplus', 'sort' => 'popular']),
                    ],
                    [
                        'title' => __('Android Flagship Picks'),
                        'subtitle' => __('Official Warranty | Fast Delivery'),
                        'category_slugs' => ['mobile-phones'],
                        'keywords' => ['galaxy', 'xiaomi', 'pro'],
                        'url' => $this->buildListingUrl('mobile-phones', ['q' => 'android', 'sort' => 'popular']),
                    ],
                    [
                        'title' => __('Samsung Smartphones'),
                        'subtitle' => __('Easy EMI | Display Insurance'),
                        'category_slugs' => ['mobile-phones'],
                        'keywords' => ['samsung'],
                        'url' => $this->buildListingUrl('mobile-phones', ['q' => 'samsung', 'sort' => 'popular']),
                    ],
                    [
                        'title' => __('Xiaomi Smartphones'),
                        'subtitle' => __('Cash/Card on Delivery'),
                        'category_slugs' => ['mobile-phones'],
                        'keywords' => ['xiaomi', 'redmi'],
                        'url' => $this->buildListingUrl('mobile-phones', ['q' => 'xiaomi', 'sort' => 'popular']),
                    ],
                    [
                        'title' => __('Apple & Premium Phones'),
                        'subtitle' => __('Best Prices | Fast Delivery'),
                        'category_slugs' => ['mobile-phones'],
                        'keywords' => ['iphone', 'pro max', 'ultra'],
                        'url' => $this->buildListingUrl('mobile-phones', ['q' => 'iphone', 'sort' => 'popular']),
                    ],
                ],
            ],
        ];

        $usedProductIds = collect($seededUsedProductIds)
            ->map(fn($id) => (int) $id)
            ->filter(fn(int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $usedImageUrls = collect($seededUsedImageUrls)
            ->map(fn($url) => trim((string) $url))
            ->filter(fn(string $url): bool => $url !== '')
            ->unique()
            ->values()
            ->all();
        foreach ($groups as &$group) {
            foreach ($group['items'] as &$item) {
                $item['image'] = $this->resolveShowcaseImage(
                    $item['category_slugs'] ?? [],
                    $item['keywords'] ?? [],
                    $usedProductIds,
                    $usedImageUrls
                );
            }
        }
        unset($group, $item);

        return $groups;
    }

    private function buildHomeProductSections(): array
    {
        $definitions = [
            [
                'title' => __('Official Smartphones with Brand Warranty!'),
                'view_all_url' => $this->buildListingUrl('mobile-phones', ['sort' => 'popular']),
                'category_slugs' => ['mobile-phones'],
                'keywords' => ['iphone', 'samsung', 'xiaomi', 'oneplus'],
                'sort' => 'discount',
                'limit' => 10,
            ],
            [
                'title' => __('Top Laptops & Computers!'),
                'view_all_url' => $this->buildListingUrl('laptops-computers', ['sort' => 'popular']),
                'category_slugs' => ['laptops-computers'],
                'keywords' => ['laptop', 'macbook', 'inspiron', 'ideapad', 'xps'],
                'sort' => 'popular',
                'limit' => 10,
            ],
            [
                'title' => __('Top Selling Home Appliances!'),
                'view_all_url' => $this->buildListingUrl('home-living', ['sort' => 'popular']),
                'category_slugs' => ['home-living', 'kitchen-dining'],
                'keywords' => ['refrigerator', 'fryer', 'cooker', 'kitchen'],
                'sort' => 'popular',
                'limit' => 10,
            ],
            [
                'title' => __('Fashion Essentials on Sale!'),
                'view_all_url' => $this->buildListingUrl('fashion', ['sort' => 'popular']),
                'category_slugs' => ['fashion', 'mens-fashion', 'womens-fashion'],
                'keywords' => ['shirt', 'hoodie', 'jeans', 'fashion'],
                'sort' => 'popular',
                'limit' => 10,
            ],
            [
                'title' => __('Beauty & Grooming Essentials!'),
                'view_all_url' => $this->buildListingUrl('beauty-health', ['sort' => 'popular']),
                'category_slugs' => ['beauty-health', 'skincare', 'hair-care'],
                'keywords' => ['serum', 'face wash', 'skincare', 'grooming'],
                'sort' => 'discount',
                'limit' => 10,
            ],
            [
                'title' => __('Fitness & Sports Picks!'),
                'view_all_url' => $this->buildListingUrl('sports-outdoors', ['sort' => 'popular']),
                'category_slugs' => ['sports-outdoors', 'sports-equipment', 'fitness'],
                'keywords' => ['dumbbell', 'yoga', 'fitness', 'sports'],
                'sort' => 'popular',
                'limit' => 10,
            ],
            [
                'title' => __('Books & Stationery Deals!'),
                'view_all_url' => $this->buildListingUrl('books-stationery', ['sort' => 'popular']),
                'category_slugs' => ['books-stationery', 'books', 'office-supplies'],
                'keywords' => ['book', 'notebook', 'office', 'stationery'],
                'sort' => 'popular',
                'limit' => 10,
            ],
            [
                'title' => __('Best Deals on Gadgets & Accessories!'),
                'view_all_url' => $this->buildListingUrl('accessories', ['sort' => 'popular']),
                'category_slugs' => ['accessories'],
                'keywords' => ['headphone', 'earbuds', 'watch', 'mouse', 'router', 'camera'],
                'sort' => 'discount',
                'limit' => 10,
            ],
            [
                'title' => __('Daily Grocery Picks!'),
                'view_all_url' => $this->buildListingUrl('groceries', ['sort' => 'popular']),
                'category_slugs' => ['groceries', 'food-beverages', 'organic'],
                'keywords' => ['rice', 'organic', 'grocery', 'food'],
                'sort' => 'popular',
                'limit' => 10,
            ],
            [
                'title' => __('New Arrivals at NovaMart!'),
                'view_all_url' => $this->buildListingUrl('electronics', ['sort' => 'latest']),
                'category_slugs' => ['electronics', 'accessories'],
                'keywords' => ['new', 'latest', 'pro'],
                'sort' => 'latest',
                'limit' => 10,
            ],
        ];

        $sections = [];
        foreach ($definitions as $definition) {
            $products = $this->resolveSectionProducts($definition);
            if ($products->count() < 4) {
                continue;
            }

            $sections[] = [
                'title' => $definition['title'],
                'view_all_url' => $definition['view_all_url'],
                'products' => $products,
            ];
        }

        return $sections;
    }

    private function resolveSectionProducts(array $definition): Collection
    {
        $limit = (int) ($definition['limit'] ?? 10);
        $categorySlugs = (array) ($definition['category_slugs'] ?? []);
        $keywords = (array) ($definition['keywords'] ?? []);
        $candidatePoolSize = max(50, $limit * 8);

        $usedImageSignatures = [];
        $usedProductIds = [];

        $query = $this->baseProductQuery();
        $this->applyCategoryFilter($query, $categorySlugs);
        $this->applyKeywordFilter($query, $keywords);
        $this->applySort($query, (string) ($definition['sort'] ?? 'popular'));
        $products = $this->pickProductsWithUniqueImages(
            $query->take($candidatePoolSize)->get(),
            $limit,
            $usedImageSignatures,
            $usedProductIds
        );

        if ($products->count() >= $limit || empty($categorySlugs)) {
            return $products->values();
        }

        $fallbackQuery = $this->baseProductQuery();
        $this->applyCategoryFilter($fallbackQuery, $categorySlugs);
        if (!empty($usedProductIds)) {
            $fallbackQuery->whereNotIn('id', $usedProductIds);
        }
        $this->applySort($fallbackQuery, 'popular');

        $missing = $limit - $products->count();
        if ($missing > 0) {
            $products = $products->concat(
                $this->pickProductsWithUniqueImages(
                    $fallbackQuery->take($candidatePoolSize)->get(),
                    $missing,
                    $usedImageSignatures,
                    $usedProductIds
                )
            );
        }

        $missing = $limit - $products->count();
        if ($missing > 0) {
            $fillQuery = $this->baseProductQuery();
            $this->applyCategoryFilter($fillQuery, $categorySlugs);
            if (!empty($usedProductIds)) {
                $fillQuery->whereNotIn('id', $usedProductIds);
            }
            $this->applySort($fillQuery, 'popular');

            $products = $products->concat($fillQuery->take($missing)->get());
        }

        return $products->values();
    }

    private function pickProductsWithUniqueImages(
        Collection $candidates,
        int $limit,
        array &$usedImageSignatures = [],
        array &$usedProductIds = []
    ): Collection {
        $selected = collect();

        foreach ($candidates as $candidate) {
            if ($selected->count() >= $limit) {
                break;
            }

            $productId = (int) $candidate->id;
            if (in_array($productId, $usedProductIds, true)) {
                continue;
            }

            $signature = $this->resolveProductCardImageSignature($candidate);
            if ($signature !== '' && in_array($signature, $usedImageSignatures, true)) {
                continue;
            }

            $selected->push($candidate);
            $usedProductIds[] = $productId;

            if ($signature !== '') {
                $usedImageSignatures[] = $signature;
            }
        }

        return $selected->values();
    }

    private function resolveProductCardImageSignature(Product $product): string
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
        $storagePosition = strpos($urlPath, $storagePrefix);
        if ($storagePosition !== false) {
            return trim((string) substr($urlPath, $storagePosition + strlen($storagePrefix)), '/');
        }

        return trim($urlPath, '/');
    }

    private function resolveShowcaseImage(
        array $categorySlugs = [],
        array $keywords = [],
        array &$usedProductIds = [],
        array &$usedImageUrls = []
    ): string
    {
        $fallbackImage = asset('images/placeholders/no-category-image.svg');
        $candidates = $this->baseProductQuery();
        $this->applyCategoryFilter($candidates, $categorySlugs);
        $this->applyKeywordFilter($candidates, $keywords);
        $this->applySort($candidates, 'popular');

        if (!empty($usedProductIds)) {
            $candidates->whereNotIn('id', $usedProductIds);
        }

        $products = $candidates->take(8)->get();

        $product = $products->first(function (Product $candidate) use ($usedImageUrls): bool {
            $imageUrl = $candidate->primary_image_url;
            return $imageUrl && !in_array($imageUrl, $usedImageUrls, true);
        }) ?? $products->first();

        // Fallback 1: Keep keywords and categories, but drop usedProductIds restriction
        // It's better to reuse an image than to show a completely unrelated product!
        if (!$product && !empty($usedProductIds)) {
            $retryQuery = $this->baseProductQuery();
            $this->applyCategoryFilter($retryQuery, $categorySlugs);
            $this->applyKeywordFilter($retryQuery, $keywords);
            $this->applySort($retryQuery, 'popular');

            $products = $retryQuery->take(8)->get();
            $product = $products->first(function (Product $candidate) use ($usedImageUrls): bool {
                $imageUrl = $candidate->primary_image_url;
                return $imageUrl && !in_array($imageUrl, $usedImageUrls, true);
            }) ?? $products->first();
        }

        // Fallback 2: Drop keywords, but keep category (only if no products matched keywords at all)
        if (!$product && !empty($categorySlugs)) {
            $fallbackQuery = $this->baseProductQuery();
            $this->applyCategoryFilter($fallbackQuery, $categorySlugs);
            if (!empty($usedProductIds)) {
                $fallbackQuery->whereNotIn('id', $usedProductIds);
            }
            $this->applySort($fallbackQuery, 'popular');

            $products = $fallbackQuery->take(8)->get();
            $product = $products->first(function (Product $candidate) use ($usedImageUrls): bool {
                $imageUrl = $candidate->primary_image_url;
                return $imageUrl && !in_array($imageUrl, $usedImageUrls, true);
            }) ?? $products->first();
            
            // Retry fallback 2 without usedProductIds
            if (!$product && !empty($usedProductIds)) {
                $retryFallbackQuery = $this->baseProductQuery();
                $this->applyCategoryFilter($retryFallbackQuery, $categorySlugs);
                $this->applySort($retryFallbackQuery, 'popular');
                $products = $retryFallbackQuery->take(8)->get();
                $product = $products->first() ?? null;
            }
        }

        if (!$product) {
            return $fallbackImage;
        }

        $imageUrl = $product->primary_image_url ?? $fallbackImage;

        $usedProductIds[] = (int) $product->id;
        $usedProductIds = array_values(array_unique($usedProductIds));

        if ($imageUrl !== $fallbackImage) {
            $usedImageUrls[] = $imageUrl;
            $usedImageUrls = array_values(array_unique($usedImageUrls));
        }

        return $imageUrl;
    }

    private function buildListingUrl(?string $categorySlug = null, array $params = []): string
    {
        $categorySlug = $categorySlug ? trim($categorySlug) : null;
        $cleanParams = collect($params)
            ->reject(fn($value) => $value === null || $value === '')
            ->all();

        if (!$categorySlug) {
            return route('products.index', $cleanParams);
        }

        $categoryExists = Category::query()->where('slug', $categorySlug)->exists();
        if (!$categoryExists) {
            return route('products.index', $cleanParams);
        }

        if (empty($cleanParams)) {
            return route('category.show', $categorySlug);
        }

        return route('products.index', ['category' => $categorySlug] + $cleanParams);
    }

    private function baseProductQuery(): Builder
    {
        return Product::query()
            ->with(['vendor', 'category.parent', 'brand', 'primaryImage'])
            ->published()
            ->inStock();
    }

    private function applyCategoryFilter(Builder $query, array $categorySlugs): void
    {
        $slugs = collect($categorySlugs)
            ->map(fn($slug) => trim((string) $slug))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($slugs)) {
            return;
        }

        $query->whereHas('category', function (Builder $categoryQuery) use ($slugs): void {
            $categoryQuery->whereIn('slug', $slugs)
                ->orWhereHas('parent', fn(Builder $parentQuery) => $parentQuery->whereIn('slug', $slugs));
        });
    }

    private function applyKeywordFilter(Builder $query, array $keywords): void
    {
        $terms = collect($keywords)
            ->map(fn($keyword) => trim((string) $keyword))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($terms)) {
            return;
        }

        $query->where(function (Builder $keywordQuery) use ($terms): void {
            foreach ($terms as $term) {
                $keywordQuery->orWhere('name', 'like', '%' . $term . '%')
                    ->orWhere('short_description', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            }
        });
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'latest' => $query->orderByDesc('created_at'),
            'price_low' => $query->orderBy('base_price'),
            'price_high' => $query->orderByDesc('base_price'),
            'discount' => $query
                ->orderByRaw('(COALESCE(compare_price, base_price) - base_price) DESC')
                ->orderByDesc('sales_count')
                ->orderByDesc('created_at'),
            default => $query
                ->orderByDesc('sales_count')
                ->orderByDesc('featured')
                ->orderByDesc('created_at'),
        };
    }

    public function about()
    {
        $page = \App\Domains\ECommerce\Models\Page::where('slug', 'about-us')->firstOrFail();
        return view('frontend.pages.about', compact('page'));
    }

    public function contact()
    {
        $page = \App\Domains\ECommerce\Models\Page::where('slug', 'contact-us')->firstOrFail();

        if (view()->exists('frontend.pages.contact')) {
            return view('frontend.pages.contact', compact('page'));
        }

        return view('frontend.pages.show', compact('page'));
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $contactMessage = DB::transaction(function () use ($validated, $request): ContactMessage {
            return ContactMessage::create([
                'user_id' => auth()->id(),
                'name' => trim((string) $validated['name']),
                'email' => strtolower(trim((string) $validated['email'])),
                'phone' => isset($validated['phone']) ? trim((string) $validated['phone']) : null,
                'subject' => trim((string) $validated['subject']),
                'message' => trim((string) $validated['message']),
                'status' => ContactMessage::STATUS_NEW,
                'ip_address' => (string) $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ]);
        });

        $this->sendContactMessageEmails($contactMessage);

        return back()->with('success', 'Your message has been received. Our support team will contact you soon.');
    }

    private function sendContactMessageEmails(ContactMessage $contactMessage): void
    {
        $supportEmail = trim((string) config('mail.contact.address', ''));

        if ($supportEmail !== '') {
            try {
                Mail::to($supportEmail)->send(new ContactMessageSubmittedMail($contactMessage));
            } catch (Throwable $exception) {
                Log::warning('Failed to send support notification for contact message.', [
                    'contact_message_id' => (int) $contactMessage->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        } else {
            Log::warning('CONTACT_SUPPORT_EMAIL is not configured. Contact message support notification skipped.', [
                'contact_message_id' => (int) $contactMessage->id,
            ]);
        }

        try {
            Mail::to($contactMessage->email)->send(new ContactMessageAcknowledgementMail($contactMessage));
        } catch (Throwable $exception) {
            Log::warning('Failed to send acknowledgement for contact message.', [
                'contact_message_id' => (int) $contactMessage->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function creditCards()
    {
        return view('frontend.pages.credit_cards');
    }

    public function giftCards()
    {
        return view('frontend.pages.gift_cards');
    }

    public function page($slug)
    {
        $page = \App\Domains\ECommerce\Models\Page::where('slug', $slug)->active()->firstOrFail();

        if ($slug === 'terms-conditions' && view()->exists('frontend.pages.terms')) {
            return view('frontend.pages.terms', compact('page'));
        }

        if ($slug === 'privacy-policy' && view()->exists('frontend.pages.privacy')) {
            return view('frontend.pages.privacy', compact('page'));
        }

        if ($slug === 'return-refund-policy' && view()->exists('frontend.pages.return_policy')) {
            return view('frontend.pages.return_policy', compact('page'));
        }

        return view('frontend.pages.show', compact('page'));
    }
}



<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Product;
use App\Domains\ECommerce\Models\Category;
use App\Domains\ECommerce\Models\Brand;
use App\Domains\ECommerce\Models\Order;
use App\Domains\ECommerce\Models\OrderItem;
use App\Domains\ECommerce\Models\Review;
use App\Domains\ECommerce\Models\AuditLog;
use App\Domains\ECommerce\Models\ProductQuestion;
use App\Domains\ECommerce\Models\VendorFollow;
use App\Domains\ECommerce\Models\ProductView;
use App\Domains\ECommerce\Models\SearchLog;
use App\Domains\ECommerce\Models\Banner;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->search($request->all());
        $categories = Category::active()->parents()->with('children')->ordered()->get();
        $brands = Brand::active()->ordered()->get();

        // Log search if query present
        if ($request->filled('q')) {
            SearchLog::record($request->q, $products->total(), auth()->id());
        }

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        $product = $this->productService->getProductDetails($slug);
        $productCardRelations = ['vendor', 'category', 'brand', 'primaryImage'];

        $currentCategoryId = (int) ($product->category_id ?? 0);
        $currentBrandId = (int) ($product->brand_id ?? 0);
        $contextRootCategoryId = (int) ($product->category?->parent_id ?: $currentCategoryId);

        $contextCategoryIds = collect();
        if ($contextRootCategoryId > 0) {
            $contextCategoryIds = Category::query()
                ->where('id', $contextRootCategoryId)
                ->orWhere('parent_id', $contextRootCategoryId)
                ->pluck('id');
        }

        if ($contextCategoryIds->isEmpty() && $currentCategoryId > 0) {
            $contextCategoryIds = collect([$currentCategoryId]);
        }

        $contextCategoryIdList = $contextCategoryIds
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $contextProductsQuery = Product::query()
            ->with($productCardRelations)
            ->where('id', '!=', $product->id)
            ->published()
            ->inStock();

        if (!empty($contextCategoryIdList)) {
            $contextProductsQuery->whereIn('category_id', $contextCategoryIdList);
        }

        $contextFallbackProducts = (clone $contextProductsQuery)
            ->orderByRaw('CASE WHEN category_id = ? THEN 0 ELSE 1 END', [$currentCategoryId ?: 0])
            ->orderByDesc('sales_count')
            ->orderByDesc('rating')
            ->orderByDesc('id')
            ->take(120)
            ->get();

        $relatedProducts = (clone $contextProductsQuery)
            ->orderByRaw(
                'CASE WHEN category_id = ? THEN 0 WHEN brand_id = ? THEN 1 ELSE 2 END',
                [$currentCategoryId ?: 0, $currentBrandId ?: 0]
            )
            ->orderByDesc('sales_count')
            ->orderByDesc('rating')
            ->orderByDesc('id')
            ->take(40)
            ->get();

        $relatedByCategoryProducts = (clone $contextProductsQuery)
            ->where('category_id', $currentCategoryId)
            ->orderByDesc('sales_count')
            ->orderByDesc('rating')
            ->orderByDesc('id')
            ->take(30)
            ->get();

        if ($relatedByCategoryProducts->isEmpty()) {
            $relatedByCategoryProducts = (clone $contextProductsQuery)
                ->orderByDesc('sales_count')
                ->orderByDesc('rating')
                ->orderByDesc('id')
                ->take(30)
                ->get();
        }

        $sellerMoreProducts = Product::query()
            ->with($productCardRelations)
            ->where('id', '!=', $product->id)
            ->where('supplier_id', $product->supplier_id)
            ->published()
            ->inStock()
            ->orderByDesc('sales_count')
            ->orderByDesc('rating')
            ->orderByDesc('id')
            ->take(24)
            ->get();

        $fillProducts = function ($collection, int $target, array $fallbackCollections = []) use ($product) {
            $base = collect($collection)
                ->filter(fn($item) => $item && (int) $item->id !== (int) $product->id)
                ->unique('id')
                ->values();

            if ($base->count() >= $target) {
                return $base->take($target)->values();
            }

            $existing = $base->pluck('id')->map(fn($id) => (int) $id)->all();
            $fallbackPool = collect();
            foreach ($fallbackCollections as $fallbackCollection) {
                $fallbackPool = $fallbackPool->merge($fallbackCollection);
            }

            $need = $target - $base->count();
            $extra = $fallbackPool
                ->filter(fn($item) => $item && (int) $item->id !== (int) $product->id)
                ->reject(fn($item) => in_array((int) ($item->id ?? 0), $existing, true))
                ->unique('id')
                ->take($need)
                ->values();

            return $base->concat($extra)->take($target)->values();
        };

        $relatedProducts = $fillProducts($relatedProducts, 28, [$contextFallbackProducts]);
        $relatedByCategoryProducts = $fillProducts($relatedByCategoryProducts, 24, [$relatedProducts, $contextFallbackProducts]);
        $sellerMoreProducts = $fillProducts($sellerMoreProducts, 18);

        $compareProducts = collect([$product])
            ->merge($fillProducts($relatedByCategoryProducts->merge($relatedProducts), 12, [$contextFallbackProducts]))
            ->unique('id')
            ->take(6)
            ->values();

        $buyingCandidates = (clone $contextProductsQuery)
            ->orderBy('base_price')
            ->take(20)
            ->get();

        $moreBuyingOptions = collect([$product])
            ->merge($buyingCandidates)
            ->merge($fillProducts(collect(), 10, [$contextFallbackProducts]))
            ->unique('id')
            ->take(10)
            ->values();

        $canReview = false;
        $reviewAccessMessage = null;
        $existingUserReview = null;

        if (auth()->check()) {
            $user = auth()->user();
            $existingUserReview = Review::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->latest('id')
                ->first();

            if (!$user->hasRole('customer')) {
                $reviewAccessMessage = 'Only customer accounts can submit product reviews.';
            } else {
                $purchased = OrderItem::query()
                    ->where('product_id', $product->id)
                    ->whereHas('order', function ($query) use ($user): void {
                        $query->where('user_id', $user->id)
                            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_RETURNED]);
                    })
                    ->exists();

                $canReview = $purchased;
                if (!$purchased) {
                    $reviewAccessMessage = 'You can review this product only after purchasing and receiving it.';
                }
            }
        } else {
            $reviewAccessMessage = 'Sign in with your customer account to submit a review after purchase.';
        }

        // Record view
        ProductView::record($product, auth()->id());

        $questions = ProductQuestion::query()
            ->with('user:id,name')
            ->where('product_id', $product->id)
            ->where('is_public', true)
            ->latest('id')
            ->take(20)
            ->get();

        $questionCount = $questions->count();
        $answeredQuestionCount = $questions->whereNotNull('answer')->count();

        $storeFollowerCount = VendorFollow::query()
            ->where('vendor_id', $product->vendor_id)
            ->count();

        $isFollowingStore = auth()->check()
            ? VendorFollow::query()
                ->where('vendor_id', $product->vendor_id)
                ->where('user_id', auth()->id())
                ->exists()
            : false;

        $priceHistory = AuditLog::query()
            ->with('actor:id,name')
            ->where('event', 'product.price_changed')
            ->where('auditable_type', Product::class)
            ->where('auditable_id', $product->id)
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(function (AuditLog $log): array {
                $oldValues = (array) ($log->old_values ?? []);
                $newValues = (array) ($log->new_values ?? []);

                return [
                    'changed_at' => $log->created_at,
                    'old_price' => isset($oldValues['price']) ? (float) $oldValues['price'] : null,
                    'new_price' => isset($newValues['price']) ? (float) $newValues['price'] : null,
                    'old_compare_price' => isset($oldValues['compare_price']) ? (float) $oldValues['compare_price'] : null,
                    'new_compare_price' => isset($newValues['compare_price']) ? (float) $newValues['compare_price'] : null,
                    'changed_by' => $log->actor?->name ?: 'System',
                ];
            })
            ->filter(fn(array $row): bool => $row['old_price'] !== null || $row['new_price'] !== null || $row['old_compare_price'] !== null || $row['new_compare_price'] !== null)
            ->values();

        return view('frontend.products.show', compact(
            'product',
            'relatedProducts',
            'canReview',
            'reviewAccessMessage',
            'existingUserReview',
            'questions',
            'questionCount',
            'answeredQuestionCount',
            'storeFollowerCount',
            'isFollowingStore',
            'priceHistory',
            'relatedByCategoryProducts',
            'sellerMoreProducts',
            'compareProducts',
            'moreBuyingOptions',
        ));
    }

    public function storeReview(Request $request, Product $product)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('customer')) {
            return back()->with('error', 'Only customer accounts can submit reviews.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $purchaseItem = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) use ($user): void {
                $query->where('user_id', $user->id)
                    ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_RETURNED]);
            })
            ->latest('id')
            ->first();

        if (!$purchaseItem) {
            return back()->with('error', 'Review rejected: only verified buyers can review this product.');
        }

        $existingReview = Review::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->latest('id')
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'order_id' => $purchaseItem->order_id,
                'rating' => (int) $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'is_verified_purchase' => true,
                'is_approved' => false,
            ]);

            $message = 'Your review has been updated and is pending admin approval.';
        } else {
            Review::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'order_id' => $purchaseItem->order_id,
                'rating' => (int) $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'is_verified_purchase' => true,
                'is_approved' => false,
            ]);

            $message = 'Your review has been submitted and is pending admin approval.';
        }

        $product->updateRating();

        return back()->with('success', $message);
    }

    public function storeQuestion(Request $request, Product $product)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'min:10', 'max:1200'],
        ]);

        ProductQuestion::query()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'question' => trim((string) $validated['question']),
            'is_public' => true,
        ]);

        return back()->with('success', 'Your question has been submitted.');
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();
        $categorySearchText = strtolower(trim($category->slug . ' ' . $category->name));

        $categoryVisualProfiles = [
            [
                'match' => ['laptop', 'computer'],
                'hub' => [
                    asset('images') . '/storefront/laptops/hub/top-deals.jpg',
                    asset('images') . '/storefront/laptops/hub/all-laptops.jpg',
                    asset('images') . '/storefront/laptops/hub/windows-laptops.jpg',
                    asset('images') . '/storefront/laptops/hub/macbooks.jpg',
                    asset('images') . '/storefront/laptops/hub/chromebooks.jpg',
                    asset('images') . '/storefront/laptops/hub/gaming-laptops.jpg',
                    asset('images') . '/storefront/laptops/hub/copilot-laptops.jpg',
                    asset('images') . '/storefront/laptops/hub/surface-laptops.jpg',
                ],
                'promo' => [
                    'main' => asset('images') . '/storefront/laptops/promo/main.jpg',
                    'side' => asset('images') . '/storefront/laptops/promo/side.jpg',
                ],
                'copy' => [
                    'main_kicker' => 'Featured',
                    'main_title' => $category->name . ' deals',
                    'main_description' => 'Browse curated CC0 visuals and latest laptop offers inspired by modern category hubs.',
                    'main_cta' => 'Shop deals',
                    'main_sort' => 'popular',
                    'side_kicker' => 'Smart Picks',
                    'side_title' => 'Copilot+ and creator-ready',
                    'side_description' => 'Compare performance-first laptops for gaming, study, and work in one flow.',
                    'side_cta' => 'Explore now',
                    'side_sort' => 'newest',
                ],
            ],
            [
                'match' => ['phone', 'mobile', 'smartphone'],
                'hub' => [
                    asset('images') . '/storefront/phones/hub/top-deals.jpg',
                    asset('images') . '/storefront/phones/hub/all-phones.jpg',
                    asset('images') . '/storefront/phones/hub/android-phones.jpg',
                    asset('images') . '/storefront/phones/hub/iphone.jpg',
                    asset('images') . '/storefront/phones/hub/budget-phones.jpg',
                    asset('images') . '/storefront/phones/hub/camera-phones.jpg',
                    asset('images') . '/storefront/phones/hub/gaming-phones.jpg',
                    asset('images') . '/storefront/phones/hub/phone-accessories.jpg',
                ],
                'promo' => [
                    'main' => asset('images') . '/storefront/phones/promo/main.jpg',
                    'side' => asset('images') . '/storefront/phones/promo/side.jpg',
                ],
                'copy' => [
                    'main_kicker' => 'Mobile Picks',
                    'main_title' => $category->name . ' specials',
                    'main_description' => 'Find flagship, camera, and value phones with curated deals and fresh drops.',
                    'main_cta' => 'Shop phones',
                    'main_sort' => 'popular',
                    'side_kicker' => 'New Arrivals',
                    'side_title' => 'Camera and battery champions',
                    'side_description' => 'Compare top phone launches and pick the right performance-price balance.',
                    'side_cta' => 'Explore phones',
                    'side_sort' => 'newest',
                ],
            ],
            [
                'match' => ['tv', 'television'],
                'hub' => [
                    asset('images') . '/storefront/tv/hub/top-deals.jpg',
                    asset('images') . '/storefront/tv/hub/all-tvs.jpg',
                    asset('images') . '/storefront/tv/hub/oled-tvs.jpg',
                    asset('images') . '/storefront/tv/hub/qled-tvs.jpg',
                    asset('images') . '/storefront/tv/hub/4k-tvs.jpg',
                    asset('images') . '/storefront/tv/hub/gaming-tvs.jpg',
                    asset('images') . '/storefront/tv/hub/large-screen.jpg',
                    asset('images') . '/storefront/tv/hub/soundbars.jpg',
                ],
                'promo' => [
                    'main' => asset('images') . '/storefront/tv/promo/main.jpg',
                    'side' => asset('images') . '/storefront/tv/promo/side.jpg',
                ],
                'copy' => [
                    'main_kicker' => 'Home Theater',
                    'main_title' => $category->name . ' offers',
                    'main_description' => 'Upgrade your setup with OLED, QLED, and 4K picks tuned for every room.',
                    'main_cta' => 'Shop TVs',
                    'main_sort' => 'popular',
                    'side_kicker' => 'Streaming Ready',
                    'side_title' => 'Bigger screens, sharper details',
                    'side_description' => 'Filter by display type, size, and budget to find your next living room upgrade.',
                    'side_cta' => 'Explore TVs',
                    'side_sort' => 'newest',
                ],
            ],
            [
                'match' => ['fashion', 'clothing', 'apparel', 'wear'],
                'hub' => [
                    asset('images') . '/storefront/fashion/hub/top-deals.jpg',
                    asset('images') . '/storefront/fashion/hub/all-fashion.jpg',
                    asset('images') . '/storefront/fashion/hub/mens-fashion.jpg',
                    asset('images') . '/storefront/fashion/hub/womens-fashion.jpg',
                    asset('images') . '/storefront/fashion/hub/kids-fashion.jpg',
                    asset('images') . '/storefront/fashion/hub/footwear.jpg',
                    asset('images') . '/storefront/fashion/hub/bags-luggage.jpg',
                    asset('images') . '/storefront/fashion/hub/watches.jpg',
                ],
                'promo' => [
                    'main' => asset('images') . '/storefront/fashion/promo/main.jpg',
                    'side' => asset('images') . '/storefront/fashion/promo/side.jpg',
                ],
                'copy' => [
                    'main_kicker' => 'Style Edit',
                    'main_title' => $category->name . ' trends',
                    'main_description' => 'Discover everyday essentials and statement pieces curated for all seasons.',
                    'main_cta' => 'Shop fashion',
                    'main_sort' => 'popular',
                    'side_kicker' => 'Fresh Looks',
                    'side_title' => 'Outfits for work and weekend',
                    'side_description' => 'Mix clothing, footwear, and accessories to build your perfect look quickly.',
                    'side_cta' => 'Explore styles',
                    'side_sort' => 'newest',
                ],
            ],
        ];

        $categoryVisualFallback = collect($categoryVisualProfiles)->first(function (array $profile) use ($categorySearchText): bool {
            foreach ($profile['match'] as $keyword) {
                if (str_contains($categorySearchText, strtolower($keyword))) {
                    return true;
                }
            }

            return false;
        });

        $products = $this->productService->getProductsByCategory($category, $request->all());
        $subcategories = $category->children()->active()->ordered()->get();

        $categoryIds = array_merge(
            [$category->id],
            $subcategories->pluck('id')->all()
        );

        $brands = Brand::query()
            ->active()
            ->whereHas('products', function ($query) use ($categoryIds): void {
                $query->whereIn('category_id', $categoryIds)
                    ->published()
                    ->inStock();
            })
            ->ordered()
            ->get();

        $quickLinks = collect([
            [
                'title' => 'Top Deals',
                'url' => route('category.show', ['slug' => $category->slug, 'sort' => 'price_low']),
                'image' => $category->image_url,
            ],
            [
                'title' => 'All ' . $category->name,
                'url' => route('category.show', $category->slug),
                'image' => $category->image_url,
            ],
        ])->merge(
            $subcategories->take(6)->map(fn(Category $subCategory) => [
                'title' => $subCategory->name,
                'url' => route('category.show', $subCategory->slug),
                'image' => $subCategory->image_url,
            ])
        )->values()
            ->map(function (array $link, int $index) use ($categoryVisualFallback): array {
                $current = (string) ($link['image'] ?? '');
                $isPlaceholder = empty($current) || str_contains($current, 'no-category-image.svg');
                $hubImagePool = $categoryVisualFallback['hub'] ?? [];

                if ($isPlaceholder && isset($hubImagePool[$index])) {
                    $link['image'] = $hubImagePool[$index];
                } elseif (empty($link['image'])) {
                    $link['image'] = asset('images') . '/placeholders/no-category-image.svg';
                }

                return $link;
            });

        $heroProducts = Product::query()
            ->with(['vendor', 'primaryImage'])
            ->whereIn('category_id', $categoryIds)
            ->published()
            ->inStock()
            ->orderBy('featured', 'desc')
            ->orderBy('sales_count', 'desc')
            ->take(2)
            ->get();

        $categoryBanners = Banner::query()
            ->active()
            ->position('category')
            ->ordered()
            ->take(2)
            ->get();

        if (!view()->exists('frontend.products.category')) {
            $categories = Category::active()->parents()->with('children')->ordered()->get();

            return view('frontend.products.index', compact('products', 'categories', 'brands'));
        }

        return view('frontend.products.category', compact(
            'category',
            'products',
            'subcategories',
            'brands',
            'quickLinks',
            'heroProducts',
            'categoryBanners',
            'categoryVisualFallback',
        ));
    }

    public function search(Request $request)
    {
        if (!$request->filled('q')) {
            return redirect()->route('products.index');
        }

        $products = $this->productService->search(['q' => $request->q]);
        $categories = Category::active()->parents()->ordered()->get();

        // Log search
        SearchLog::record($request->q, $products->total(), auth()->id());

        if (!view()->exists('frontend.products.search')) {
            $brands = Brand::active()->ordered()->get();
            return view('frontend.products.index', compact('products', 'categories', 'brands'));
        }

        return view('frontend.products.search', [
            'products' => $products,
            'categories' => $categories,
            'query' => $request->q,
        ]);
    }

    public function recentlyViewed(Request $request)
    {
        $viewerId = auth()->id();
        $viewerIp = (string) $request->ip();

        $viewQuery = ProductView::query()->latest('id');

        if ($viewerId) {
            $viewQuery->where(function ($query) use ($viewerId, $viewerIp): void {
                $query->where('user_id', $viewerId)
                    ->orWhere(function ($guestQuery) use ($viewerIp): void {
                        $guestQuery->whereNull('user_id')
                            ->where('ip_address', $viewerIp);
                    });
            });
        } else {
            $viewQuery->whereNull('user_id')
                ->where('ip_address', $viewerIp);
        }

        $orderedProductIds = $viewQuery->pluck('product_id')
            ->map(fn($productId) => (int) $productId)
            ->unique()
            ->values();

        $products = Product::query()
            ->with(['vendor', 'category', 'brand', 'primaryImage'])
            ->published()
            ->inStock()
            ->whereIn('id', $orderedProductIds->all());

        if ($orderedProductIds->isNotEmpty()) {
            $caseStatement = 'CASE id ' . $orderedProductIds
                ->map(fn(int $id, int $position): string => 'WHEN ' . $id . ' THEN ' . $position)
                ->implode(' ') . ' END';

            $products->orderByRaw($caseStatement);
        }

        $products = $products->paginate(20);

        return view('frontend.products.recently-viewed', compact('products'));
    }

    public function suggestions(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(['items' => []]);
        }

        $products = Product::query()
            ->with('primaryImage')
            ->published()
            ->inStock()
            ->search($query)
            ->orderByDesc('featured')
            ->orderByDesc('sales_count')
            ->limit(8)
            ->get();

        $items = $products->map(function (Product $product): array {
            return [
                'name' => $product->name,
                'url' => route('products.show', $product->slug),
                'image' => $product->primary_image_url,
                'price' => (float) $product->final_price,
                'compare_price' => $product->compare_price ? (float) $product->compare_price : null,
            ];
        })->values();

        return response()->json(['items' => $items]);
    }

    public function quickView(Product $product)
    {
        $product->load(['images', 'variations.attributeValues.attribute', 'vendor']);

        if (!view()->exists('frontend.products.partials.quick-view')) {
            return response()->json([
                'html' => '<div style="padding:12px;"><strong>' . e($product->name) . '</strong></div>',
            ]);
        }

        return response()->json([
            'html' => view('frontend.products.partials.quick-view', compact('product'))->render(),
        ]);
    }
}

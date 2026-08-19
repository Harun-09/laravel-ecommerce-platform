@extends('layouts.app')

@section('content')
    @php
        $selectedBrands = array_values(array_filter((array) request('brand', [])));
        $heroBannerPrimary = $categoryBanners->get(0);
        $heroBannerSecondary = $categoryBanners->get(1);
        $heroProductPrimary = $heroProducts->get(0);
        $heroProductSecondary = $heroProducts->get(1);
        $selectedSort = request('sort', 'best_match');
        $primaryBannerImage = (string) ($heroBannerPrimary->image_url ?? '');
        $secondaryBannerImage = (string) ($heroBannerSecondary->image_url ?? '');
        $primaryBannerUsable = filled($primaryBannerImage) && !str_contains($primaryBannerImage, 'no-banner-image.svg');
        $secondaryBannerUsable = filled($secondaryBannerImage) && !str_contains($secondaryBannerImage, 'no-banner-image.svg');
        $fallbackPromoImages = (array) ($categoryVisualFallback['promo'] ?? []);
        $fallbackCopy = (array) ($categoryVisualFallback['copy'] ?? []);
        $hasCategoryVisualFallback = filled($fallbackPromoImages['main'] ?? null) && filled($fallbackPromoImages['side'] ?? null);
        $fallbackMainSort = $fallbackCopy['main_sort'] ?? 'popular';
        $fallbackSideSort = $fallbackCopy['side_sort'] ?? 'newest';
        $heroPrimaryDescription = trim((string) ($heroProductPrimary?->display_description ?? ''));
        $heroSecondaryDescription = trim((string) ($heroProductSecondary?->display_description ?? ''));
    @endphp

    <div class="container section bb-category">
        <nav class="bb-breadcrumb">
            <a href="{{ route('home') }}">Best Buy</a>
            <span>/</span>
            <a href="{{ route('products.index') }}">All Products</a>
            <span>/</span>
            <span>{{ $category->name }}</span>
        </nav>

        <h1 class="bb-page-title">{{ $category->name }}</h1>

        <section class="bb-hub-strip">
            @foreach($quickLinks as $hubItem)
                <a href="{{ $hubItem['url'] }}" class="bb-hub-item">
                    <span class="bb-hub-thumb">
                        <img src="{{ $hubItem['image'] }}" alt="{{ $hubItem['title'] }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-category-image.svg') }}';">
                    </span>
                    <span class="bb-hub-title">{{ $hubItem['title'] }}</span>
                </a>
            @endforeach
        </section>

        <section class="bb-promo-grid">
            <article class="bb-promo-card bb-promo-main">
                @if($hasCategoryVisualFallback)
                    <img src="{{ $fallbackPromoImages['main'] }}" alt="{{ $category->name }} featured">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">{{ $fallbackCopy['main_kicker'] ?? 'Featured' }}</p>
                        <h2>{{ $fallbackCopy['main_title'] ?? ($category->name . ' deals') }}</h2>
                        <p>{{ $fallbackCopy['main_description'] ?? 'Explore curated products and the latest category offers.' }}</p>
                        <a href="{{ route('products.index', ['category' => $category->slug, 'sort' => $fallbackMainSort]) }}">{{ $fallbackCopy['main_cta'] ?? 'Shop now' }}</a>
                    </div>
                @elseif($primaryBannerUsable)
                    <img src="{{ $heroBannerPrimary->image_url }}" alt="{{ $heroBannerPrimary->title }}">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">Featured</p>
                        <h2>{{ $heroBannerPrimary->title }}</h2>
                        <p>{{ $heroBannerPrimary->subtitle ?: 'Explore latest performance laptops and premium deals.' }}</p>
                        <a href="{{ $heroBannerPrimary->link ?: route('products.index', ['category' => $category->slug]) }}">Learn more</a>
                    </div>
                @elseif($heroProductPrimary)
                    <img src="{{ $heroProductPrimary->primary_image_url }}" alt="{{ $heroProductPrimary->name }}">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">Top Pick</p>
                        <h2>{{ $heroProductPrimary->name }}</h2>
                        <p>{{ \Illuminate\Support\Str::limit($heroPrimaryDescription ?: 'Top-rated picks curated for your daily needs.', 90) }}</p>
                        <a href="{{ route('products.show', $heroProductPrimary->slug) }}">Shop now</a>
                    </div>
                @else
                    <img src="{{ asset('images/placeholders/no-banner-image.svg') }}" alt="{{ $category->name }}">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">Now Live</p>
                        <h2>{{ $category->name }} Collection</h2>
                        <p>Shop verified brands, latest specs, and smart value picks in one place.</p>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}">Browse all</a>
                    </div>
                @endif
            </article>

            <article class="bb-promo-card bb-promo-side">
                @if($hasCategoryVisualFallback)
                    <img src="{{ $fallbackPromoImages['side'] }}" alt="{{ $category->name }} promotion">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">{{ $fallbackCopy['side_kicker'] ?? 'Smart Picks' }}</p>
                        <h2>{{ $fallbackCopy['side_title'] ?? ('New ' . $category->name . ' picks') }}</h2>
                        <p>{{ $fallbackCopy['side_description'] ?? 'Compare products quickly and find your best fit.' }}</p>
                        <a href="{{ route('products.index', ['category' => $category->slug, 'sort' => $fallbackSideSort]) }}">{{ $fallbackCopy['side_cta'] ?? 'Explore' }}</a>
                    </div>
                @elseif($secondaryBannerUsable)
                    <img src="{{ $heroBannerSecondary->image_url }}" alt="{{ $heroBannerSecondary->title }}">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">New Arrivals</p>
                        <h2>{{ $heroBannerSecondary->title }}</h2>
                        <p>{{ $heroBannerSecondary->subtitle ?: 'Powerful devices built for speed and productivity.' }}</p>
                        <a href="{{ $heroBannerSecondary->link ?: route('products.index', ['category' => $category->slug, 'sort' => 'newest']) }}">Explore</a>
                    </div>
                @elseif($heroProductSecondary)
                    <img src="{{ $heroProductSecondary->primary_image_url }}" alt="{{ $heroProductSecondary->name }}">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">Customer Favorite</p>
                        <h2>{{ $heroProductSecondary->name }}</h2>
                        <p>{{ \Illuminate\Support\Str::limit($heroSecondaryDescription ?: 'Reliable category pick for everyday use.', 90) }}</p>
                        <a href="{{ route('products.show', $heroProductSecondary->slug) }}">See product</a>
                    </div>
                @else
                    <img src="{{ asset('images/placeholders/no-banner-image.svg') }}" alt="{{ $category->name }}">
                    <div class="bb-promo-overlay">
                        <p class="bb-promo-kicker">Need Help?</p>
                        <h2>Pick the right {{ $category->name }}</h2>
                        <p>Filter by brand, budget, and ratings to find your best fit quickly.</p>
                        <a href="#category-results">View options</a>
                    </div>
                @endif
            </article>
        </section>

        <section class="bb-results-wrap" id="category-results">
            <aside class="bb-filters">
                <form id="category-filter-form" action="{{ route('category.show', $category->slug) }}" method="GET">
                    <h3>Filters</h3>

                    @if($subcategories->isNotEmpty())
                        <div class="bb-filter-block">
                            <h4>Subcategories</h4>
                            <div class="bb-pill-grid">
                                @foreach($subcategories as $subCategory)
                                    <a href="{{ route('category.show', $subCategory->slug) }}" class="bb-sub-pill">{{ $subCategory->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="bb-filter-block">
                        <h4>Brands</h4>
                        <div class="bb-check-list">
                            @forelse($brands->take(14) as $brand)
                                <label>
                                    <input type="checkbox" name="brand[]" value="{{ $brand->slug }}"
                                        {{ in_array($brand->slug, $selectedBrands, true) ? 'checked' : '' }}>
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @empty
                                <p class="bb-empty-note">No brands available.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bb-filter-block">
                        <h4>Price Range</h4>
                        <div class="bb-price-row">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max">
                        </div>
                    </div>

                    <div class="bb-filter-block">
                        <h4>Rating</h4>
                        <div class="bb-check-list">
                            @for($rating = 4; $rating >= 1; $rating--)
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="radio" name="rating" value="{{ $rating }}"
                                        {{ (string) request('rating') === (string) $rating ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #0046be;">
                                    <span>
                                        <span style="color: #facc15; margin-right: 4px;">
                                            @for($j = 1; $j <= 5; $j++)
                                                <i class="fas fa-star" style="color: {{ $j <= $rating ? '#facc15' : '#d1d5db' }};"></i>
                                            @endfor
                                        </span>
                                        <span style="font-size: 13px; color: #475569;">{{ $rating }} & up</span>
                                    </span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div class="bb-filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('category.show', $category->slug) }}" style="display: block; text-align: center; margin-top: 12px; font-size: 14px; color: #1d4f9a; font-weight: 600;">Clear all</a>
                    </div>
                </form>
            </aside>

            <div class="bb-results-main">
                <div class="bb-results-toolbar">
                    <div>
                        <p class="bb-results-title">{{ $products->total() }} results for {{ $category->name }}</p>
                        <p class="bb-results-subtitle">Shop trusted brands and compare what matters most.</p>
                    </div>

                    <label class="bb-sort-select">
                        <span>Sort by</span>
                        <select id="category-sort" data-category-url="{{ route('category.show', $category->slug) }}">
                            <option value="best_match" {{ $selectedSort === 'best_match' ? 'selected' : '' }}>Best Match</option>
                            <option value="newest" {{ $selectedSort === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="popular" {{ $selectedSort === 'popular' ? 'selected' : '' }}>Best Selling</option>
                            <option value="rating" {{ $selectedSort === 'rating' ? 'selected' : '' }}>Top Rated</option>
                            <option value="price_low" {{ $selectedSort === 'price_low' ? 'selected' : '' }}>Price Low to High</option>
                            <option value="price_high" {{ $selectedSort === 'price_high' ? 'selected' : '' }}>Price High to Low</option>
                        </select>
                    </label>
                </div>

                @if($products->isEmpty())
                    <div class="bb-empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try changing your filters or browse another subcategory.</p>
                    </div>
                @else
                    <div class="grid grid-4">
                        @foreach($products as $product)
                            @include('frontend.products.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <div class="bb-pagination">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    @php
        $isDealOfTheDay = request('deal') === 'today';
        $listingTitle = $isDealOfTheDay ? 'Deal of the Day' : 'All Products';
        $listingCountLabel = $isDealOfTheDay ? 'deals' : 'products';
        $clearFiltersUrl = $isDealOfTheDay ? route('products.index', ['deal' => 'today']) : route('products.index');
        $emptyTitle = $isDealOfTheDay ? 'No deals available right now' : 'No products found';
        $emptyDescription = $isDealOfTheDay
            ? 'Please check back later for currently active flash sale deals.'
            : 'Try adjusting your filters or search terms';
    @endphp

    <div class="container section bb-category">
        <nav class="bb-breadcrumb">
            <a href="{{ route('home') }}">Best Buy</a>
            <span>/</span>
            <span>{{ $listingTitle }}</span>
        </nav>

        <section class="bb-results-wrap" id="category-results">
            <!-- Sidebar Filters -->
            <aside class="bb-filters">
                <form action="{{ route('products.index') }}" method="GET">
                    <h3>Filters</h3>
                    @if($isDealOfTheDay)
                        <input type="hidden" name="deal" value="today">
                    @endif

                    <!-- Categories -->
                    <div class="bb-filter-block">
                        <h4>Categories</h4>
                        <div class="bb-check-list">
                            @foreach($categories as $category)
                                <label>
                                    <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'checked' : '' }}>
                                    <span>{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="bb-filter-block">
                        <h4>Brands</h4>
                        <div class="bb-check-list">
                            @foreach($brands->take(10) as $brand)
                                <label>
                                    <input type="checkbox" name="brand[]" value="{{ $brand->slug }}" {{ in_array($brand->slug, (array) request('brand', [])) ? 'checked' : '' }}>
                                    <span>{{ $brand->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="bb-filter-block">
                        <h4>Price Range</h4>
                        <div class="bb-price-row">
                            <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                            <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="bb-filter-block">
                        <h4>Rating</h4>
                        <div class="bb-check-list">
                            @for($i = 4; $i >= 1; $i--)
                                <label>
                                    <input type="radio" name="rating" value="{{ $i }}" {{ request('rating') == $i ? 'checked' : '' }}>
                                    <span>
                                        <span style="color: #facc15; margin-right: 4px;">
                                            @for($j = 1; $j <= 5; $j++)
                                                <i class="fas fa-star" style="color: {{ $j <= $i ? '#facc15' : '#d1d5db' }};"></i>
                                            @endfor
                                        </span>
                                        {{ $i }} & up
                                    </span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <div class="bb-filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ $clearFiltersUrl }}">Clear All</a>
                    </div>
                </form>
            </aside>

            <!-- Products Grid -->
            <div class="bb-results-main">
                <!-- Header -->
                <div class="bb-results-toolbar">
                    <div>
                        <p class="bb-results-title">{{ $products->total() }} {{ $listingCountLabel }}</p>
                        <p class="bb-results-subtitle">{{ $listingTitle }}</p>
                    </div>

                    <label class="bb-sort-select">
                        <span>Sort by</span>
                        <select onchange="window.location.href='{{ route('products.index') }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value})">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Best Rating</option>
                        </select>
                    </label>
                </div>

                @if($products->isEmpty())
                    <div class="bb-empty-state">
                        <i class="fas fa-search"></i>
                        <h3>{{ $emptyTitle }}</h3>
                        <p>{{ $emptyDescription }}</p>
                    </div>
                @else
                    <div class="grid grid-4">
                        @foreach($products as $product)
                            @include('frontend.products.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="bb-pagination">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@php
    $listingDescription = \Illuminate\Support\Str::limit($product->display_description, 130);
    $productUrl = route('products.show', $product->slug);
@endphp

<div class="product-card" data-product-url="{{ $productUrl }}" role="link" tabindex="0"
    aria-label="View {{ $product->name }}">

    @if($product->discount_percentage)
        <div class="badge">-{{ $product->discount_percentage }}%</div>
    @endif

    <a href="{{ $productUrl }}">
        <img src="{{ $product->listing_image_url }}" alt="{{ $product->name }}" loading="lazy"
            onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-product-image.svg';">
    </a>

    <div class="content">
        <div class="vendor">{{ $product->vendor->shop_name ?? 'NovaMart' }}</div>
        <h3><a href="{{ $productUrl }}">{{ $product->name }}</a></h3>
        <p class="desc">{{ $listingDescription }}</p>

        <div class="price">
            <span class="current">{{ store_money($product->final_price) }}</span>
            @if($product->compare_price && $product->compare_price > $product->price)
                <span class="old">{{ store_money($product->compare_price) }}</span>
            @endif
        </div>

        @if($product->rating > 0)
            <div class="rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star{{ $i <= $product->rating ? '' : '-half-alt' }}"
                        style="color: {{ $i <= $product->rating ? '#facc15' : '#d1d5db' }};"></i>
                @endfor
                <span>({{ $product->reviews_count }})</span>
            </div>
        @endif

        <div class="actions">
            <button type="button" class="add-cart"
                onclick="event.preventDefault();event.stopPropagation();addToCart({{ $product->id }})">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
            <button type="button" class="wishlist"
                onclick="event.preventDefault();event.stopPropagation();toggleWishlist({{ $product->id }}, this)">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
</div>

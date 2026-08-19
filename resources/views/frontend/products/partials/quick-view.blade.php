@php
    $quickDescription = \Illuminate\Support\Str::limit($product->display_description, 170);
@endphp

<div style="display: grid; grid-template-columns: 140px 1fr; gap: 16px;">
    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
        style="width: 140px; height: 140px; object-fit: cover; border-radius: 8px;">

    <div>
        <p style="font-size: 12px; color: #6b7280; margin-bottom: 6px;">
            {{ $product->vendor->shop_name ?? 'NovaMart' }}
        </p>
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">{{ $product->name }}</h3>

        <p style="font-size: 20px; font-weight: 700; color: #6366f1; margin-bottom: 12px;">
            {{ store_money($product->final_price) }}
        </p>

        @if($quickDescription !== '')
            <p style="font-size: 14px; color: #4b5563; margin-bottom: 14px;">
                {{ $quickDescription }}
            </p>
        @endif

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline" style="padding: 8px 12px;">
                View Details
            </a>
            <button type="button" class="btn btn-primary" style="padding: 8px 12px;"
                onclick="addToCart({{ $product->id }})">
                Add to Cart
            </button>
        </div>
    </div>
</div>


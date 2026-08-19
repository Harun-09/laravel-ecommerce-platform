@extends('layouts.app')

@section('content')
    <div class="container section">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700;">Search Results</h1>
                <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">
                    "{{ $query }}" এর জন্য {{ $products->total() }} টি product পাওয়া গেছে
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline">All Products</a>
        </div>

        <div style="display: flex; gap: 30px;">
            <aside style="width: 280px; flex-shrink: 0;">
                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Filter by Category</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', ['q' => $query, 'category' => $category->slug]) }}"
                                style="color: #4b5563; font-size: 14px;">
                                <i class="fas fa-angle-right" style="margin-right: 6px; color: #9ca3af;"></i>{{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            <div style="flex: 1;">
                @if($products->isEmpty())
                    <div style="text-align: center; padding: 60px 20px;">
                        <i class="fas fa-search" style="font-size: 64px; color: #d1d5db; margin-bottom: 20px;"></i>
                        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">No products found</h3>
                        <p style="color: #6b7280;">Try different keywords or check spelling.</p>
                    </div>
                @else
                    <div class="grid grid-4">
                        @foreach($products as $product)
                            @include('frontend.products.partials.product-card', ['product' => $product])
                        @endforeach
                    </div>

                    <div style="margin-top: 40px; display: flex; justify-content: center;">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container section">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700;">{{ __('Recently Viewed') }}</h1>
                <p style="color: #6b7280; font-size: 14px; margin-top: 4px;">
                    {{ __('Products you visited most recently.') }}
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline">{{ __('All Products') }}</a>
        </div>

        @if($products->isEmpty())
            <div style="text-align: center; padding: 64px 20px; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px;">
                <i class="fas fa-clock-rotate-left" style="font-size: 54px; color: #94a3b8; margin-bottom: 14px;"></i>
                <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">{{ __('No recently viewed products yet') }}</h3>
                <p style="color: #6b7280; margin-bottom: 20px;">{{ __('Browse products and they will appear here automatically.') }}</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">{{ __('Start Shopping') }}</a>
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
@endsection

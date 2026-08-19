@extends('layouts.app')

@section('content')
    <div class="container section">
        <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 24px;">My Wishlist</h1>

        @if($wishlist->isEmpty())
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 12px;">
                <i class="fas fa-heart" style="font-size: 80px; color: #d1d5db; margin-bottom: 24px;"></i>
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 12px;">Your wishlist is empty</h2>
                <p style="color: #6b7280; margin-bottom: 24px;">Save products you like by clicking the heart icon.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Explore Products</a>
            </div>
        @else
            <div class="grid grid-5">
                @foreach($wishlist as $item)
                    @include('frontend.products.partials.product-card', ['product' => $item->product])
                @endforeach
            </div>

            <div style="margin-top: 40px; display: flex; justify-content: center;">
                {{ $wishlist->links() }}
            </div>
        @endif
    </div>
@endsection
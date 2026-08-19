@extends('layouts.app')

@section('content')
<!-- Hero Banner -->
@php
    $heroSlides = $banners->take(8)->values();
    if ($heroSlides->count() > 0 && $heroSlides->count() < 5) {
        $seedSlides = $heroSlides->values();
        $duplicateIndex = 0;
        while ($heroSlides->count() < 5) {
            $heroSlides->push($seedSlides[$duplicateIndex % $seedSlides->count()]);
            $duplicateIndex++;
        }
    }
    $heroSlideBackgrounds = [
        'linear-gradient(120deg, #77dede 0%, #7ccff8 100%)',
        'linear-gradient(120deg, #f8faff 0%, #e2e8f8 100%)',
        'linear-gradient(120deg, #5f82e8 0%, #7054ce 100%)',
        'linear-gradient(120deg, #e9f2ff 0%, #d9e6ff 100%)',
    ];
@endphp

<section class="hero-banner-pick" id="hero-banner-pick">
    <div class="hero-banner-pick__slider" data-hero-slider>
        @forelse($heroSlides as $index => $slide)
            @php
                $slideBackground = $heroSlideBackgrounds[$index % count($heroSlideBackgrounds)];
            @endphp
            <article class="hero-banner-pick__slide {{ $index === 0 ? 'is-active' : '' }}"
                style="--hero-slide-bg: {{ $slideBackground }};"
                data-hero-slide>
                <a class="hero-banner-pick__link" href="{{ $slide->link ?: route('products.index') }}">
                    <div class="container hero-banner-pick__content">
                        <picture class="hero-banner-pick__media">
                            @if($slide->mobile_image_url)
                                <source media="(max-width: 768px)" srcset="{{ $slide->mobile_image_url }}">
                            @endif
                            <img src="{{ $slide->image_url }}"
                                alt="{{ $slide->title ?: 'Promotional banner' }}"
                                draggable="false"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        </picture>
                    </div>
                </a>
            </article>
        @empty
            <article class="hero-banner-pick__slide is-active"
                style="--hero-slide-bg: linear-gradient(120deg, #77dede 0%, #7ccff8 100%);"
                data-hero-slide>
                <a class="hero-banner-pick__link" href="{{ route('products.index') }}">
                    <div class="container hero-banner-pick__content">
                        <div class="hero-banner-pick__fallback">
                            <h2>{{ __('Discover Amazing Products') }}</h2>
                            <p>{{ __('Latest offers and top brands in one place.') }}</p>
                        </div>
                    </div>
                </a>
            </article>
        @endforelse
    </div>

    @if($heroSlides->count() > 1)
        <button type="button" class="hero-banner-pick__nav hero-banner-pick__nav--prev" data-hero-prev
            aria-label="{{ __('Previous banner') }}">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="hero-banner-pick__nav hero-banner-pick__nav--next" data-hero-next
            aria-label="{{ __('Next banner') }}">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="hero-banner-pick__dots" data-hero-dots>
            @foreach($heroSlides as $index => $slide)
                <button type="button"
                    class="hero-banner-pick__dot {{ $index === 0 ? 'is-active' : '' }}"
                    data-hero-dot="{{ $index }}"
                    aria-label="{{ __('Go to banner') }} {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>

<!-- Flash Sale -->
@if($flashSale)
<section class="section" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 40px 0;">
    <div class="container">
        <div class="section-title" style="margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <h2 style="color: #92400e;"><i class="fas fa-bolt"></i> {{ __('Flash Sale') }}</h2>
                <div style="background: #92400e; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600;">
                    {{ __('Ends in:') }} <span id="flash-timer">{{ gmdate("H:i:s", $flashSale->time_remaining ?? 0) }}</span>
                </div>
            </div>
            <a href="#" style="color: #92400e;">{{ __('View All') }}</a>
        </div>
        
        <div class="grid grid-5">
            @foreach($flashSale->products as $product)
                @include('frontend.products.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Categories -->
<section class="section" style="padding-top: 28px; padding-bottom: 8px;">
    <div class="container">
        <div class="section-title">
            <h2>{{ __('Shop By Category') }}</h2>
            <a href="{{ route('products.index') }}">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 14px;">
            @foreach($categories->take(8) as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                    style="text-align: center; padding: 20px 12px; background: white; border-radius: 12px; transition: all 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div
                        style="width: 56px; height: 56px; border-radius: 50%; overflow: hidden; margin: 0 auto 10px; box-shadow: 0 3px 10px rgba(99, 102, 241, 0.24);">
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                            style="width: 100%; height: 100%; object-fit: cover;"
                            onerror="this.onerror=null;this.src='{{ asset('images') }}/placeholders/no-category-image.svg';">
                    </div>
                    <span style="font-weight: 500; font-size: 13px; color: #374151;">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Showcase Category Collections -->
@foreach(($homeShowcaseGroups ?? []) as $showcaseGroup)
    <section class="section home-showcase-group">
        <div class="container">
            <div class="section-title">
                <h2>{{ $showcaseGroup['title'] }}</h2>
                <a href="{{ $showcaseGroup['view_all_url'] }}">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="home-showcase-grid">
                @foreach($showcaseGroup['items'] as $showcaseItem)
                    <a href="{{ $showcaseItem['url'] }}" class="home-showcase-card">
                        <div class="home-showcase-card__image-wrap">
                            <img src="{{ $showcaseItem['image'] }}" alt="{{ $showcaseItem['title'] }}" loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholders/no-category-image.svg') }}';">
                        </div>
                        <div class="home-showcase-card__body">
                            <span class="home-showcase-card__subtitle">{{ $showcaseItem['subtitle'] }}</span>
                            <span class="home-showcase-card__title">{{ $showcaseItem['title'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endforeach

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-star" style="color: #f59e0b;"></i> {{ __('Featured Products') }}</h2>
            <a href="{{ route('products.index', ['featured' => 1]) }}">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="grid grid-5">
            @foreach($featuredProducts as $product)
                @include('frontend.products.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- Logical Home Merchandising Sections -->
@foreach(($homeProductSections ?? []) as $productSection)
    <section class="section home-merch-section">
        <div class="container">
            <div class="section-title">
                <h2>{{ $productSection['title'] }}</h2>
                <a href="{{ $productSection['view_all_url'] }}">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="grid grid-5">
                @foreach($productSection['products'] as $product)
                    @include('frontend.products.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
@endforeach

<!-- Services Banner -->
<section style="background: #f3f4f6; padding: 40px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
            <div style="background: white; padding: 24px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 60px; height: 60px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 24px;">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 4px;">{{ __('Fast Delivery') }}</h4>
                    <p style="font-size: 13px; color: #6b7280;">{{ __('Delivery within 2-3 days') }}</p>
                </div>
            </div>
            
            <div style="background: white; padding: 24px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 60px; height: 60px; background: #dcfce7; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 24px;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 4px;">{{ __('Secure Payment') }}</h4>
                    <p style="font-size: 13px; color: #6b7280;">{{ __('100% secure payment') }}</p>
                </div>
            </div>
            
            <div style="background: white; padding: 24px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 60px; height: 60px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 24px;">
                    <i class="fas fa-undo"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 4px;">{{ __('Easy Returns') }}</h4>
                    <p style="font-size: 13px; color: #6b7280;">{{ __('7 days return policy') }}</p>
                </div>
            </div>
            
            <div style="background: white; padding: 24px; border-radius: 12px; display: flex; align-items: center; gap: 16px;">
                <div style="width: 60px; height: 60px; background: #f3e8ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #9333ea; font-size: 24px;">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 4px;">{{ __('24/7 Support') }}</h4>
                    <p style="font-size: 13px; color: #6b7280;">{{ __('Dedicated customer support') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-sparkles" style="color: #6366f1;"></i> {{ __('New Arrivals') }}</h2>
            <a href="{{ route('products.index', ['sort' => 'latest']) }}">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="grid grid-5">
            @foreach($newArrivals as $product)
                @include('frontend.products.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- Best Sellers -->
<section class="section" style="background: #f9fafb;">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-fire" style="color: #ef4444;"></i> {{ __('Best Sellers') }}</h2>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}">{{ __('View All') }} <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="grid grid-5">
            @foreach($bestSellers as $product)
                @include('frontend.products.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- Newsletter -->
<section style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); padding: 60px 0;">
    <div class="container" style="text-align: center; color: white;">
        <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">{{ __('Subscribe to Our Newsletter') }}</h2>
        <p style="opacity: 0.9; margin-bottom: 24px;">{{ __('Get exclusive offers, new arrivals, and more delivered to your inbox!') }}</p>
        <form id="home-newsletter-form" action="{{ route('deals.subscribe') }}" method="POST" style="max-width: 500px; margin: 0 auto; display: flex; gap: 12px; justify-content: center;">
            @csrf
            <input type="hidden" name="source" value="home_newsletter">
            <div id="home-newsletter-email-wrap" style="flex: 1; {{ ($newsletterAccountSubscribed ?? false) ? 'display: none;' : '' }}">
                <input type="email"
                    id="home-newsletter-email"
                    name="email"
                    value=""
                    required
                    autocomplete="email"
                    placeholder="{{ __('Enter your email address') }}"
                    style="width: 100%; padding: 16px 24px; border: none; border-radius: 50px; font-size: 15px;">
            </div>
            <button id="home-newsletter-submit"
                type="submit"
                data-subscribed="{{ ($newsletterAccountSubscribed ?? false) ? '1' : '0' }}"
                {{ ($newsletterAccountSubscribed ?? false) ? 'disabled' : '' }}
                class="btn btn-secondary"
                style="border-radius: 50px; padding: 16px 32px;">{{ ($newsletterAccountSubscribed ?? false) ? __('Subscribed') : __('Subscribe') }}</button>
        </form>
        @error('email')
            <p style="margin-top: 12px; font-size: 14px; color: #fde68a;">{{ $message }}</p>
        @enderror
    </div>
</section>
@endsection


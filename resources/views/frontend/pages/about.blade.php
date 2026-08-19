@extends('layouts.app')

@section('content')
    @php
        $summary = $page->meta_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($page->content)), 210);
    @endphp

    <x-frontend.page-hero
        theme="about"
        eyebrow="NovaMart Story"
        :title="$page->title"
        :summary="$summary"
        :tags="['Trusted Sellers', 'Secure Checkout', 'Fast Delivery']" />

    <section class="section static-page-body">
        <div class="container">
            <div class="about-layout">
                <article class="card about-content">
                    {!! $page->content !!}
                </article>

                <aside class="about-side">
                    <div class="card about-side__card">
                        <h3>Shop With Confidence</h3>
                        <p>Every order is protected with secure payments, verified sellers, and dedicated support.</p>
                    </div>

                    <div class="card about-side__card">
                        <h3>Built For Bangladesh</h3>
                        <p>From daily essentials to lifestyle picks, NovaMart helps customers and local businesses grow together.</p>
                    </div>

                    <div class="card about-side__cta">
                        <h3>Start Shopping</h3>
                        <p>Explore top categories and discover your next favorite product.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Browse Products</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

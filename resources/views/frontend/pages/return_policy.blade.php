@extends('layouts.app')

@section('content')
    @php
        $summary = $page->meta_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($page->content)), 260);
    @endphp

    <x-frontend.page-hero
        theme="return"
        eyebrow="Returns & Refunds"
        :title="$page->title"
        :summary="$summary"
        :tags="['Eligibility Rules', 'Refund Timeline', 'Support Assistance']" />

    <section class="section static-page-body">
        <div class="container">
            <div class="return-layout">
                <article class="card return-content">
                    {!! $page->content !!}
                </article>

                <aside class="return-side">
                    <div class="card return-side__card">
                        <h3>Return Window</h3>
                        <p>Submit your return request quickly after delivery to ensure smooth processing.</p>
                    </div>

                    <div class="card return-side__card">
                        <h3>Need Return Help?</h3>
                        <p>Our support team can guide you through eligibility checks and refund tracking.</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">Contact Support</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

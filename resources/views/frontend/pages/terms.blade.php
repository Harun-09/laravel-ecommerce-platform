@extends('layouts.app')

@section('content')
    @php
        $summary = $page->meta_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($page->content)), 260);
    @endphp

    <x-frontend.page-hero
        theme="terms"
        eyebrow="Legal Information"
        :title="$page->title"
        :summary="$summary"
        :tags="['Use of Website', 'Account & Orders', 'Shipping & Delivery']" />

    <section class="section static-page-body">
        <div class="container">
            <div class="terms-layout">
                <article class="card terms-content">
                    {!! $page->content !!}
                </article>

                <aside class="terms-side">
                    <div class="card terms-side__card">
                        <h3>Quick Note</h3>
                        <p>By continuing to use NovaMart, you agree to these terms and any applicable policies.</p>
                    </div>

                    <div class="card terms-side__card">
                        <h3>Need Help?</h3>
                        <p>If any section is unclear, contact our support team and we will assist you.</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">Contact Support</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

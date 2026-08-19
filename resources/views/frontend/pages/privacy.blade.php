@extends('layouts.app')

@section('content')
    @php
        $summary = $page->meta_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($page->content)), 260);
    @endphp

    <x-frontend.page-hero
        theme="privacy"
        eyebrow="Data Protection"
        :title="$page->title"
        :summary="$summary"
        :tags="['Information We Collect', 'How We Use Data', 'Security Practices']" />

    <section class="section static-page-body">
        <div class="container">
            <div class="privacy-layout">
                <article class="card privacy-content">
                    {!! $page->content !!}
                </article>

                <aside class="privacy-side">
                    <div class="card privacy-side__card">
                        <h3>Your Privacy Matters</h3>
                        <p>We protect customer data with secure systems and strict access controls across the platform.</p>
                    </div>

                    <div class="card privacy-side__card">
                        <h3>Questions?</h3>
                        <p>If you need clarification about data use or account privacy, contact support.</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">Contact Support</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection

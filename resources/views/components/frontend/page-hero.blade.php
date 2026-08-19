@props([
    'theme' => 'about',
    'eyebrow' => '',
    'title' => '',
    'summary' => '',
    'tags' => [],
])

@php
    $tagItems = collect($tags)->map(fn($tag) => trim((string) $tag))->filter()->values();
@endphp

<section class="static-page-hero static-page-hero--{{ $theme }} section">
    <div class="container">
        <div class="static-page-hero__panel">
            @if($eyebrow !== '')
                <p class="static-page-hero__eyebrow">{{ $eyebrow }}</p>
            @endif
            <h1>{{ $title }}</h1>
            @if($summary !== '')
                <p>{{ $summary }}</p>
            @endif

            @if($tagItems->isNotEmpty())
                <div class="static-page-hero__chips">
                    @foreach($tagItems as $tag)
                        <span>{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="bb-pagination-nav">
        
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            {{-- Hide previous button when on first page --}}
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="bb-pagination-btn">
                &laquo; {!! __('Previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="bb-pagination-btn bb-pagination-btn-next">
                {!! __('Next') !!} &raquo;
            </a>
        @else
            {{-- Hide next button when no more pages --}}
        @endif

    </nav>
@endif

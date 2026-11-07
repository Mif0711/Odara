@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="page-btn is-disabled" aria-disabled="true">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-btn" rel="prev" aria-label="Halaman sebelumnya">
                <svg class="icon"><use href="#i-chevron-left"></use></svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-ellipsis">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-btn is-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-btn" rel="next" aria-label="Halaman berikutnya">
                <svg class="icon"><use href="#i-chevron-right"></use></svg>
            </a>
        @else
            <span class="page-btn is-disabled" aria-disabled="true">
                <svg class="icon"><use href="#i-chevron-right"></use></svg>
            </span>
        @endif
    </nav>
@endif

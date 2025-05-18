@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $startPage = max(1, min($currentPage - 2, $lastPage - 4));
    $endPage = min($lastPage, max(5, $currentPage + 2));
    $showStartEllipsis = $startPage > 1;
    $showEndEllipsis = $endPage < $lastPage;
@endphp

<div class="custom-pagination">
    @if ($paginator->onFirstPage())
        <span class="pagination-arrow disabled">
            <i class="fas fa-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-arrow">
            <i class="fas fa-chevron-left"></i>
        </a>
    @endif

    @if ($showStartEllipsis)
        <a href="{{ $paginator->url(1) }}" class="pagination-number {{ $currentPage == 1 ? 'active' : '' }}">1</a>
        <span class="pagination-ellipsis">...</span>
    @endif

    @for ($i = $startPage; $i <= $endPage; $i++)
        <a href="{{ $paginator->url($i) }}" class="pagination-number {{ $currentPage == $i ? 'active' : '' }}">
            {{ $i }}
        </a>
    @endfor

    @if ($showEndEllipsis)
        <span class="pagination-ellipsis">...</span>
        <a href="{{ $paginator->url($lastPage) }}" class="pagination-number {{ $currentPage == $lastPage ? 'active' : '' }}">
            {{ $lastPage }}
        </a>
    @endif

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-arrow">
            <i class="fas fa-chevron-right"></i>
        </a>
    @else
        <span class="pagination-arrow disabled">
            <i class="fas fa-chevron-right"></i>
        </span>
    @endif
</div>
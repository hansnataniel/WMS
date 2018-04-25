@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            @if (($paginator->currentPage() - $i == 7) OR ($i - $paginator->currentPage() == 7))
                <li class="pagination-item pagination-dot pagination-disabled">
                    <span>...</span>
                </li>
            @endif

            @if ($i == $paginator->currentPage())
                <li class="pagination-item active pagination-disabled">
                    <span>{{$i}}</span>
                </li>
            @elseif ((($paginator->currentPage() > $i) AND ($paginator->currentPage() - $i < 7)) OR (($i > $paginator->currentPage()) AND ($i - $paginator->currentPage() < 7)))
                <li class="pagination-item pagination-active">
                    <a href="{{$paginator->url($i)}}">{{$i}}</a>
                </li>
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="disabled"><span>&raquo;</span></li>
        @endif
    </ul>
@endif

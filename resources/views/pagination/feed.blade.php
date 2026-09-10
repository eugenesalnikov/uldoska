{{-- resources/views/pagination/feed.blade.php --}}
@if ($paginator->hasPages())
  <nav class="pager" aria-label="Страницы">
    @if ($paginator->onFirstPage())
      <span class="pager-btn is-disabled">Назад</span>
    @else
      <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">Назад</a>
    @endif

    <span class="pager-meta">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
            из {{ $paginator->total() }}
        </span>

    @if ($paginator->hasMorePages())
      <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Вперёд</a>
    @else
      <span class="pager-btn is-disabled">Вперёд</span>
    @endif
  </nav>
@endif

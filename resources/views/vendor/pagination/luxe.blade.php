@if ($paginator->hasPages())
  <nav class="w-full flex flex-col items-center gap-3" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    <p class="text-xs font-label-caps text-label-caps text-on-surface-variant uppercase tracking-[0.25em]">
      {{ __('Showing :from to :to of :total', ['from' => $paginator->firstItem() ?? 0, 'to' => $paginator->lastItem() ?? $paginator->count(), 'total' => $paginator->total()]) }}
    </p>

    <div class="inline-flex items-center gap-2 border border-outline-variant bg-surface-container-lowest px-2 py-2">
      @if ($paginator->onFirstPage())
        <span class="px-3 py-2 text-xs font-semibold uppercase tracking-widest text-on-surface-variant/60 cursor-not-allowed">
          {{ __('Prev') }}
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 text-xs font-semibold uppercase tracking-widest text-primary hover:bg-surface-container-high">
          {{ __('Prev') }}
        </a>
      @endif

      <div class="flex items-center gap-1">
        @foreach ($elements as $element)
          @if (is_string($element))
            <span class="px-2 py-2 text-xs text-on-surface-variant">{{ $element }}</span>
          @endif

          @if (is_array($element))
            @foreach ($element as $page => $url)
              @if ($page == $paginator->currentPage())
                <span aria-current="page" class="px-3 py-2 text-xs font-semibold uppercase tracking-widest bg-primary text-on-primary">
                  {{ $page }}
                </span>
              @else
                <a href="{{ $url }}" class="px-3 py-2 text-xs font-semibold uppercase tracking-widest text-primary hover:bg-surface-container-high" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                  {{ $page }}
                </a>
              @endif
            @endforeach
          @endif
        @endforeach
      </div>

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 text-xs font-semibold uppercase tracking-widest text-primary hover:bg-surface-container-high">
          {{ __('Next') }}
        </a>
      @else
        <span class="px-3 py-2 text-xs font-semibold uppercase tracking-widest text-on-surface-variant/60 cursor-not-allowed">
          {{ __('Next') }}
        </span>
      @endif
    </div>
  </nav>
@endif


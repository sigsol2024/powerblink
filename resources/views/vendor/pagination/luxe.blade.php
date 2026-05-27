@if ($paginator->hasPages())
  <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    @if ($paginator->onFirstPage())
      <span class="text-sm text-on-surface-variant/50 select-none">{{ __('Previous') }}</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="text-sm font-medium text-primary hover:underline underline-offset-4">
        {{ __('Previous') }}
      </a>
    @endif

    <div class="flex items-center gap-2">
      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="px-1 text-on-surface-variant">…</span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="min-w-[2rem] h-8 inline-flex items-center justify-center text-sm font-semibold text-on-primary bg-primary rounded-full" aria-current="page">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="min-w-[2rem] h-8 inline-flex items-center justify-center text-sm text-on-surface-variant hover:text-primary transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach
    </div>

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="text-sm font-medium text-primary hover:underline underline-offset-4">
        {{ __('Next') }}
      </a>
    @else
      <span class="text-sm text-on-surface-variant/50 select-none">{{ __('Next') }}</span>
    @endif
  </nav>
  <p class="mt-4 text-center text-xs text-on-surface-variant">
    {{ __(':total results', ['total' => $paginator->total()]) }}
  </p>
@endif

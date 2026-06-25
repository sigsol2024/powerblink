@props([
    'title' => null,
    'subtitle' => null,
    'count' => null,
])

@if (isset($actions) || $subtitle || $count)
  <div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4']) }}>
    @if ($subtitle || $count)
      <div class="min-w-0">
        @if ($subtitle)
          <p class="text-sm text-on-surface-variant">{{ $subtitle }}</p>
        @endif
        @if ($count !== null && $count !== '')
          <span class="text-xs text-on-surface-variant">{{ $count }}</span>
        @endif
      </div>
    @endif
    @isset($actions)
      <div class="flex flex-wrap items-center gap-2 shrink-0">
        {{ $actions }}
      </div>
    @endisset
  </div>
@endif

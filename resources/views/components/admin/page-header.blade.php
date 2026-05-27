@props([
    'title',
    'subtitle' => null,
    'count' => null,
])

<header {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white sticky top-0 z-40 shrink-0']) }}>
  <div class="flex items-center gap-3 min-w-0 flex-wrap">
    <div class="min-w-0">
      <h2 class="text-lg font-semibold text-wp-text">{{ $title }}</h2>
      @if ($subtitle)
        <p class="text-wp-text-muted text-xs mt-0.5">{{ $subtitle }}</p>
      @endif
    </div>
    @if ($count !== null && $count !== '')
      <span class="text-xs text-wp-text-muted shrink-0">{{ $count }}</span>
    @endif
  </div>
  @isset($actions)
    <div class="shrink-0 flex flex-wrap items-center gap-2">
      {{ $actions }}
    </div>
  @endisset
</header>

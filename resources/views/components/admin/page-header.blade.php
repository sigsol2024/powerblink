@props([
    'backHref' => null,
    'backLabel' => null,
    'subtitle' => null,
    'count' => null,
])

@php
  $hasToolbar = isset($actions) || $backHref || $subtitle || ($count !== null && $count !== '');
@endphp

@if ($hasToolbar)
  <div {{ $attributes->merge(['class' => 'flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4 md:mb-6']) }}>
    <div class="flex flex-wrap items-center gap-3 min-w-0">
      @if ($backHref)
        <a href="{{ $backHref }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-on-surface-variant hover:text-secondary transition-colors min-h-11">
          <x-icon name="chevron_left" class="w-4 h-4" />
          {{ $backLabel ?? __('Back') }}
        </a>
      @endif
      @if ($subtitle || ($count !== null && $count !== ''))
        <div class="min-w-0">
          @if ($subtitle)
            <p class="text-sm text-on-surface-variant">{{ $subtitle }}</p>
          @endif
          @if ($count !== null && $count !== '')
            <span class="text-xs text-on-surface-variant">{{ $count }}</span>
          @endif
        </div>
      @endif
    </div>
    @isset($actions)
      <div class="flex flex-wrap items-center gap-2 shrink-0">
        {{ $actions }}
      </div>
    @endisset
  </div>
@endif

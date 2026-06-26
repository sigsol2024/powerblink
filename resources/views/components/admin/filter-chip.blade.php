@props([
    'active' => false,
    'count' => null,
])

@php
  $classes = $active
      ? 'bg-secondary text-on-secondary border-secondary'
      : 'bg-surface-container-lowest text-on-surface border-outline-variant hover:bg-surface-container-low';
@endphp

<button
  type="button"
  {{ $attributes->merge(['class' => 'px-3 py-1.5 text-sm border rounded-full font-semibold transition-colors whitespace-nowrap min-h-11 '.$classes]) }}
>
  {{ $slot }}@if ($count !== null)<span class="ml-1 opacity-80">({{ $count }})</span>@endif
</button>

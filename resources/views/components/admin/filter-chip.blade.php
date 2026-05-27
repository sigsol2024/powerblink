@props([
    'active' => false,
    'count' => null,
])

@php
  $classes = $active
      ? 'bg-wp-link text-white border-wp-link'
      : 'bg-white text-wp-text border-wp-border hover:bg-wp-bg';
@endphp

<button
  type="button"
  {{ $attributes->merge(['class' => 'px-3 py-1.5 text-sm border rounded transition-colors whitespace-nowrap '.$classes]) }}
>
  {{ $slot }}@if ($count !== null)<span class="ml-1 opacity-80">({{ $count }})</span>@endif
</button>

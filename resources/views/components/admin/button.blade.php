@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
  $base = 'inline-flex items-center justify-center gap-2 text-sm font-semibold rounded-lg transition-all min-h-11 px-4';
  $variants = [
      'primary' => 'bg-secondary text-on-secondary hover:brightness-110 active:scale-[0.98]',
      'secondary' => 'px-4 py-2.5 border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-high',
      'danger' => 'px-4 py-2.5 border border-error/30 bg-error-container text-on-error-container hover:bg-error-container/80',
      'ghost' => 'px-3 py-2 text-secondary hover:bg-secondary-container/20 bg-transparent border-0 min-h-11',
      'navy' => 'bg-primary text-on-primary hover:brightness-110',
  ];
  $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif

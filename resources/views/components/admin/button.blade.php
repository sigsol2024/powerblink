@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
  $base = 'inline-flex items-center justify-center gap-2 text-sm font-medium rounded transition-colors';
  $variants = [
      'primary' => 'admin-luxe-btn-primary',
      'secondary' => 'px-3.5 py-2 border border-wp-border bg-white text-wp-text hover:bg-wp-bg',
      'danger' => 'px-3.5 py-2 border border-rose-300 bg-white text-rose-700 hover:bg-rose-50',
      'ghost' => 'px-2 py-1.5 text-wp-link hover:text-wp-link-hover bg-transparent border-0',
  ];
  $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif

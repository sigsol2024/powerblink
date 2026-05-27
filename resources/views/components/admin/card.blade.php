@props([
    'variant' => 'default',
])

@php
  $variants = [
      'default' => 'bg-white border border-wp-border rounded',
      'stats' => 'bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]',
      'toolbar' => 'bg-white border border-wp-border rounded p-3 md:p-4 flex flex-col gap-3',
      'table' => 'bg-white border border-wp-border rounded overflow-hidden',
  ];
  $classes = $variants[$variant] ?? $variants['default'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
  {{ $slot }}
</div>

@props([
    'variant' => 'default',
])

@php
  $variants = [
      'default' => 'bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-sm',
      'stats' => 'bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-5 md:p-6 flex flex-col justify-between min-h-[5.5rem] stat-card-shadow',
      'toolbar' => 'bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-4 flex flex-col gap-3',
      'table' => 'bg-surface-container-lowest border border-outline-variant/60 rounded-xl overflow-hidden shadow-sm',
      'glass' => 'glass-card rounded-xl',
  ];
  $classes = $variants[$variant] ?? $variants['default'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
  {{ $slot }}
</div>

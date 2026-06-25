@props([
    'label',
    'value',
    'hint' => null,
    'accent' => 'secondary',
    'icon' => null,
])

@php
  $accents = [
    'secondary' => 'border-secondary',
    'gold' => 'border-tertiary-fixed-dim',
    'navy' => 'border-primary',
    'green' => 'border-secondary-fixed',
  ];
  $border = $accents[$accent] ?? $accents['secondary'];
@endphp
<div {{ $attributes->merge(['class' => "bg-surface-container-lowest p-5 md:p-6 rounded-xl stat-card-shadow card-hover border-l-4 {$border}"]) }}>
  <p class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase tracking-wide text-[11px]">{{ $label }}</p>
  <div class="flex items-baseline gap-2 flex-wrap">
    <span class="font-stat-md text-stat-md text-primary leading-none">{{ $value }}</span>
    @if ($hint)
      <span class="text-on-surface-variant text-xs">{{ $hint }}</span>
    @endif
    @if ($icon)
      <span class="material-symbols-outlined text-secondary text-lg ml-auto">{{ $icon }}</span>
    @endif
  </div>
</div>

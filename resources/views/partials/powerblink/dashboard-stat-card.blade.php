@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
    'badge' => null,
    'badgeVariant' => 'neutral',
    'iconBg' => 'bg-secondary-container',
])

@php
  $badgeClasses = [
      'neutral' => 'bg-surface-container text-on-surface-variant',
      'success' => 'bg-secondary-container/40 text-on-secondary-container',
      'warning' => 'bg-tertiary-fixed/30 text-on-tertiary-fixed-variant',
      'info' => 'bg-primary-fixed/40 text-on-primary-fixed',
  ];
  $badgeClass = $badgeClasses[$badgeVariant] ?? $badgeClasses['neutral'];
@endphp
<div {{ $attributes->merge(['class' => 'glass-card rounded-3xl p-5 md:p-6 card-hover stat-card-shadow flex flex-col justify-between min-h-[8.5rem]']) }}>
  <div class="flex items-start justify-between gap-3 mb-4">
    @if ($icon)
      <div class="p-3 rounded-2xl {{ $iconBg }} text-on-secondary-container shrink-0">
        <x-icon :name="$icon" class="w-6 h-6" />
      </div>
    @endif
    @if ($badge)
      <span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full {{ $badgeClass }}">{{ $badge }}</span>
    @endif
  </div>
  <div>
    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wide text-[11px] mb-1">{{ $label }}</p>
    <div class="flex items-baseline gap-2 flex-wrap">
      <span class="font-stat-md text-stat-md text-primary leading-none">{{ $value }}</span>
      @if ($hint)
        <span class="text-on-surface-variant text-xs">{{ $hint }}</span>
      @endif
    </div>
  </div>
</div>

@props([
    'variant' => 'neutral',
])

@php
  $map = [
      'success' => 'activated',
      'activated' => 'activated',
      'warning' => 'pending_review',
      'pending_review' => 'pending_review',
      'awaiting_payment' => 'awaiting_payment',
      'danger' => 'rejected',
      'rejected' => 'rejected',
  ];
  $key = $map[$variant] ?? $variant;
  $variants = [
      'activated' => 'bg-secondary-container/40 text-on-secondary-container border-secondary/30',
      'pending_review' => 'bg-tertiary-fixed/30 text-on-tertiary-fixed-variant border-tertiary-fixed-dim/40',
      'awaiting_payment' => 'bg-primary-fixed/40 text-on-primary-fixed border-primary-fixed-dim/40',
      'rejected' => 'bg-error-container text-on-error-container border-error/20',
      'neutral' => 'bg-surface-container text-on-surface-variant border-outline-variant',
  ];
  $classes = 'inline-flex items-center px-2.5 py-1 border rounded-full text-[10px] font-bold uppercase tracking-wide '.($variants[$key] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>

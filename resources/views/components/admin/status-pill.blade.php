@props([
    'variant' => 'neutral',
])

@php
  $variants = [
      'success' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
      'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
      'danger' => 'bg-red-100 text-red-800 border-red-200',
      'neutral' => 'bg-slate-100 text-slate-700 border-slate-200',
  ];
  $classes = 'inline-flex items-center px-2 py-0.5 border rounded text-[10px] font-medium '.($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>

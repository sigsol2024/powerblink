@props([
    'title',
    'description' => null,
    'variant' => 'default',
])

@php
  $headerBg = $variant === 'danger' ? 'bg-error-container/30' : 'bg-surface-container-low/80';
  $titleClass = $variant === 'danger' ? 'text-on-error-container' : 'text-on-surface';
  $descClass = $variant === 'danger' ? 'text-on-error-container/80' : 'text-on-surface-variant';
@endphp

<x-admin.card {{ $attributes }}>
  <div class="-mx-5 md:-mx-6 -mt-5 md:-mt-6 mb-5 md:mb-6 px-5 md:px-6 py-4 border-b border-outline-variant/60 {{ $headerBg }} rounded-t-3xl">
    <h2 class="font-label-caps text-label-caps uppercase tracking-wide {{ $titleClass }}">{{ $title }}</h2>
    @if ($description)
      <p class="text-xs mt-1 {{ $descClass }}">{{ $description }}</p>
    @endif
  </div>
  {{ $slot }}
</x-admin.card>

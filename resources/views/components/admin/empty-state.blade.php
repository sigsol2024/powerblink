@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'px-4 py-10 text-center text-on-surface-variant text-sm']) }}>
  @if ($title)
    <p class="font-medium text-on-surface mb-1">{{ $title }}</p>
  @endif
  @if ($slot->isNotEmpty())
    <p>{{ $slot }}</p>
  @endif
</div>

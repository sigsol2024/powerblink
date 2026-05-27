@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'px-4 py-10 text-center text-wp-text-muted text-sm']) }}>
  @if ($title)
    <p class="font-medium text-wp-text mb-1">{{ $title }}</p>
  @endif
  @if ($slot->isNotEmpty())
    <p>{{ $slot }}</p>
  @endif
</div>

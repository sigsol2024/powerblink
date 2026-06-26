@props(['label', 'span' => '1'])

<div @class([
    'min-w-0',
    'sm:col-span-2' => (string) $span === '2',
    'col-span-full' => (string) $span === 'full',
])>
  <dt class="font-label-caps text-[11px] uppercase tracking-wide text-on-surface-variant mb-1">{{ $label }}</dt>
  <dd {{ $attributes->merge(['class' => 'text-sm text-on-surface']) }}>{{ $slot }}</dd>
</div>

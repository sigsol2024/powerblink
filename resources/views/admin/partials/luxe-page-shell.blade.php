@php
  $pageTitle = $pageTitle ?? '';
  $pageDescription = $pageDescription ?? null;
@endphp
  <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 px-margin-mobile md:px-margin-desktop py-6 border-b border-outline-variant bg-surface-container-lowest/95 backdrop-blur-md shrink-0">
  <div class="min-w-0">
    <h2 class="text-lg font-semibold text-wp-text tracking-tight">{{ $pageTitle }}</h2>
    @if ($pageDescription)
      <p class="text-sm text-wp-text-muted mt-2 max-w-2xl">{{ $pageDescription }}</p>
    @endif
  </div>
  @if (! empty($actions))
    <div class="flex flex-wrap items-center gap-3 shrink-0">{!! $actions !!}</div>
  @endif
</header>
<div class="px-margin-mobile md:px-margin-desktop py-4 md:py-6 flex-1">
  {{ $slot ?? '' }}
</div>

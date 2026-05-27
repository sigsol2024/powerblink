@php
  $pageTitle = $pageTitle ?? '';
  $pageDescription = $pageDescription ?? null;
@endphp
<header class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 px-4 md:px-6 py-6 md:py-8 border-b border-outline-variant bg-background/95 backdrop-blur-md sticky top-0 z-30 shrink-0">
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
<div class="px-4 md:px-6 py-6 md:py-8 flex-1">
  {{ $slot ?? '' }}
</div>
@include('admin.partials.luxe-footer')

@php
  $formMethod = $formMethod ?? 'post';
  $submitLabel = $submitLabel ?? __('Save Product');
@endphp

@push('head')
  @include('dashboard.vehicles.partials.luxe-form-styles')
@endpush

<div class="min-h-full flex flex-col">
  <header class="sticky top-0 z-30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white shrink-0">
    <h2 class="text-lg font-semibold text-wp-text">{{ $title }}</h2>
    <div class="flex items-center gap-2">
      <a href="{{ $cancelUrl }}" class="text-sm px-3 py-1.5 border border-wp-border text-wp-text bg-white rounded hover:bg-wp-bg transition-colors">
        {{ __('Cancel') }}
      </a>
      <button type="submit" form="luxe-product-form" class="admin-luxe-btn-primary">
        {{ $submitLabel }}
      </button>
    </div>
  </header>

  <div class="max-w-[1100px] mx-auto py-6 md:py-8 px-4 md:px-6 w-full flex-1">
    <form id="luxe-product-form" method="post" action="{{ $formAction }}" class="luxe-product-form space-y-6" enctype="multipart/form-data">
      @if (! in_array(strtolower($formMethod), ['post'], true))
        @method($formMethod)
      @endif
      {!! $slot !!}
    </form>
  </div>

  @include('admin.partials.luxe-footer', ['footerClass' => 'mt-6'])
</div>

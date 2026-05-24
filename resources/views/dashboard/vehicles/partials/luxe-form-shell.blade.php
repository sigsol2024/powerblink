@php
  $formMethod = $formMethod ?? 'post';
  $submitLabel = $submitLabel ?? __('Save Product');
@endphp

@push('head')
  @include('dashboard.vehicles.partials.luxe-form-styles')
@endpush

<div class="min-h-full flex flex-col">
  <header class="sticky top-0 z-30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-margin-mobile md:px-gutter py-6 border-b border-outline-variant bg-background/95 backdrop-blur-md shrink-0">
    <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary tracking-tight">{{ $title }}</h2>
    <div class="flex items-center gap-3">
      <a href="{{ $cancelUrl }}" class="font-button-text text-button-text uppercase px-6 md:px-8 py-3 border border-primary text-primary hover:bg-primary hover:text-on-primary transition-all text-center">
        {{ __('Cancel') }}
      </a>
      <button type="submit" form="luxe-product-form" class="font-button-text text-button-text uppercase px-6 md:px-8 py-3 bg-primary text-on-primary hover:scale-105 transition-transform shadow-lg shadow-primary/10">
        {{ $submitLabel }}
      </button>
    </div>
  </header>

  <div class="max-w-[1000px] mx-auto py-10 md:py-16 px-margin-mobile md:px-gutter w-full flex-1">
    <form id="luxe-product-form" method="post" action="{{ $formAction }}" class="luxe-product-form space-y-10" enctype="multipart/form-data">
      @if (! in_array(strtolower($formMethod), ['post'], true))
        @method($formMethod)
      @endif
      {!! $slot !!}
    </form>
  </div>

  @include('admin.partials.luxe-footer', ['footerClass' => 'mt-8'])
</div>

@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
@endphp
<footer class="luxe-store w-full py-section-py-mobile md:py-section-py-desktop px-margin-mobile md:px-gutter flex flex-col md:flex-row justify-between items-start max-w-max-container mx-auto border-t border-outline-variant mt-8">
  <div class="mb-12 md:mb-0">
    <h2 class="font-headline-md text-headline-md text-primary uppercase tracking-widest mb-4">{{ strtoupper($brandName) }}</h2>
    <p class="font-label-caps text-label-caps text-on-surface-variant max-w-xs leading-relaxed">{{ __('Defining the new era of global luxury through the lens of African craftsmanship.') }}</p>
  </div>
  <div class="grid grid-cols-2 gap-x-12 md:gap-x-16 gap-y-8">
    <div class="flex flex-col gap-3">
      <p class="font-label-caps text-label-caps text-primary mb-2">{{ __('INFORMATION') }}</p>
      <a href="{{ route('privacy-policy') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('PRIVACY POLICY') }}</a>
      <a href="{{ route('terms') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('TERMS OF SERVICE') }}</a>
    </div>
    <div class="flex flex-col gap-3">
      <p class="font-label-caps text-label-caps text-primary mb-2">{{ __('ASSISTANCE') }}</p>
      <a href="{{ route('contact') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('CONTACT US') }}</a>
      <a href="{{ route('faq') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('FAQ') }}</a>
    </div>
  </div>
  <div class="mt-12 md:mt-0 md:text-right">
    <p class="font-label-caps text-label-caps text-on-surface-variant">© {{ date('Y') }} {{ $brandName }}. {{ __('ALL RIGHTS RESERVED.') }}</p>
  </div>
</footer>

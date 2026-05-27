@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
@endphp
<footer class="luxe-store w-full py-section-py-mobile md:py-section-py-desktop px-margin-mobile md:px-gutter flex flex-col md:flex-row justify-between items-start max-w-max-container mx-auto border-t border-outline-variant">
  <div class="mb-12 md:mb-0">
    <h2 class="font-headline-md text-headline-md text-primary uppercase tracking-widest mb-4">{{ strtoupper($brandName) }}</h2>
    <p class="font-label-caps text-label-caps text-on-surface-variant max-w-[300px]">{{ __('THE PINNACLE OF AFRICAN ARTISANSHIP AND GLOBAL LUXURY DESIGN.') }}</p>
  </div>
  <div class="grid grid-cols-2 gap-12 lg:gap-24">
    <div class="flex flex-col gap-4">
      <p class="font-label-caps text-label-caps text-primary font-bold">{{ __('CLIENT SERVICES') }}</p>
      <a href="{{ route('about') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('ABOUT US') }}</a>
      <a href="{{ route('contact') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('CONTACT US') }}</a>
      <a href="{{ route('faq') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('FAQ') }}</a>
    </div>
    <div class="flex flex-col gap-4">
      <p class="font-label-caps text-label-caps text-primary font-bold">{{ __('LEGAL') }}</p>
      <a href="{{ route('privacy-policy') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('PRIVACY POLICY') }}</a>
      <a href="{{ route('terms') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('TERMS OF SERVICE') }}</a>
    </div>
  </div>
  <div class="mt-12 md:mt-0 md:text-right flex flex-col justify-between h-full">
    <p class="font-label-caps text-label-caps text-on-surface-variant">© {{ date('Y') }} {{ $brandName }}. {{ __('ALL RIGHTS RESERVED.') }}</p>
  </div>
</footer>

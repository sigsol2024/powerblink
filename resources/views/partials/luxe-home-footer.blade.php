@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $tagline = trim((string) ($site['footer_tagline'] ?? ''));
  if ($tagline === '') {
    $tagline = __('THE PINNACLE OF AFRICAN ARTISANSHIP AND GLOBAL LUXURY DESIGN.');
  }
  $newsletterEnabled = (string) ($site['newsletter_enabled'] ?? '0') === '1';
  $newsletterNote = trim((string) ($site['newsletter_note'] ?? ''));
  $privacyUrl = trim((string) ($site['footer_privacy_url'] ?? ''));
  $termsUrl = trim((string) ($site['footer_terms_url'] ?? ''));
  if ($privacyUrl === '' || $privacyUrl === '#') {
    $privacyUrl = route('privacy-policy');
  }
  if ($termsUrl === '' || $termsUrl === '#') {
    $termsUrl = route('terms');
  }
@endphp
<footer class="luxe-store w-full py-section-py-mobile md:py-section-py-desktop px-margin-mobile md:px-gutter flex flex-col md:flex-row justify-between items-start max-w-max-container mx-auto border-t border-outline-variant">
  <div class="mb-12 md:mb-0">
    <h2 class="font-headline-md text-headline-md text-primary uppercase tracking-widest mb-4">{{ strtoupper($brandName) }}</h2>
    <p class="font-label-caps text-label-caps text-on-surface-variant max-w-[300px]">{{ $tagline }}</p>
    @if ($newsletterEnabled)
      <form action="{{ route('newsletter.subscribe') }}" method="post" class="mt-7 max-w-[320px]">
        @csrf
        <label class="font-label-caps text-label-caps text-primary font-bold block mb-3">{{ __('NEWSLETTER') }}</label>
        @if ($newsletterNote !== '')
          <p class="font-body-md text-body-md text-on-surface-variant mb-4">{{ $newsletterNote }}</p>
        @endif
        <div class="flex border-b border-outline-variant py-2">
          <input class="bg-transparent border-none focus:ring-0 p-0 text-body-md w-full placeholder:text-outline-variant" name="email" type="email" placeholder="{{ __('Your email') }}" required />
          <button type="submit" class="material-symbols-outlined text-primary" aria-label="{{ __('Subscribe') }}">arrow_forward</button>
        </div>
      </form>
    @endif
  </div>
  <div class="grid grid-cols-2 gap-12 lg:gap-24">
    <div class="flex flex-col gap-4">
      <p class="font-label-caps text-label-caps text-primary font-bold">{{ __('CLIENT SERVICES') }}</p>
      <a href="{{ route('about') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('ABOUT US') }}</a>
      <a href="{{ route('contact') }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('CONTACT US') }}</a>
    </div>
    <div class="flex flex-col gap-4">
      <p class="font-label-caps text-label-caps text-primary font-bold">{{ __('LEGAL') }}</p>
      <a href="{{ $privacyUrl }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('PRIVACY POLICY') }}</a>
      <a href="{{ $termsUrl }}" class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors">{{ __('TERMS OF SERVICE') }}</a>
    </div>
  </div>
  <div class="mt-12 md:mt-0 md:text-right flex flex-col justify-between h-full">
    <p class="font-label-caps text-label-caps text-on-surface-variant">© {{ date('Y') }} {{ $brandName }}. {{ __('ALL RIGHTS RESERVED.') }}</p>
  </div>
</footer>

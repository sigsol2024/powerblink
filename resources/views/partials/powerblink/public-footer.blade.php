@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $email = $site['dealer_public_email'] ?? $site['dealer_email'] ?? 'info@powerblinkfc.com';
  $phone = $site['dealer_phone'] ?? $site['dealer_sales_phone'] ?? '';
  $address = $site['dealer_address'] ?? __('Ibeju Lekki, Lagos State');
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
@endphp
<footer class="bg-primary-container border-t border-outline-variant/20 pt-16 md:pt-20 pb-10 mt-auto">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-element-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto text-on-primary-container">
    <div>
      <div class="flex items-center gap-2 mb-6">
        @if (!empty($logoPath))
          <img src="{{ \App\Support\MediaImageUrl::url($logoPath) }}" alt="{{ $brandName }}" class="h-10 w-auto" />
        @endif
        <span class="font-display-hero text-headline-md text-on-primary">{{ $brandName }}</span>
      </div>
      <p class="text-on-primary-container/80 mb-6 text-sm leading-relaxed">
        {{ __('Elite Excellence in Ibeju Lekki. Shaping the future of football, one star at a time.') }}
      </p>
      <a href="{{ route('registration.wizard') }}" class="inline-flex items-center gap-2 text-secondary-fixed font-bold text-sm hover:underline">
        {{ __('Start Registration') }}
        <x-icon name="arrow_forward" class="w-4 h-4" />
      </a>
    </div>
    <div>
      <h4 class="font-bold text-on-primary mb-6">{{ __('Quick Links') }}</h4>
      <ul class="space-y-3 text-sm">
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('about') }}">{{ __('About Us') }}</a></li>
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('programs') }}">{{ __('Programs') }}</a></li>
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('coaching') }}">{{ __('Coaching Team') }}</a></li>
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-bold text-on-primary mb-6">{{ __('Programs') }}</h4>
      <ul class="space-y-3 text-sm">
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('programs') }}">{{ __('Foundation Phase') }}</a></li>
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('programs') }}">{{ __('Elite Academy') }}</a></li>
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('tournaments') }}">{{ __('Tournaments') }}</a></li>
        <li><a class="text-on-primary-container/80 hover:text-secondary-fixed transition-colors" href="{{ route('registration.wizard') }}">{{ __('Registration') }}</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-bold text-on-primary mb-6">{{ __('Contact Us') }}</h4>
      <ul class="space-y-3 text-sm">
        <li class="flex items-start gap-3 text-on-primary-container/80">
          <x-icon name="location_on" class="w-5 h-5 text-secondary-fixed shrink-0" />
          <span>{{ $address }}</span>
        </li>
        @if ($email !== '')
          <li class="flex items-center gap-3 text-on-primary-container/80">
            <x-icon name="mail" class="w-5 h-5 text-secondary-fixed" />
            <a href="mailto:{{ $email }}" class="hover:text-secondary-fixed transition-colors">{{ $email }}</a>
          </li>
        @endif
        @if ($phone !== '')
          <li class="flex items-center gap-3 text-on-primary-container/80">
            <x-icon name="call" class="w-5 h-5 text-secondary-fixed" />
            <span>{{ $phone }}</span>
          </li>
        @endif
      </ul>
    </div>
  </div>
  <div class="mt-12 px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-on-primary-container/60">
    <p>&copy; {{ date('Y') }} {{ $brandName }}. {{ __('Elite Excellence in Ibeju Lekki.') }}</p>
    <div class="flex gap-6">
      <a href="{{ route('contact') }}" class="hover:text-secondary-fixed transition-colors">{{ __('Privacy Policy') }}</a>
      <a href="{{ route('contact') }}" class="hover:text-secondary-fixed transition-colors">{{ __('Terms of Service') }}</a>
    </div>
  </div>
</footer>

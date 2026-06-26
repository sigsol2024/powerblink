@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
@endphp
<footer class="shrink-0 border-t border-outline-variant bg-surface-container-lowest px-margin-mobile md:px-margin-desktop py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-on-surface-variant">
  <span>&copy; {{ date('Y') }} {{ $brandName }}</span>
  <a href="{{ route('contact') }}" class="hover:text-secondary transition-colors">{{ __('Help &amp; contact') }}</a>
</footer>

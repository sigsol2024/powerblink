@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
@endphp
<footer class="shrink-0 border-t border-pb-border bg-pb-surface px-4 md:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-pb-muted">
  <span>&copy; {{ date('Y') }} {{ $brandName }}</span>
  <a href="{{ route('contact') }}" class="hover:text-pb-navy">{{ __('Help &amp; contact') }}</a>
</footer>

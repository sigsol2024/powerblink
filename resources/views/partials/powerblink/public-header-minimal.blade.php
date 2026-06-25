@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
@endphp
<header class="bg-pb-navy text-white border-b border-white/10">
  <div class="max-w-container mx-auto px-4 md:px-6 flex items-center justify-between h-14">
    <a href="{{ route('home') }}" class="flex items-center gap-2">
      @if (!empty($logoPath))
        <img src="{{ \App\Support\MediaImageUrl::url($logoPath) }}" alt="" class="h-8 w-8 rounded-full object-cover" />
      @endif
      <span class="font-display font-bold text-base">{{ $brandName }}</span>
    </a>
    <a href="{{ route('home') }}" class="text-sm text-white/70 hover:text-white">{{ __('Back to site') }}</a>
  </div>
</header>

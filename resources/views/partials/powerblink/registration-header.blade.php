@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
@endphp
<header class="bg-surface fixed top-0 w-full z-50 shadow-sm border-b border-outline-variant/40">
  <div class="max-w-container mx-auto px-margin-mobile md:px-margin-desktop py-4 flex justify-between items-center gap-4">
    <div class="flex items-center gap-3 min-w-0">
      @if (!empty($logoPath))
        <img src="{{ \App\Support\MediaImageUrl::url($logoPath) }}" alt="" class="h-10 w-10 rounded-full object-cover shrink-0" />
      @else
        <img src="{{ \App\Support\PlaceholderMedia::url('asset/images/powerblink/home-powerblink-fc-001.jpg') }}" alt="" class="h-10 w-10 rounded-full object-cover shrink-0" />
      @endif
      <div class="min-w-0">
        <p class="font-display font-bold text-primary text-sm md:text-base truncate">{{ $brandName }}</p>
        <p class="text-label-caps text-[10px] uppercase tracking-widest text-on-surface-variant">{{ __('Registration') }}</p>
      </div>
    </div>
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 min-h-11 px-3 text-on-surface-variant font-medium hover:text-primary transition-colors shrink-0">
      <span class="material-symbols-outlined text-xl">close</span>
      <span class="hidden sm:inline text-sm">{{ __('Exit application') }}</span>
    </a>
  </div>
</header>

@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
  $nav = [
    ['route' => 'home', 'label' => __('Home')],
    ['route' => 'about', 'label' => __('About')],
    ['route' => 'programs', 'label' => __('Programs')],
    ['route' => 'coaching', 'label' => __('Coaching')],
    ['route' => 'tournaments', 'label' => __('Tournaments')],
    ['route' => 'gallery', 'label' => __('Gallery')],
    ['route' => 'contact', 'label' => __('Contact')],
  ];
@endphp
<header id="main-nav" class="fixed top-0 inset-x-0 z-50 bg-surface/90 backdrop-blur-md shadow-md transition-all duration-300">
  <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop flex items-center justify-between py-4">
    <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
      @if (!empty($logoPath))
        <img src="{{ \App\Support\MediaImageUrl::url($logoPath) }}" alt="{{ $brandName }}" class="h-10 w-10 rounded-full object-cover" />
      @endif
      <span class="font-display-hero text-headline-md font-extrabold text-primary">{{ $brandName }}</span>
    </a>

    <nav class="hidden xl:flex items-center gap-6" aria-label="{{ __('Main') }}">
      @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}"
           class="text-sm font-medium transition-colors duration-300 {{ request()->routeIs($item['route']) ? 'active-nav-link' : 'text-on-surface-variant hover:text-secondary' }}">
          {{ $item['label'] }}
        </a>
      @endforeach
    </nav>

    <div class="flex items-center gap-2 md:gap-3">
      @auth
        <a href="{{ auth()->user()->canAccessAdminPanel() ? route('admin.dashboard') : route('portal.dashboard') }}"
           class="hidden sm:inline-flex px-4 py-2 border-2 border-primary text-primary text-sm font-bold rounded-lg hover:bg-primary hover:text-on-primary transition-all">
          {{ __('Dashboard') }}
        </a>
      @else
        <a href="{{ route('login') }}"
           class="hidden sm:inline-flex px-4 py-2 border-2 border-primary text-primary text-sm font-bold rounded-lg hover:bg-primary hover:text-on-primary transition-all">
          {{ __('Login') }}
        </a>
      @endauth
      <a href="{{ route('registration.wizard') }}"
         class="inline-flex px-4 py-2 rounded-lg bg-secondary text-on-secondary text-sm font-bold shadow-md hover:opacity-90 transition-all">
        {{ __('Register') }}
      </a>
      <button type="button" class="xl:hidden p-2 text-primary" x-data @click="$dispatch('toggle-mobile-nav')" aria-label="{{ __('Menu') }}">
        <x-icon name="menu" class="w-6 h-6" />
      </button>
    </div>
  </div>

  <div class="xl:hidden border-t border-outline-variant/30 bg-surface/95 backdrop-blur-md" x-data="{ open: false }" @toggle-mobile-nav.window="open = !open" x-show="open" x-cloak>
    <nav class="max-w-container-max mx-auto px-margin-mobile py-3 flex flex-col gap-1">
      @foreach ($nav as $item)
        <a href="{{ route($item['route']) }}" class="px-3 py-2 text-sm rounded-lg {{ request()->routeIs($item['route']) ? 'bg-secondary/10 text-secondary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">
          {{ $item['label'] }}
        </a>
      @endforeach
      @guest
        <a href="{{ route('login') }}" class="px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container rounded-lg">{{ __('Login') }}</a>
      @endguest
    </nav>
  </div>
</header>

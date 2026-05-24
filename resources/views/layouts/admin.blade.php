@php
  $site = $site ?? [];
  $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : config('app.name', 'Console');
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
  $user = Auth::user();
  $n = trim((string) ($user->name ?? 'User'));
  $initials = strtoupper(substr($n, 0, 1).(str_contains($n, ' ') ? substr($n, (int) strrpos($n, ' ') + 1, 1) : ''));
  $initials = strlen($initials) > 2 ? substr($initials, 0, 2) : $initials;

  $isAdminRole = $user && $user->hasRole('admin');
  $dashboardHomeRoute = $isAdminRole ? 'admin.dashboard' : 'dashboard';

  $navItems = $isAdminRole
      ? [
          ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => __('Dashboard'), 'icon' => 'dashboard'],
          ['route' => 'dashboard.vehicles.index', 'match' => 'dashboard.vehicles.*', 'label' => __('Products'), 'icon' => 'inventory_2'],
          ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'label' => __('Orders'), 'icon' => 'shopping_cart'],
          ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => __('Customers'), 'icon' => 'group'],
          ['route' => 'admin.analytics.index', 'match' => 'admin.analytics.*', 'label' => __('Analytics'), 'icon' => 'monitoring'],
          ['route' => 'admin.pages.index', 'match' => 'admin.pages.*', 'label' => __('Pages'), 'icon' => 'article'],
          ['route' => 'admin.listing-options.index', 'match' => 'admin.listing-options.*', 'label' => __('Listing options'), 'icon' => 'tune'],
          ['route' => 'admin.media.index', 'match' => 'admin.media.*', 'label' => __('Media'), 'icon' => 'perm_media'],
          ['route' => 'admin.audit.index', 'match' => 'admin.audit.*', 'label' => __('Audit trail'), 'icon' => 'history'],
      ]
      : [
          ['route' => 'dashboard', 'match' => 'dashboard', 'label' => __('Overview'), 'icon' => 'dashboard'],
          ['route' => 'dashboard.vehicles.index', 'match' => 'dealer.vehicles.list', 'label' => __('My products'), 'icon' => 'inventory_2'],
          ['route' => 'dashboard.vendor-settings.edit', 'match' => 'dashboard.vendor-settings.*', 'label' => __('Dealer contact'), 'icon' => 'storefront'],
          ['route' => 'dashboard.favorites.index', 'match' => 'dashboard.favorites.*', 'label' => __('Saved'), 'icon' => 'favorite'],
      ];

  $mediaUploadUrl = $isAdminRole ? route('admin.media.upload') : route('dashboard.api.media.upload');
  $mediaListUrl = $isAdminRole ? route('admin.media.list') : route('dashboard.api.media');
  $viteReady = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
  $fullBleedRoutes = request()->routeIs([
    'admin.dashboard', 'dashboard.vehicles.index', 'admin.orders.index', 'admin.orders.show',
    'dashboard.vehicles.create', 'dashboard.vehicles.edit',
    'admin.users.*', 'admin.settings.*', 'admin.media.*', 'admin.analytics.*', 'admin.audit.*',
    'admin.pages.*', 'admin.listing-options.*',
  ]);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ isset($title) ? $title.' — ' : '' }}{{ $brandName }}</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  @include('admin.partials.luxe-head')
  @stack('head')
  <style>[x-cloak]{display:none!important}</style>
  @if ($viteReady)
    @vite(['resources/js/app.js'])
  @else
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
  @endif
  @stack('scripts')
</head>
<body
  class="admin-luxe-root font-body-md text-on-background antialiased h-full overflow-hidden selection:bg-secondary-fixed-dim selection:text-on-secondary-fixed"
  x-data="{ drawerOpen: false }"
  @keydown.escape.window="drawerOpen = false"
>
  <div class="flex h-screen w-full overflow-hidden relative">
    <div class="absolute inset-0 luxe-grid-pattern pointer-events-none" aria-hidden="true"></div>

    <div
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      x-show="drawerOpen"
      x-cloak
      x-transition.opacity
      @click="drawerOpen = false"
      aria-hidden="true"
    ></div>

    <aside class="hidden lg:flex flex-col h-screen w-64 shrink-0 border-r border-outline-variant bg-surface-container-low z-50">
      <div class="px-6 py-10 shrink-0">
        <a href="{{ route($dashboardHomeRoute) }}">
          <h1 class="font-headline-md text-headline-md text-primary uppercase tracking-widest">ADÉ ADMIN</h1>
          <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">{{ __('Luxury Management') }}</p>
        </a>
      </div>

      <nav class="flex-1 flex flex-col space-y-1 min-h-0 overflow-y-auto custom-scrollbar" aria-label="{{ $isAdminRole ? __('Admin') : __('Account') }}">
        @foreach ($navItems as $item)
          @php
            $match = $item['match'];
            if ($match === 'dealer.vehicles.list') {
                $active = request()->routeIs('dashboard.vehicles.*') && ! request()->routeIs('dashboard.vehicles.create');
            } else {
                $active = request()->routeIs($match);
            }
          @endphp
          <a
            href="{{ route($item['route']) }}"
            class="flex items-center px-6 py-4 transition-all {{ $active ? 'bg-primary text-on-primary font-medium' : 'text-on-surface-variant hover:bg-surface-container-high' }}"
          >
            <span class="material-symbols-outlined mr-4 {{ $active ? 'filled' : '' }}">{{ $item['icon'] }}</span>
            <span class="font-body-md">{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>

      <div class="mt-auto border-t border-outline-variant py-6 shrink-0">
        <a href="{{ $isAdminRole ? route('admin.settings.edit') : route('dashboard.vendor-settings.edit') }}" class="flex items-center px-6 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all">
          <span class="material-symbols-outlined mr-4">settings</span>
          <span class="font-body-md">{{ __('Settings') }}</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="flex w-full items-center px-6 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all text-left">
            <span class="material-symbols-outlined mr-4">logout</span>
            <span class="font-body-md">{{ __('Logout') }}</span>
          </button>
        </form>
        <div class="px-6 mt-6 flex items-center gap-3">
          @if (!empty($logoPath))
            <img src="{{ \App\Support\VehicleImageUrl::url($logoPath) }}" alt="" class="w-10 h-10 rounded-full object-cover grayscale shrink-0" />
          @else
            <span class="w-10 h-10 rounded-full bg-surface-container-highest flex items-center justify-center font-label-caps text-[10px] font-bold shrink-0">{{ $initials }}</span>
          @endif
          <div class="min-w-0">
            <p class="font-label-caps text-[10px] uppercase tracking-tighter opacity-70 truncate">{{ __('Admin User') }}</p>
            <p class="font-body-md text-sm font-medium truncate">{{ $user->name }}</p>
          </div>
        </div>
      </div>
    </aside>

    {{-- Mobile drawer (same structure) --}}
    <aside
      class="fixed inset-y-0 left-0 z-50 flex h-full w-[min(18rem,calc(100vw-2rem))] flex-col border-r border-outline-variant bg-surface-container-low lg:hidden transition-transform duration-300"
      :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="px-6 py-8 flex items-center justify-between shrink-0 border-b border-outline-variant">
        <div>
          <h1 class="font-headline-md text-primary uppercase tracking-widest text-lg">ADÉ ADMIN</h1>
          <p class="font-label-caps text-[10px] text-on-surface-variant mt-1">{{ __('Luxury Management') }}</p>
        </div>
        <button type="button" class="material-symbols-outlined text-primary p-2" @click="drawerOpen = false" aria-label="{{ __('Close') }}">close</button>
      </div>
      <nav class="flex-1 overflow-y-auto custom-scrollbar py-2">
        @foreach ($navItems as $item)
          @php
            $match = $item['match'];
            if ($match === 'dealer.vehicles.list') {
                $active = request()->routeIs('dashboard.vehicles.*') && ! request()->routeIs('dashboard.vehicles.create');
            } else {
                $active = request()->routeIs($match);
            }
          @endphp
          <a href="{{ route($item['route']) }}" @click="drawerOpen = false" class="flex items-center px-6 py-4 {{ $active ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
            <span class="material-symbols-outlined mr-4 {{ $active ? 'filled' : '' }}">{{ $item['icon'] }}</span>
            <span class="font-body-md">{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>
      <div class="border-t border-outline-variant p-4 shrink-0">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center px-4 py-3 text-on-surface-variant text-sm" @click="drawerOpen = false">{{ __('View site') }}</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-3 text-error text-sm font-label-caps">{{ __('Logout') }}</button>
        </form>
      </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden bg-background relative z-10">
      <header class="lg:hidden flex justify-between items-center px-margin-mobile py-4 border-b border-outline-variant bg-background/95 backdrop-blur-sm shrink-0 z-40">
        <h1 class="font-headline-md text-primary uppercase tracking-widest text-lg">ADÉ ADMIN</h1>
        <button type="button" class="material-symbols-outlined text-primary p-2 -mr-2" @click="drawerOpen = true" aria-label="{{ __('Menu') }}">menu</button>
      </header>

      <div class="flex-1 min-h-0 overflow-hidden flex flex-col">
        @if (isset($header) && ! $fullBleedRoutes)
          <div class="shrink-0 border-b border-outline-variant bg-surface-container-lowest/95 px-margin-mobile md:px-gutter py-6 backdrop-blur-sm">
            <div class="max-w-max-container mx-auto w-full">{{ $header }}</div>
          </div>
        @endif
        <div class="flex-1 min-h-0 overflow-y-auto custom-scrollbar">
          {{ $slot }}
        </div>
      </div>
    </main>
  </div>

  @include('partials.media-modal', ['mediaUploadUrl' => $mediaUploadUrl])
  <input type="hidden" id="media-list-url" value="{{ $mediaListUrl }}" />
  @unless (request()->routeIs('admin.pages.edit'))
    @include('partials.media-modal-pickers')
  @endunless
  @stack('body-end')
</body>
</html>

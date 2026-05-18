@php
  $site = $site ?? [];
  $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : config('app.name', 'Console');
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
  $user = Auth::user();
  $n = trim((string) ($user->name ?? 'User'));
  $initials = strtoupper(substr($n, 0, 1).(str_contains($n, ' ') ? substr($n, (int) strrpos($n, ' ') + 1, 1) : ''));
  $initials = strlen($initials) > 2 ? substr($initials, 0, 2) : $initials;

  $icOverview = 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z';
  $icListings = 'M6 6.878V6a2.25 2.25 0 012.25-2.25h9.75A2.25 2.25 0 0120.25 6v.878m-15.75 1.5h15m-15 0a2.25 2.25 0 00-2.25 2.25v9.75A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25v-9.75a2.25 2.25 0 00-2.25-2.25h-15z';
  $icUsers = 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z';
  $icPlus = 'M12 4.5v15m7.5-7.5h-15';
  $icHeart = 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z';
  $icStore = 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c-1.026 0-1.945.52-2.48 1.312A3.001 3.001 0 003.75 9.35m0 0v11.65';

  $isAdminRole = $user && $user->hasRole('admin');
  $dashboardHomeRoute = $isAdminRole ? 'admin.dashboard' : 'dashboard';
  $shellSubtitle = $isAdminRole ? __('Console') : __('Dealer');
  $navAriaLabel = $isAdminRole ? __('Admin') : __('Account');

  /** Outline icons (stroke 1.5) — Heroicons 24 outline style */
  $navItems = $isAdminRole
      ? [
          ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => __('Overview'), 'icon' => $icOverview],
          ['route' => 'dashboard.vehicles.index', 'match' => 'dashboard.vehicles.*', 'label' => __('All listings'), 'icon' => $icListings],
          ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => __('All users'), 'icon' => $icUsers],
          ['route' => 'admin.analytics.index', 'match' => 'admin.analytics.*', 'label' => __('Analytics'), 'icon' => 'M3 3v18h18M7 15l3-3 3 2 4-5'],
          ['route' => 'admin.pages.index', 'match' => 'admin.pages.*', 'label' => __('Pages'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
          ['route' => 'admin.listing-options.index', 'match' => 'admin.listing-options.*', 'label' => __('Listing options'), 'icon' => 'M4 6h16M4 10h16M4 14h10'],
          ['route' => 'admin.media.index', 'match' => 'admin.media.*', 'label' => __('Media'), 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
          ['route' => 'admin.settings.edit', 'match' => 'admin.settings.*', 'label' => __('Site settings'), 'icon' => 'M3 3v18h18M7 15l3-3 3 2 4-5'],
          ['route' => 'admin.audit.index', 'match' => 'admin.audit.*', 'label' => __('Audit trail'), 'icon' => 'M9 12h6m-6 4h6M7.5 3.75h9A2.25 2.25 0 0118.75 6v12A2.25 2.25 0 0116.5 20.25h-9A2.25 2.25 0 015.25 18V6A2.25 2.25 0 017.5 3.75z'],
      ]
      : [
          ['route' => 'dashboard', 'match' => 'dashboard', 'label' => __('Overview'), 'icon' => $icOverview],
          ['route' => 'dashboard.vehicles.index', 'match' => 'dealer.vehicles.list', 'label' => __('My listings'), 'icon' => $icListings],
          ['route' => 'dashboard.vendor-settings.edit', 'match' => 'dashboard.vendor-settings.*', 'label' => __('Dealer contact'), 'icon' => $icStore],
          ['route' => 'dashboard.favorites.index', 'match' => 'dashboard.favorites.*', 'label' => __('Saved vehicles'), 'icon' => $icHeart],
      ];

  $mediaUploadUrl = $isAdminRole ? route('admin.media.upload') : route('dashboard.api.media.upload');
  $mediaListUrl = $isAdminRole ? route('admin.media.list') : route('dashboard.api.media');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}{{ $brandName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @stack('head')
    <style>[x-cloak]{display:none!important}</style>
    {{-- Sidebar SVG icons + StayEazi-style scroll shell (fixed rail + ml offset + flex column scroll pane) --}}
    <style>
      .admin-sidebar svg.admin-nav-icon { width: 1.25rem; height: 1.25rem; max-width: 1.25rem; max-height: 1.25rem; flex-shrink: 0; display: block; }
      /*
        StayEazi pattern: fixed sidebar + main has margin-left AND width calc (not width:100% + margin, which overflows).
        See .main-content-area { width: calc(100% - 70px); margin-left: 70px; } in admin-design.css
      */
      .admin-main-shell {
        display: flex;
        flex-direction: column;
        height: 100dvh;
        max-height: 100dvh;
        min-height: 0;
        overflow: hidden;
        box-sizing: border-box;
        width: 100%;
        max-width: 100%;
      }
      @media (min-width: 1024px) {
        .admin-main-shell {
          margin-left: 16rem;
          width: calc(100% - 16rem);
          max-width: calc(100% - 16rem);
        }
        .admin-main-shell.admin-main-shell--rail {
          margin-left: 4.5rem;
          width: calc(100% - 4.5rem);
          max-width: calc(100% - 4.5rem);
        }
      }
      .admin-content-scroll { flex: 1 1 0%; min-height: 0; overflow-y: auto; overflow-x: hidden; -webkit-overflow-scrolling: touch; }
      .admin-scrollbar { scrollbar-width: thin; scrollbar-color: #a1a1aa #f4f4f5; }
      .admin-scrollbar::-webkit-scrollbar { width: 10px; height: 10px; }
      .admin-scrollbar::-webkit-scrollbar-track { background: #f4f4f5; }
      .admin-scrollbar::-webkit-scrollbar-thumb { background-color: #a1a1aa; border-radius: 9999px; border: 2px solid #f4f4f5; }
      .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
      }
      .hide-scrollbar::-webkit-scrollbar {
        width: 0;
        height: 0;
      }
      .admin-shell-header { min-width: 0; }
      .admin-shell-header h2,
      .admin-shell-header .admin-page-title,
      .admin-shell-header p {
        margin: 0;
      }
      .admin-shell-header .admin-page-title,
      .admin-shell-header > h2 {
        font-size: 1rem;
        font-weight: 600;
        line-height: 1.35;
        color: #18181b;
      }
      .admin-shell-header .admin-page-eyebrow {
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #71717a;
        margin-bottom: 0.125rem;
      }
      @media (min-width: 640px) {
        .admin-shell-header .admin-page-title,
        .admin-shell-header > h2 {
          font-size: 1.125rem;
        }
      }
      .admin-content-toolbar {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
      }
      @media (min-width: 640px) {
        .admin-content-toolbar {
          flex-direction: row;
          align-items: flex-start;
          justify-content: space-between;
          gap: 1rem;
        }
      }
      .admin-content-toolbar__actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
      }
      @media (min-width: 640px) {
        .admin-content-toolbar__actions {
          flex-direction: row;
          flex-wrap: wrap;
          align-items: center;
          justify-content: flex-end;
          width: auto;
          margin-left: auto;
          flex-shrink: 0;
        }
      }
      .admin-btn,
      .admin-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.25rem;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        transition: background-color 0.15s, border-color 0.15s, color 0.15s;
      }
      @media (min-width: 640px) {
        .admin-btn,
        .admin-btn-primary {
          width: auto;
        }
      }
      .admin-btn {
        border: 1px solid #e4e4e7;
        background-color: #fff;
        color: #3f3f46;
      }
      .admin-btn:hover {
        border-color: #d4d4d8;
        background-color: #fafafa;
      }
      .admin-btn-primary {
        border: 1px solid #f59e0b;
        background-color: #f59e0b;
        color: #0f172a;
      }
      .admin-btn-primary:hover {
        background-color: #fbbf24;
      }
    </style>
    @stack('scripts')
    @include('partials.vite-assets')
  </head>
  <body
    class="h-full max-h-[100dvh] overflow-hidden overflow-x-hidden antialiased bg-zinc-100 text-zinc-900"
    style="font-family:Inter,system-ui,sans-serif"
    x-data="{
      drawerOpen: false,
      railMode: (() => { try { return localStorage.getItem('mt_admin_rail') === '1'; } catch (e) { return false; } })(),
      openDrawer() { this.drawerOpen = true; document.body.classList.add('overflow-hidden'); },
      closeDrawer() { this.drawerOpen = false; document.body.classList.remove('overflow-hidden'); },
      toggleRail() {
        this.railMode = !this.railMode;
        try { localStorage.setItem('mt_admin_rail', this.railMode ? '1' : '0'); } catch (e) {}
      },
    }"
    @keydown.escape.window="closeDrawer()"
    @resize.window="if (window.innerWidth >= 1024) closeDrawer()"
  >
    <div
      class="fixed inset-0 z-[90] hidden bg-black/60 backdrop-blur-[2px] transition-opacity lg:hidden"
      :class="drawerOpen ? 'block' : 'hidden'"
      x-transition.opacity.duration.200ms
      @click="closeDrawer()"
      aria-hidden="true"
    ></div>

    {{-- StayEazi-style: sidebar position:fixed (out of flow), main offset with margin-left; only .admin-content-scroll scrolls. --}}
      <aside
        class="admin-sidebar fixed inset-y-0 left-0 z-[100] flex h-[100dvh] w-[min(18rem,calc(100vw-2.5rem))] flex-col overflow-hidden border-r border-white/10 bg-[#0a0d12] shadow-xl transition-[transform,width] duration-300 ease-out lg:w-64"
        :class="[
          drawerOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
          railMode ? 'lg:!w-[4.5rem]' : '',
        ]"
      >
        <div class="flex h-16 shrink-0 items-center gap-2 border-b border-white/10 px-4">
          <a href="{{ route($dashboardHomeRoute) }}" class="flex min-w-0 flex-1 items-center gap-3" @click="closeDrawer()">
            @if (!empty($logoPath))
              <img x-show="!railMode" x-cloak src="{{ \App\Support\VehicleImageUrl::url($logoPath) }}" alt="" class="h-9 w-9 shrink-0 rounded-lg object-contain ring-1 ring-white/10" />
            @else
              <span x-show="!railMode" x-cloak class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-amber-500/40 bg-amber-500/10 text-sm font-bold text-amber-400">{{ strtoupper(\Illuminate\Support\Str::substr($brandName, 0, 1)) }}</span>
            @endif
            <div class="min-w-0" x-show="!railMode" x-cloak>
              <div class="truncate text-sm font-semibold tracking-tight text-white">{{ $brandName }}</div>
              <div class="truncate text-[10px] font-medium uppercase tracking-[0.18em] text-zinc-500">{{ $shellSubtitle }}</div>
            </div>
          </a>
          <button
            type="button"
            class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-white/5 hover:text-white lg:flex"
            @click="toggleRail()"
            title="{{ __('Toggle menu width') }}"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
          </button>
          <button type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-zinc-400 hover:bg-white/5 hover:text-white lg:hidden" @click="closeDrawer()" aria-label="{{ __('Close') }}">
            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <nav class="hide-scrollbar flex min-h-0 flex-1 flex-col gap-0.5 overflow-y-auto overflow-x-hidden px-3 py-4" aria-label="{{ $navAriaLabel }}">
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
              @click="closeDrawer()"
              title="{{ $item['label'] }}"
              class="flex items-center gap-3 rounded-lg border-l-2 py-2.5 pl-2.5 pr-2 text-[13px] font-medium transition-colors {{ $active ? 'border-amber-400 bg-white/[0.07] text-amber-300' : 'border-transparent text-zinc-400 hover:bg-white/[0.05] hover:text-zinc-200' }}"
              :class="railMode ? 'justify-center px-2' : ''"
            >
              <svg class="admin-nav-icon text-current" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
              </svg>
              <span class="min-w-0 flex-1 truncate" x-show="!railMode">{{ $item['label'] }}</span>
            </a>
          @endforeach

          <div class="my-3 shrink-0 border-t border-white/10"></div>

          <a
            href="{{ route('home') }}"
            target="_blank"
            rel="noopener noreferrer"
            @click="closeDrawer()"
            class="flex items-center gap-3 rounded-lg py-2.5 pl-2.5 pr-2 text-[13px] font-medium text-zinc-500 transition hover:bg-white/[0.05] hover:text-zinc-200"
            :class="railMode ? 'justify-center px-2' : ''"
            title="{{ __('View site') }}"
          >
            <svg class="admin-nav-icon text-current" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
            </svg>
            <span class="truncate" x-show="!railMode">{{ __('View site') }}</span>
          </a>

          <a
            href="{{ route('inventory.index') }}"
            target="_blank"
            rel="noopener noreferrer"
            @click="closeDrawer()"
            class="flex items-center gap-3 rounded-lg py-2.5 pl-2.5 pr-2 text-[13px] font-medium text-zinc-500 transition hover:bg-white/[0.05] hover:text-zinc-200"
            :class="railMode ? 'justify-center px-2' : ''"
            title="{{ __('Public inventory') }}"
          >
            <svg class="admin-nav-icon text-current" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span class="truncate" x-show="!railMode">{{ __('Inventory') }}</span>
          </a>
        </nav>

        <div class="mt-auto shrink-0 border-t border-white/10 bg-black/25 p-4">
          <div class="flex items-center gap-3" :class="railMode ? 'flex-col' : ''">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/10 bg-zinc-800/80 text-[10px] font-bold uppercase tracking-wide text-amber-400/90">
              {{ $initials }}
            </div>
            <div class="min-w-0 flex-1" x-show="!railMode" x-cloak>
              <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
              <p class="truncate text-xs text-zinc-500">{{ $user->email }}</p>
            </div>
          </div>
          <div class="mt-3 grid grid-cols-2 gap-2" x-show="!railMode" x-cloak>
            <a href="{{ route('profile.edit') }}" @click="closeDrawer()" class="rounded-md border border-white/10 bg-white/5 py-2 text-center text-xs font-semibold text-white transition hover:bg-white/10">{{ __('Profile') }}</a>
            <form method="POST" action="{{ route('logout') }}" class="contents">
              @csrf
              <button type="submit" class="rounded-md border border-red-400/30 bg-red-500/10 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20">{{ __('Log out') }}</button>
            </form>
          </div>
          <div class="mt-2 flex flex-col gap-1" x-show="railMode" x-cloak>
            <a href="{{ route('profile.edit') }}" class="flex justify-center rounded-md py-2 text-zinc-400 hover:text-white" title="{{ __('Profile') }}">
              <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="flex w-full justify-center rounded-md py-2 text-red-400 hover:text-red-300" title="{{ __('Log out') }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
              </button>
            </form>
          </div>
        </div>
      </aside>

      <div
        class="admin-main-shell min-w-0 pt-0 transition-[margin,width,max-width] duration-300 ease-out"
        :class="{ 'admin-main-shell--rail': railMode }"
      >
        <header class="z-40 flex h-16 shrink-0 items-center gap-4 border-b border-zinc-200/90 bg-white/95 px-4 shadow-sm backdrop-blur-md sm:px-6 lg:px-8">
          <button
            type="button"
            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-700 shadow-sm lg:hidden"
            @click="openDrawer()"
            aria-label="{{ __('Menu') }}"
          >
            <svg class="h-6 w-6 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 6.75h16.5"/></svg>
          </button>
          <div class="min-w-0 flex-1">
            @isset($header)
              <div class="admin-shell-header text-zinc-900">{{ $header }}</div>
            @else
              <p class="truncate text-lg font-semibold tracking-tight text-zinc-900">{{ $brandName }}</p>
            @endif
          </div>
          <a
            href="{{ route('home') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="admin-btn hidden shrink-0 lg:inline-flex"
          >
            <svg class="h-4 w-4 shrink-0 text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
            </svg>
            {{ __('View site') }}
          </a>
        </header>

        <main class="admin-content-scroll admin-scrollbar min-w-0 bg-gradient-to-b from-zinc-100 to-zinc-50 overscroll-contain overflow-x-auto">
          <div class="mx-auto max-w-[1600px] min-w-0 px-4 py-6 sm:px-6 lg:px-10">
            {{ $slot }}
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

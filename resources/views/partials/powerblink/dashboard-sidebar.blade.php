@php
  $user = Auth::user();
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
  $allNavItems = config('powerblink-admin-nav', []);
  $navItems = collect($allNavItems)->filter(fn ($item) => $user && $user->can($item['permission']))->values()->all();
  $homeRoute = 'admin.dashboard';
@endphp
<aside class="hidden lg:flex flex-col w-60 shrink-0 bg-pb-navy text-white h-full">
  <div class="px-5 py-5 border-b border-white/10 shrink-0">
    <a href="{{ route($homeRoute) }}" class="block">
      @if (!empty($logoPath))
        <img src="{{ \App\Support\MediaImageUrl::url($logoPath) }}" alt="" class="h-8 w-8 rounded-full object-cover mb-2" />
      @endif
      <h1 class="font-display font-bold text-sm tracking-tight">{{ $brandName }}</h1>
      <p class="text-[11px] text-white/50 mt-0.5">{{ __('Admin') }}</p>
    </a>
  </div>

  <nav class="flex-1 overflow-y-auto py-2 text-[13px]" aria-label="{{ __('Admin navigation') }}">
    @foreach ($navItems as $item)
      @php $active = request()->routeIs($item['match']); @endphp
      <a href="{{ route($item['route']) }}"
         @if ($active) aria-current="page" @endif
         class="flex items-center gap-3 px-5 py-2.5 transition-colors {{ $active ? 'bg-pb-green/30 text-white border-r-2 border-pb-green-bright' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
        <span>{{ __($item['label']) }}</span>
      </a>
    @endforeach
  </nav>

  <div class="border-t border-white/10 py-3 shrink-0 text-[13px]">
    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-3 px-5 py-2 text-white/70 hover:text-white">
      <span class="material-symbols-outlined text-[18px]">public</span>
      <span>{{ __('View site') }}</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="flex w-full items-center gap-3 px-5 py-2 text-white/70 hover:text-white text-left">
        <span class="material-symbols-outlined text-[18px]">logout</span>
        <span>{{ __('Logout') }}</span>
      </button>
    </form>
  </div>
</aside>

{{-- Mobile drawer nav (controlled by parent layout alpine state) --}}
<aside
  class="lg:hidden fixed inset-y-0 left-0 z-50 flex w-[min(16rem,calc(100vw-2rem))] flex-col bg-pb-navy text-white transition-transform duration-200 text-[13px]"
  x-cloak
  :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
>
  <div class="px-5 py-4 flex items-center justify-between border-b border-white/10">
    <span class="font-display font-bold text-sm">{{ $brandName }}</span>
    <button type="button" class="p-1" @click="drawerOpen = false" aria-label="{{ __('Close') }}">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>
  <nav class="flex-1 overflow-y-auto py-2">
    @foreach ($navItems as $item)
      @php $active = request()->routeIs($item['match']); @endphp
      <a href="{{ route($item['route']) }}" @click="drawerOpen = false"
         class="flex items-center gap-3 px-5 py-2.5 {{ $active ? 'bg-pb-green/30' : 'hover:bg-white/10' }}">
        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
        <span>{{ __($item['label']) }}</span>
      </a>
    @endforeach
  </nav>
</aside>

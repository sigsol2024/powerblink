@php
  $user = Auth::user();
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $allNavItems = config('powerblink-admin-nav', []);
  $navItems = collect($allNavItems)->filter(fn ($item) => $user && $user->can($item['permission']))->values()->all();
  $homeRoute = 'admin.dashboard';
  $isAdminSidebar = $isAdminSidebar ?? request()->routeIs('admin.*');
@endphp

<aside class="hidden lg:flex fixed left-0 top-0 z-40 h-screen w-64 flex-col border-r border-outline-variant bg-surface-container-lowest py-6 px-4 shadow-sm">
  <div class="mb-8 px-2 shrink-0">
    <a href="{{ route($homeRoute) }}" class="block">
      <h1 class="font-headline-md text-headline-md font-extrabold text-primary leading-tight">{{ $brandName }}</h1>
      <p class="font-label-caps text-label-caps text-on-surface-variant opacity-70 tracking-widest mt-1 uppercase">{{ __('Elite admin portal') }}</p>
    </a>
  </div>

  <nav class="flex-grow space-y-1 custom-scrollbar overflow-y-auto" aria-label="{{ __('Admin navigation') }}">
    @foreach ($navItems as $item)
      @php $active = request()->routeIs($item['match']); @endphp
      <a href="{{ route($item['route']) }}"
         @if ($active) aria-current="page" @endif
         class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ $active ? 'bg-secondary-container text-on-secondary-container font-bold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }}">
        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 shrink-0" />
        <span class="font-label-caps text-label-caps">{{ __($item['label']) }}</span>
      </a>
    @endforeach
  </nav>

  @if ($isAdminSidebar)
    <div class="mt-6 shrink-0">
      <a href="{{ route('registration.wizard') }}" class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold font-headline-md text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
        <x-icon name="add" class="w-4 h-4" />
        {{ __('New Registration') }}
      </a>
    </div>
  @endif
</aside>

{{-- Mobile drawer --}}
<aside
  class="lg:hidden fixed inset-y-0 left-0 z-50 flex w-[min(16rem,calc(100vw-2rem))] flex-col border-r border-outline-variant bg-surface-container-lowest py-6 px-4 shadow-xl transition-transform duration-200"
  x-cloak
  :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
>
  <div class="flex items-center justify-between mb-6 px-2 shrink-0">
    <div>
      <span class="font-headline-md text-sm font-extrabold text-primary">{{ $brandName }}</span>
      <p class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest mt-0.5">{{ __('Elite admin portal') }}</p>
    </div>
    <button type="button" class="p-2 rounded-lg hover:bg-surface-container-high" @click="drawerOpen = false" aria-label="{{ __('Close') }}">
      <x-icon name="close" class="w-5 h-5" />
    </button>
  </div>
  <nav class="flex-1 overflow-y-auto space-y-1 custom-scrollbar">
    @foreach ($navItems as $item)
      @php $active = request()->routeIs($item['match']); @endphp
      <a href="{{ route($item['route']) }}" @click="drawerOpen = false"
         class="flex items-center gap-3 px-4 py-3 rounded-lg {{ $active ? 'bg-secondary-container text-on-secondary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 shrink-0" />
        <span class="font-label-caps text-label-caps text-sm">{{ __($item['label']) }}</span>
      </a>
    @endforeach
  </nav>
  @if ($isAdminSidebar)
    <a href="{{ route('registration.wizard') }}" @click="drawerOpen = false" class="mt-4 w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-sm flex items-center justify-center gap-2">
      <x-icon name="add" class="w-4 h-4" />
      {{ __('New Registration') }}
    </a>
  @endif
</aside>

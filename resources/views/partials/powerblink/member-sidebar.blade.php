@php
  $user = Auth::user();
  $role = $user?->isParent() ? 'parent' : ($user?->isPlayer() ? 'player' : ($user?->isCoach() ? 'coach' : 'member'));
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);

  $parentNav = [
    ['route' => 'portal.dashboard', 'match' => 'portal.dashboard', 'label' => __('Dashboard'), 'icon' => 'dashboard'],
    ['route' => 'profile.edit', 'match' => 'profile.*', 'label' => __('My Children'), 'icon' => 'family_restroom'],
    ['route' => 'profile.edit', 'match' => 'parent.*', 'label' => __('Registrations'), 'icon' => 'how_to_reg'],
    ['route' => 'profile.edit', 'match' => 'parent.schedule*', 'label' => __('Schedule'), 'icon' => 'calendar_month'],
    ['route' => 'profile.edit', 'match' => 'parent.attendance*', 'label' => __('Attendance'), 'icon' => 'fact_check'],
    ['route' => 'profile.edit', 'match' => 'parent.performance*', 'label' => __('Performance'), 'icon' => 'analytics'],
    ['route' => 'profile.edit', 'match' => 'parent.payments*', 'label' => __('Payments'), 'icon' => 'payments'],
    ['route' => 'profile.edit', 'match' => 'parent.documents*', 'label' => __('Documents'), 'icon' => 'folder'],
    ['route' => 'profile.edit', 'match' => 'parent.announcements*', 'label' => __('Announcements'), 'icon' => 'campaign'],
  ];

  $playerNav = [
    ['route' => 'portal.dashboard', 'match' => 'portal.dashboard', 'label' => __('Dashboard'), 'icon' => 'dashboard'],
    ['route' => 'profile.edit', 'match' => 'profile.*', 'label' => __('My Profile'), 'icon' => 'person'],
    ['route' => 'profile.edit', 'match' => 'player.schedule*', 'label' => __('Schedule'), 'icon' => 'calendar_month'],
    ['route' => 'profile.edit', 'match' => 'player.attendance*', 'label' => __('Attendance'), 'icon' => 'fact_check'],
    ['route' => 'profile.edit', 'match' => 'player.performance*', 'label' => __('Performance'), 'icon' => 'analytics'],
    ['route' => 'profile.edit', 'match' => 'player.announcements*', 'label' => __('Announcements'), 'icon' => 'campaign'],
  ];

  $coachNav = [
    ['route' => 'portal.dashboard', 'match' => 'portal.dashboard', 'label' => __('Dashboard'), 'icon' => 'dashboard'],
    ['route' => 'profile.edit', 'match' => 'coach.players*', 'label' => __('My Squads'), 'icon' => 'groups'],
    ['route' => 'profile.edit', 'match' => 'coach.sessions*', 'label' => __('Training Schedule'), 'icon' => 'calendar_month'],
    ['route' => 'profile.edit', 'match' => 'coach.attendance*', 'label' => __('Attendance'), 'icon' => 'fact_check'],
    ['route' => 'profile.edit', 'match' => 'coach.performance*', 'label' => __('Performance Reports'), 'icon' => 'analytics'],
    ['route' => 'profile.edit', 'match' => 'coach.tournaments*', 'label' => __('Tournaments'), 'icon' => 'emoji_events'],
    ['route' => 'profile.edit', 'match' => 'coach.announcements*', 'label' => __('Announcements'), 'icon' => 'campaign'],
  ];

  $navItems = match ($role) {
    'parent' => $parentNav,
    'player' => $playerNav,
    'coach' => $coachNav,
    default => [['route' => 'portal.dashboard', 'match' => 'portal.dashboard', 'label' => __('Dashboard'), 'icon' => 'dashboard']],
  };

  $portalLabel = match ($role) {
    'parent' => __('Parent Portal'),
    'player' => __('Player Portal'),
    'coach' => __('Coach Portal'),
    default => __('Member Portal'),
  };
@endphp

<aside class="hidden lg:flex fixed left-0 top-0 z-40 h-screen w-64 flex-col border-r border-outline-variant bg-surface-container-lowest py-6 px-4 shadow-sm">
  <div class="mb-8 px-2 shrink-0">
    <a href="{{ route('portal.dashboard') }}" class="block">
      <h1 class="font-headline-md text-headline-md font-extrabold text-primary leading-tight">{{ $brandName }}</h1>
      <p class="font-label-caps text-label-caps text-on-surface-variant opacity-70 tracking-widest mt-1 uppercase">{{ $portalLabel }}</p>
    </a>
  </div>
  <nav class="flex-grow space-y-1 custom-scrollbar overflow-y-auto" aria-label="{{ $portalLabel }}">
    @foreach ($navItems as $item)
      @php $active = request()->routeIs($item['match']); @endphp
      <a href="{{ route($item['route']) }}"
         @if ($active) aria-current="page" @endif
         class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ $active ? 'bg-secondary-container text-on-secondary-container font-bold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }}">
        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 shrink-0" />
        <span class="font-label-caps text-label-caps text-sm">{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>
  <div class="mt-6 shrink-0 border-t border-outline-variant pt-4">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high text-left text-sm">
        <x-icon name="logout" class="w-5 h-5" />
        <span>{{ __('Logout') }}</span>
      </button>
    </form>
  </div>
</aside>

<aside
  class="lg:hidden fixed inset-y-0 left-0 z-50 flex w-[min(16rem,calc(100vw-2rem))] flex-col border-r border-outline-variant bg-surface-container-lowest py-6 px-4 shadow-xl transition-transform duration-200"
  x-cloak
  :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
>
  <div class="flex items-center justify-between mb-6 px-2 shrink-0">
    <div>
      <span class="font-headline-md text-sm font-extrabold text-primary">{{ $brandName }}</span>
      <p class="font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest mt-0.5">{{ $portalLabel }}</p>
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
        <span class="text-sm">{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>
  <form method="POST" action="{{ route('logout') }}" class="mt-4">
    @csrf
    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:bg-surface-container-high text-left text-sm">
      <x-icon name="logout" class="w-5 h-5" />
      {{ __('Logout') }}
    </button>
  </form>
</aside>

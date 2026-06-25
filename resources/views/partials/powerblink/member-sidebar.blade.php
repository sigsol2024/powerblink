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
<aside class="hidden lg:flex flex-col w-60 shrink-0 bg-pb-navy text-white h-full">
  <div class="px-5 py-5 border-b border-white/10 shrink-0">
    <h1 class="font-display font-bold text-sm">{{ $brandName }}</h1>
    <p class="text-[11px] text-white/50 mt-0.5">{{ $portalLabel }}</p>
  </div>
  <nav class="flex-1 overflow-y-auto py-2 text-[13px]" aria-label="{{ $portalLabel }}">
    @foreach ($navItems as $item)
      @php $active = request()->routeIs($item['match']); @endphp
      <a href="{{ route($item['route']) }}"
         class="flex items-center gap-3 px-5 py-2.5 transition-colors {{ $active ? 'bg-pb-green/30 border-r-2 border-pb-green-bright' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
        <span>{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>
  <div class="border-t border-white/10 py-3 shrink-0">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="flex w-full items-center gap-3 px-5 py-2 text-white/70 hover:text-white text-left text-[13px]">
        <span class="material-symbols-outlined text-[18px]">logout</span>
        <span>{{ __('Logout') }}</span>
      </button>
    </form>
  </div>
</aside>

<aside
  class="lg:hidden fixed inset-y-0 left-0 z-50 flex w-[min(16rem,calc(100vw-2rem))] flex-col bg-pb-navy text-white transition-transform duration-200 text-[13px]"
  x-cloak
  :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
>
  <div class="px-5 py-4 flex justify-between items-center border-b border-white/10">
    <span class="font-display font-bold text-sm">{{ $portalLabel }}</span>
    <button type="button" @click="drawerOpen = false" aria-label="{{ __('Close') }}">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>
  <nav class="flex-1 overflow-y-auto py-2">
    @foreach ($navItems as $item)
      <a href="{{ route($item['route']) }}" @click="drawerOpen = false"
         class="flex items-center gap-3 px-5 py-2.5 hover:bg-white/10">
        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
        <span>{{ $item['label'] }}</span>
      </a>
    @endforeach
  </nav>
</aside>

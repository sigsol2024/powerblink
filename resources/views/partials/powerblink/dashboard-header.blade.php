@php
  $user = Auth::user();
  $n = trim((string) ($user->name ?? 'User'));
  $initials = strtoupper(substr($n, 0, 1).(str_contains($n, ' ') ? substr($n, (int) strrpos($n, ' ') + 1, 1) : ''));
  $initials = strlen($initials) > 2 ? substr($initials, 0, 2) : $initials;
  $pageTitle = $title ?? ($header ?? null);
@endphp
<header class="shrink-0 border-b border-pb-border bg-pb-surface px-4 md:px-6 py-3 flex items-center justify-between gap-4">
  <div class="flex items-center gap-3 min-w-0">
    <button type="button" class="lg:hidden pb-touch p-2 text-pb-navy rounded-lg hover:bg-pb-bg" @click="drawerOpen = true" aria-label="{{ __('Menu') }}">
      <span class="material-symbols-outlined">menu</span>
    </button>
    <div class="min-w-0">
      @if (isset($header))
        {{ $header }}
      @else
        <h1 class="text-lg font-display font-bold text-pb-navy truncate">{{ $pageTitle }}</h1>
      @endif
    </div>
  </div>

  <div class="flex items-center gap-2 shrink-0" x-data="{ menuOpen: false }">
    @php $unreadNotifications = $user->unreadNotifications()->count(); @endphp
    <a href="{{ route('notifications.index') }}" class="relative pb-touch p-2 rounded-lg hover:bg-pb-bg text-pb-navy" aria-label="{{ __('Notifications') }}">
      <span class="material-symbols-outlined">notifications</span>
      @if ($unreadNotifications > 0)
        <span class="absolute -top-0.5 -right-0.5 min-w-[1.125rem] h-[1.125rem] px-1 rounded-full bg-pb-green-bright text-pb-navy text-[10px] font-bold flex items-center justify-center">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
      @endif
    </a>
    <a href="{{ route('profile.edit') }}" class="hidden sm:flex items-center gap-2 text-sm text-pb-muted hover:text-pb-navy">
      <span class="w-8 h-8 rounded-full bg-pb-navy text-white flex items-center justify-center text-xs font-semibold">{{ $initials }}</span>
      <span class="max-w-[8rem] truncate">{{ $user->name }}</span>
    </a>
    <div class="relative">
      <button type="button" @click="menuOpen = !menuOpen" class="p-1.5 rounded-lg hover:bg-pb-bg" aria-label="{{ __('Account menu') }}">
        <span class="material-symbols-outlined text-pb-navy">more_vert</span>
      </button>
      <div x-show="menuOpen" x-cloak @click.outside="menuOpen = false"
           class="absolute right-0 mt-1 w-48 bg-white border border-pb-border rounded-lg shadow-lg py-1 z-50 text-sm">
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-pb-bg">{{ __('My account') }}</a>
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="block px-4 py-2 hover:bg-pb-bg">{{ __('View site') }}</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-2 hover:bg-pb-bg text-red-700">{{ __('Logout') }}</button>
        </form>
      </div>
    </div>
  </div>
</header>

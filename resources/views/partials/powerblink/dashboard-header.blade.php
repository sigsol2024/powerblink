@php
  $user = Auth::user();
  $n = trim((string) ($user->name ?? 'User'));
  $initials = strtoupper(substr($n, 0, 1).(str_contains($n, ' ') ? substr($n, (int) strrpos($n, ' ') + 1, 1) : ''));
  $initials = strlen($initials) > 2 ? substr($initials, 0, 2) : $initials;
  $pageTitle = $title ?? ($header ?? __('Admin Dashboard'));
@endphp
<header class="fixed top-0 right-0 z-30 flex h-16 w-full items-center justify-between border-b border-outline-variant bg-surface/80 px-margin-mobile backdrop-blur-md md:w-[calc(100%-16rem)] md:px-margin-desktop">
  <div class="flex min-w-0 items-center gap-4">
    <button type="button" class="lg:hidden pb-touch rounded-lg p-2 hover:bg-surface-container-high" @click="drawerOpen = true" aria-label="{{ __('Menu') }}">
      <x-icon name="menu" class="w-6 h-6 text-primary" />
    </button>
    <div class="min-w-0">
      @if (isset($header))
        {{ $header }}
      @else
        <h2 class="font-headline-md text-headline-md font-bold text-primary truncate">{{ $pageTitle }}</h2>
      @endif
    </div>
  </div>

  <div class="flex shrink-0 items-center gap-4 md:gap-6" x-data="{ menuOpen: false }">
    <div class="hidden lg:flex relative">
      <x-icon name="search" class="absolute left-3 top-1/2 w-4 h-4 -translate-y-1/2 text-on-surface-variant pointer-events-none" />
      <input type="search" class="w-64 rounded-full border-none bg-surface-container-low py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-secondary" placeholder="{{ __('Search players, payments...') }}" aria-label="{{ __('Search') }}" />
    </div>

    @php $unreadNotifications = $user->unreadNotifications()->count(); @endphp
    <div class="flex items-center gap-3">
      <a href="{{ route('notifications.index') }}" class="relative pb-touch text-on-surface-variant transition-colors hover:text-primary" aria-label="{{ __('Notifications') }}">
        <x-icon name="notifications" class="w-5 h-5" />
        @if ($unreadNotifications > 0)
          <span class="absolute -top-0.5 -right-0.5 flex h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-secondary px-1 text-[10px] font-bold text-on-secondary">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
        @endif
      </a>
      <a href="{{ route('contact') }}" class="hidden sm:block pb-touch text-on-surface-variant transition-colors hover:text-primary" aria-label="{{ __('Help') }}">
        <x-icon name="help_outline" class="w-5 h-5" />
      </a>
      <a href="{{ route('profile.edit') }}" class="h-8 w-8 overflow-hidden rounded-full border-2 border-secondary flex items-center justify-center bg-primary-container text-on-primary text-xs font-bold" title="{{ $user->name }}">
        {{ $initials }}
      </a>
      <div class="relative hidden sm:block">
        <button type="button" @click="menuOpen = !menuOpen" class="pb-touch rounded-full p-2 hover:bg-surface-container-high" aria-label="{{ __('Account menu') }}">
          <x-icon name="more_vert" class="w-5 h-5 text-on-surface-variant" />
        </button>
        <div x-show="menuOpen" x-cloak @click.outside="menuOpen = false"
             class="absolute right-0 mt-1 w-48 rounded-xl border border-outline-variant bg-surface-container-lowest py-1 shadow-lg z-50 text-sm">
          <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-surface-container-low">{{ __('My account') }}</a>
          <a href="{{ route('home') }}" target="_blank" rel="noopener" class="block px-4 py-2 hover:bg-surface-container-low">{{ __('View site') }}</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-surface-container-low text-error">{{ __('Logout') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</header>

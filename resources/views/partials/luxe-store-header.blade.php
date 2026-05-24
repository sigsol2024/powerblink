@php
  $site = $site ?? [];
  $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : 'ADÉ LUXE';
  $cartCount = \App\Support\Cart::count();
  $shopActive = request()->routeIs('shop.index', 'inventory.index', 'product.show', 'inventory.show');
  $cartActive = request()->routeIs('cart.*');
@endphp
<header class="fixed top-0 w-full z-50 flex justify-between items-center px-margin-mobile md:px-gutter py-4 bg-background/95 backdrop-blur-sm border-b border-outline-variant luxe-store">
  <div class="flex items-center gap-4 md:gap-8 min-w-0">
    <a href="{{ route('home') }}" class="font-display-lg text-[22px] sm:text-display-lg-mobile md:text-display-lg text-primary uppercase tracking-tighter truncate">
      {{ strtoupper($brandName) }}
    </a>
    <nav class="hidden md:flex gap-6 lg:gap-8">
      <a href="{{ route('shop.index') }}" class="font-body-md text-body-md tracking-widest py-1 {{ $shopActive ? 'text-primary font-bold border-b border-primary' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}">{{ __('COLLECTIONS') }}</a>
      <a href="{{ route('about') }}" class="font-body-md text-body-md tracking-widest text-on-surface-variant hover:text-primary transition-colors duration-300 py-1">{{ __('STORY') }}</a>
      <a href="{{ route('contact') }}" class="font-body-md text-body-md tracking-widest text-on-surface-variant hover:text-primary transition-colors duration-300 py-1">{{ __('CONTACT') }}</a>
    </nav>
  </div>
  <div class="flex items-center gap-4 md:gap-6 shrink-0">
    <a href="{{ route('shop.index') }}" class="material-symbols-outlined text-primary hover:scale-110 transition-transform hidden sm:inline" aria-label="{{ __('Search') }}">search</a>
    <a href="{{ route('cart.index') }}" class="material-symbols-outlined text-primary hover:scale-110 transition-transform relative {{ $cartActive ? 'font-bold' : '' }}" aria-label="{{ __('Bag') }}">
      shopping_bag
      @if ($cartCount > 0)
        <span class="absolute -top-1 -right-1 min-w-[1rem] h-4 px-1 bg-primary text-on-primary text-[9px] font-bold flex items-center justify-center">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
      @endif
    </a>
    @auth
      <a href="{{ route('dashboard') }}" class="material-symbols-outlined text-primary hover:scale-110 transition-transform hidden md:inline" aria-label="{{ __('Account') }}">person</a>
    @else
      <a href="{{ route('login') }}" class="material-symbols-outlined text-primary hover:scale-110 transition-transform hidden md:inline" aria-label="{{ __('Sign in') }}">person</a>
    @endauth
    <button type="button" class="material-symbols-outlined text-primary md:hidden" data-luxe-mobile-nav-toggle aria-expanded="false" aria-controls="luxe-mobile-nav">menu</button>
  </div>
</header>
<nav id="luxe-mobile-nav" class="luxe-store fixed top-[65px] inset-x-0 z-40 bg-background border-b border-outline-variant px-margin-mobile py-4 flex flex-col gap-3 md:hidden hidden" data-luxe-mobile-nav>
  <a href="{{ route('shop.index') }}" class="font-label-caps text-label-caps tracking-widest {{ $shopActive ? 'text-primary' : 'text-on-surface-variant' }}">{{ __('COLLECTIONS') }}</a>
  <a href="{{ route('about') }}" class="font-label-caps text-label-caps tracking-widest text-on-surface-variant">{{ __('STORY') }}</a>
  <a href="{{ route('contact') }}" class="font-label-caps text-label-caps tracking-widest text-on-surface-variant">{{ __('CONTACT') }}</a>
  @auth
    <a href="{{ route('dashboard') }}" class="font-label-caps text-label-caps tracking-widest text-on-surface-variant">{{ __('ACCOUNT') }}</a>
  @else
    <a href="{{ route('login') }}" class="font-label-caps text-label-caps tracking-widest text-on-surface-variant">{{ __('SIGN IN') }}</a>
  @endauth
</nav>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.querySelector('[data-luxe-mobile-nav-toggle]');
    const nav = document.querySelector('[data-luxe-mobile-nav]');
    if (!btn || !nav) return;
    btn.addEventListener('click', function () {
      nav.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', nav.classList.contains('hidden') ? 'false' : 'true');
    });
  });
</script>

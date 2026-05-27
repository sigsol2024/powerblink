<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
      $site = $site ?? [];
      $siteDisplayName = \App\Support\SiteBrand::displayName($site);
      $loaderLogoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
    @endphp
    <title>@if(!empty($title ?? null)){{ $title }} | @endif{{ $siteDisplayName }}</title>
    @if (!empty($metaDescription))
      <meta name="description" content="{{ $metaDescription }}" />
    @endif
    @if (!empty($canonicalUrl))
      <link rel="canonical" href="{{ $canonicalUrl }}" />
    @endif
    @if (!empty($ogTitle))
      <meta property="og:title" content="{{ $ogTitle }}" />
      <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? '' }}" />
      <meta property="og:type" content="website" />
      <meta property="og:url" content="{{ $ogUrl ?? $canonicalUrl ?? url()->current() }}" />
      @if (!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}" />
      @endif
      <meta name="twitter:card" content="summary_large_image" />
    @endif


    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;800;900&family=Work+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    {{-- High-visibility icons have been migrated to inline SVGs (<x-icon>). Anything that still
         uses the legacy icon-font class loads the webfont with `block` so the glyph either
         renders or stays invisible — never as raw words during the font load. --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet" />
    <style>
      /* Hide the icon-font slot until the font has rendered glyphs (extra belt-and-braces). */
      .material-symbols-outlined { font-feature-settings: 'liga'; line-height: 1; }
    </style>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: '#ffb129',
              brand_blue: '#4e77ed',
              brand_orange: '#f9a825',
              card_bg: 'rgba(34,39,45,0.95)',
              sold_red: '#e94343',
              on_surface: '#191c1e',
              page_bg: '#f8f9fc',
            },
            fontFamily: {
              headline: ['Epilogue', 'sans-serif'],
              body: ['Work Sans', 'sans-serif'],
              label: ['Inter', 'sans-serif'],
            },
          },
        },
      };
    </script>
    @include('partials.luxe-public-theme')
    <link rel="stylesheet" href="{{ asset('asset/css/site.css') }}" />
    @if (!empty($site['favicon_path'] ?? ''))
      <link rel="icon" href="{{ \App\Support\VehicleImageUrl::url($site['favicon_path']) }}" />
    @endif
    @stack('head')
    {{-- Homepage hero slider + other luxe interactions use Alpine. --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
  </head>

  @php
    $luxeStorefront = request()->routeIs([
      'home', 'shop.index', 'inventory.index', 'product.show', 'inventory.show',
      'cart.*', 'checkout.*', 'order.confirmed', 'order.show', 'orders.lookup.index', 'orders.lookup',
      'about', 'contact', 'faq', 'privacy-policy', 'terms', 'makes.index',
    ]);
    $luxeHome = request()->routeIs('home');
    $luxeShopPage = request()->routeIs('shop.index', 'inventory.index');
    // Pages that already handle their own top padding (or full-bleed hero). Everything else under
    // the luxe header needs a default offset so content isn't hidden behind the fixed bar.
    $luxeSelfPadded = request()->routeIs(
      'home', 'shop.index', 'inventory.index', 'product.show', 'inventory.show',
      'cart.*', 'checkout.*', 'order.confirmed', 'order.placed', 'order.show', 'orders.lookup.index', 'orders.lookup'
    );
    $luxeShowCartWidget = $luxeStorefront && ! request()->routeIs('cart.*', 'checkout.*', 'order.placed', 'order.confirmed');
  @endphp
  <body class="{{ $luxeStorefront ? 'bg-background text-on-background font-body-md selection:bg-secondary-fixed-dim selection:text-on-secondary-fixed luxe-store' : 'bg-page_bg font-body text-on_surface selection:bg-brand_blue/20' }} {{ $bodyClass ?? '' }}">
    @if ($luxeStorefront)
      @include('partials.luxe-store-header')
      @if ($luxeShowCartWidget)
        @include('partials.luxe-cart-widget')
      @endif
    @else
      @include('partials.header')
    @endif
    <main id="main" class="{{ $luxeStorefront && ! $luxeSelfPadded ? 'pt-20 md:pt-24' : '' }}">
      @yield('content')
    </main>
    @if ($luxeHome)
      @include('partials.luxe-home-footer', ['site' => $site ?? []])
    @elseif ($luxeShopPage)
      @include('partials.luxe-shop-footer', ['site' => $site ?? []])
    @elseif ($luxeStorefront)
      <footer class="luxe-store border-t border-outline-variant py-10 px-margin-mobile md:px-gutter font-label-caps text-label-caps text-on-surface-variant">
        <div class="max-w-max-container mx-auto flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 mb-6">
          <a href="{{ route('about') }}" class="hover:text-primary transition-colors">{{ __('ABOUT US') }}</a>
          <a href="{{ route('contact') }}" class="hover:text-primary transition-colors">{{ __('CONTACT US') }}</a>
        </div>
        <p class="text-center">© {{ date('Y') }} {{ \App\Support\SiteBrand::displayName($site ?? []) }}. {{ __('ALL RIGHTS RESERVED.') }}</p>
      </footer>
    @else
      @include('partials.footer')
    @endif
    @include('partials.whatsapp-widget')
    <script src="{{ asset('asset/js/main.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>
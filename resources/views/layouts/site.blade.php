<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth site-is-loading">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
      $site = $site ?? [];
      $siteDisplayName = \App\Support\SiteBrand::displayName($site);
    @endphp
    @include('partials.site-page-loader-styles')
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
    @include('partials.powerblink.theme')
    <link rel="stylesheet" href="{{ asset('asset/css/site.css') }}" />
    @if (!empty($site['favicon_path'] ?? ''))
      <link rel="icon" href="{{ \App\Support\MediaImageUrl::url($site['favicon_path']) }}" />
    @endif
    @stack('head')
  </head>

  @php
    $useMinimalHeader = request()->routeIs('registration.*');
  @endphp
  <body class="bg-background text-on-background font-body-md min-h-screen flex flex-col selection:bg-secondary/30 pb-mobile-safe {{ $bodyClass ?? '' }}">
    @include('partials.site-page-loader')
    @if ($useMinimalHeader)
      @include('partials.powerblink.public-header-minimal')
    @else
      @include('partials.powerblink.public-header')
    @endif
    <main id="main" class="flex-1 {{ $useMinimalHeader ? '' : 'pt-20' }}">
      @yield('content')
    </main>
    @include('partials.powerblink.public-footer', ['site' => $site ?? []])
    @include('partials.whatsapp-widget')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="{{ asset('asset/js/main.js') }}" defer></script>
    @stack('scripts')
    @unless($useMinimalHeader)
      @include('partials.powerblink.fade-up-scripts')
    @endunless
  </body>
</html>

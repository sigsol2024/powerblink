@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@if(!empty($title ?? null)){{ $title }} | @endif{{ $brandName }}</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  @include('partials.powerblink.theme')
  @include('partials.powerblink.theme-styles')
  @stack('head')
</head>
<body class="bg-background text-on-background font-body-md min-h-screen pb-mobile-safe">
  @include('partials.powerblink.registration-header', ['site' => $site])
  <main class="pt-24 pb-section-gap px-margin-mobile md:px-margin-desktop">
    @yield('content')
  </main>
  @stack('scripts')
</body>
</html>

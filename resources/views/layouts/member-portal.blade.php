@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $user = Auth::user();
  $mediaUploadUrl = route('dashboard.api.media.upload');
  $mediaListUrl = route('dashboard.api.media');
  $viteReady = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
  $isFullBleed = $fullBleed ?? false;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ isset($title) ? $title.' — ' : '' }}{{ $brandName }}</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  @include('partials.powerblink.theme')
  @stack('head')
  <style>[x-cloak]{display:none!important}</style>
  @stack('scripts')
  @if ($viteReady)
    @vite(['resources/js/app.js'])
  @else
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
  @endif
</head>
<body class="bg-background text-on-surface font-body-md antialiased overflow-x-hidden min-h-screen" x-data="{ drawerOpen: false }" @keydown.escape.window="drawerOpen = false">
  <div class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-show="drawerOpen" x-cloak x-transition.opacity @click="drawerOpen = false" aria-hidden="true"></div>

  @include('partials.powerblink.member-sidebar')

  <main class="md:ml-64 min-h-screen">
    @include('partials.powerblink.dashboard-header')

    <div class="pt-16 min-h-screen flex flex-col">
      <div class="flex-1 w-full {{ $isFullBleed ? '' : 'px-margin-mobile md:px-margin-desktop py-6 md:py-8' }}">
        {{ $slot }}
      </div>
      @include('partials.powerblink.dashboard-footer')
    </div>
  </main>
  @stack('body-end')
</body>
</html>

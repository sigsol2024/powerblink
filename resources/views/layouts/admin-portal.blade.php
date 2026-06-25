@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $user = Auth::user();
  $mediaUploadUrl = ($user && $user->can('media.manage'))
      ? route('admin.media.upload')
      : route('dashboard.api.media.upload');
  $mediaListUrl = ($user && $user->can('media.manage'))
      ? route('admin.media.list')
      : route('dashboard.api.media');
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
  @stack('scripts')
  @if ($viteReady)
    @vite(['resources/js/app.js'])
  @else
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
  @endif
</head>
<body class="font-body text-pb-navy antialiased h-full overflow-hidden bg-pb-bg" x-data="{ drawerOpen: false }" @keydown.escape.window="drawerOpen = false">
  <div class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-show="drawerOpen" x-cloak x-transition.opacity @click="drawerOpen = false" aria-hidden="true"></div>

  <div class="flex h-screen w-full overflow-hidden">
    @include('partials.powerblink.dashboard-sidebar')

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
      @include('partials.powerblink.dashboard-header')

      <div class="flex-1 min-h-0 overflow-y-auto">
        @if ($isFullBleed)
          {{ $slot }}
        @else
          <div class="max-w-container mx-auto w-full p-4 md:p-6">
            {{ $slot }}
          </div>
        @endif
      </div>

      @include('partials.powerblink.dashboard-footer')
    </div>
  </div>

  @include('partials.media-modal', ['mediaUploadUrl' => $mediaUploadUrl])
  <input type="hidden" id="media-list-url" value="{{ $mediaListUrl }}" />
  @unless (request()->routeIs('admin.pages.edit'))
    @include('partials.media-modal-pickers')
  @endunless
  @stack('body-end')
</body>
</html>

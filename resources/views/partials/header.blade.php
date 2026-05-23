@php
  $site = $site ?? [];
  $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : config('app.name', 'Site');
  $logoPath = $site['logo_path'] ?? $site['logo_url'] ?? null;
  $hoursLabel = trim((string) ($site['dealer_hours_label'] ?? '')) ?: __('Work Hours');
  $hoursLines = preg_split('/\r\n|\r|\n/', (string) ($site['dealer_sales_hours'] ?? '')) ?: [];
  $hoursSnippet = trim((string) ($hoursLines[0] ?? ''));
  $address = trim((string) ($site['dealer_address'] ?? ''));
  $phone = trim((string) ($site['dealer_phone'] ?? ''));
  if ($phone === '') {
      $phone = trim((string) ($site['dealer_sales_phone'] ?? ''));
  }
  $socialFacebook = trim((string) ($site['social_facebook'] ?? ''));
  $socialInstagram = trim((string) ($site['social_instagram'] ?? ''));
  $socialLinkedin = trim((string) ($site['social_linkedin'] ?? ''));
  $socialYoutube = trim((string) ($site['social_youtube'] ?? ''));
  $compareCount = \App\Support\Compare::count();
  $isHome = request()->routeIs('home') || request()->routeIs('faq') || request()->routeIs('about');
  $inventoryUrl = route('inventory.index');
  $nigerianTypeId = \App\Support\VehicleListingCatalog::vehicleOriginTypeIdByLabel('Nigerian');
  $foreignTypeId = \App\Support\VehicleListingCatalog::vehicleOriginTypeIdByLabel('Foreign');
  $inventoryNigeriaUrl = $nigerianTypeId ? route('inventory.index', ['type_listing_option_id' => $nigerianTypeId]) : $inventoryUrl;
  $inventoryForeignUrl = $foreignTypeId ? route('inventory.index', ['type_listing_option_id' => $foreignTypeId]) : $inventoryUrl;
  $inventoryActive = request()->routeIs('inventory.index') || request()->routeIs('makes.index');
  $currentTypeId = (int) request('type_listing_option_id', 0);
  $nigeriaActive = $inventoryActive && $nigerianTypeId && $currentTypeId === $nigerianTypeId;
  $foreignActive = $inventoryActive && $foreignTypeId && $currentTypeId === $foreignTypeId;
  $navMakesTop = \App\Support\VehicleListingCatalog::activeMakeNavTopTiles(6);
  $faqNavItems = $faqNavItems ?? [];
  $faqUrl = route('faq');
  $cmsNavActive = $cmsNavActive ?? [];
  $navOn = static fn (string $slug): bool => (bool) ($cmsNavActive[$slug] ?? true);
@endphp

{{-- Motors dealer-two inspired public header: https://motors.stylemixthemes.com/elementor-dealer-two/ --}}
<header class="{{ $isHome ? 'fixed inset-x-0 top-0 is-home-header' : 'sticky top-0' }} z-50 shadow-[0_6px_20px_rgba(0,0,0,0.16)]" data-site-header>
  @if ($isHome)
    <style>
      [data-site-header].is-home-header { box-shadow: none; }
      [data-site-header].is-home-header [data-site-header-main] { background-color: transparent; border-color: transparent; }
      [data-site-header].is-home-header.is-scrolled { box-shadow: 0 6px 20px rgba(0, 0, 0, 0.14); }
      [data-site-header].is-home-header.is-scrolled [data-site-header-main] { background-color: rgba(255, 255, 255, 0.74); border-color: rgba(15, 23, 42, 0.16); backdrop-filter: blur(10px); }
      [data-site-header].is-home-header.is-scrolled [data-header-logo],
      [data-site-header].is-home-header.is-scrolled [data-header-icon],
      [data-site-header].is-home-header.is-scrolled [data-header-action-text],
      [data-site-header].is-home-header.is-scrolled [data-header-menu-icon] { color: #111827 !important; }
      [data-site-header].is-home-header.is-scrolled [data-header-nav-link] { color: rgba(17, 24, 39, 0.92) !important; }
      [data-site-header].is-home-header.is-scrolled [data-header-nav-link]:hover { color: #111827 !important; }
      [data-site-header].is-home-header.is-scrolled [data-header-menu-button],
      [data-site-header].is-home-header.is-scrolled [data-header-account-link] { border-color: rgba(15, 23, 42, 0.2) !important; }
      [data-site-header].is-home-header.is-scrolled [data-header-account-link] { color: #111827 !important; background-color: rgba(255, 255, 255, 0.35) !important; }
    </style>
  @endif
  @if (request()->routeIs('about'))
    <style>
      [data-site-header].is-home-header [data-header-logo],
      [data-site-header].is-home-header [data-header-icon],
      [data-site-header].is-home-header [data-header-action-text],
      [data-site-header].is-home-header [data-header-menu-icon] { color: #111827 !important; }
      [data-site-header].is-home-header [data-header-nav-link] { color: rgba(17, 24, 39, 0.92) !important; }
      [data-site-header].is-home-header [data-header-nav-link]:hover { color: #111827 !important; }
      [data-site-header].is-home-header [data-header-menu-button],
      [data-site-header].is-home-header [data-header-account-link] { border-color: rgba(15, 23, 42, 0.2) !important; }
      [data-site-header].is-home-header [data-header-account-link] { color: #111827 !important; background-color: rgba(255, 255, 255, 0.28) !important; }
    </style>
  @endif
  <div class="h-10 border-b border-white/10 bg-[#232628]">
    <div class="mx-auto flex h-full w-full max-w-[1280px] items-center justify-between px-4 sm:px-6 lg:px-8">
      <div></div>

      <div class="hidden xl:flex items-center gap-6 text-[12px] font-semibold tracking-[0.01em] text-white/90">
        @if ($phone !== '')
          <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="inline-flex items-center gap-1.5 text-white/90 hover:text-white">
            <span class="material-symbols-outlined text-[18px] text-[#1280DF]">call</span>
            <span>{{ $phone }}</span>
          </a>
        @endif
        @if ($address !== '')
          <span class="inline-flex min-w-0 items-center gap-1.5 text-white/80">
            <span class="material-symbols-outlined text-[18px] text-[#1280DF]">location_on</span>
            <span class="truncate max-w-[370px]">{{ $address }}</span>
          </span>
        @endif
        @if ($hoursSnippet !== '')
          <span class="inline-flex items-center gap-1.5 text-white/90">
            <span class="material-symbols-outlined text-[18px] text-[#1280DF]">schedule</span>
            <span>{{ $hoursLabel }}</span>
            <span class="text-white/60">&nbsp;{{ $hoursSnippet }}</span>
          </span>
        @endif
      </div>

      <div class="flex items-center gap-2.5">
        @foreach (['facebook' => $socialFacebook, 'instagram' => $socialInstagram, 'linkedin' => $socialLinkedin, 'youtube' => $socialYoutube] as $net => $url)
          @if (!empty($url) && $url !== '#')
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-7 w-7 items-center justify-center text-white/60 transition-colors hover:text-white" aria-label="{{ ucfirst($net) }}">
              <span class="material-symbols-outlined text-[19px]">
                @if ($net === 'facebook')
                  thumb_up
                @elseif ($net === 'instagram')
                  photo_camera
                @elseif ($net === 'linkedin')
                  group
                @else
                  smart_display
                @endif
              </span>
            </a>
          @endif
        @endforeach
      </div>
    </div>
  </div>

  <div class="h-[90px] transition-all duration-300 {{ $isHome ? 'border-b border-transparent bg-transparent' : 'border-b border-white/10 bg-[#232628]' }}" data-site-header-main>
    <div class="relative mx-auto flex h-full w-full max-w-[1280px] items-center justify-between px-4 sm:px-6 lg:px-8">
      <a href="{{ route('home') }}" class="relative z-20 flex min-w-0 shrink items-center xl:min-w-[10rem]">
        @if (!empty($logoPath))
          <img src="{{ \App\Support\VehicleImageUrl::url($logoPath) }}" alt="{{ $brandName }}" class="h-9 w-auto max-w-[160px] object-contain sm:h-10" />
        @else
          <span data-header-logo class="block max-w-[min(68vw,17rem)] truncate font-headline text-[26px] font-black italic leading-none tracking-tight text-white sm:max-w-[22rem] sm:text-[32px] lg:max-w-none lg:text-[36px]">{{ strtolower($brandName) }}</span>
        @endif
      </a>

      <nav class="pointer-events-none absolute left-1/2 top-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 items-center justify-center gap-6 xl:flex xl:px-2 w-max" aria-label="{{ __('Primary') }}">
        @if ($navOn('home'))
        <a href="{{ route('home') }}" data-header-nav-link class="pointer-events-auto inline-flex items-center border-b-2 pb-1.5 text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] transition-colors whitespace-nowrap {{ request()->routeIs('home') ? 'border-[#1280DF] text-white' : 'border-transparent text-white/85 hover:text-[#1280DF]' }}">{{ __('Home') }}</a>
        @endif

        @if ($navOn('inventory'))
        {{-- Inventory mega dropdown (Nigerian / Foreign live under here) --}}
        <div class="pointer-events-auto relative flex items-end" data-header-inventory-dropdown>
          <a href="{{ $inventoryUrl }}" data-header-inventory-trigger data-header-nav-link class="inline-flex items-center gap-0.5 border-b-2 pb-1.5 text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] transition-colors whitespace-nowrap {{ $inventoryActive ? 'border-[#1280DF] text-white' : 'border-transparent text-white/85 hover:text-[#1280DF]' }}" aria-expanded="false" aria-haspopup="true">
            <span>{{ __('Inventory') }}</span>
            <span class="material-symbols-outlined text-[18px] leading-none text-inherit" aria-hidden="true">expand_more</span>
          </a>
          <div class="absolute left-1/2 top-full z-[60] hidden w-max -translate-x-1/2 pt-2" data-header-inventory-panel role="region" aria-label="{{ __('Inventory categories') }}">
            <div class="flex w-[min(56rem,calc(100vw-2rem))] max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5">
              <div class="flex min-w-0 w-1/2 flex-col border-r border-slate-200">
                <div class="grid flex-1 gap-px bg-slate-200 p-px">
                  <a href="{{ $inventoryNigeriaUrl }}" class="group flex items-start justify-between gap-3 bg-slate-50 px-4 py-3 transition hover:bg-slate-100 {{ $nigeriaActive ? 'ring-1 ring-inset ring-[#1280DF]/40' : '' }}">
                    <div>
                      <span class="flex items-center gap-2.5 text-[13px] font-bold uppercase tracking-[0.06em] text-zinc-900 transition-colors group-hover:text-[#1280DF]">
                        <img src="https://flagcdn.com/w40/ng.png" srcset="https://flagcdn.com/w80/ng.png 2x" width="28" height="19" alt="" class="h-6 w-auto shrink-0 rounded-sm object-cover shadow-sm ring-1 ring-slate-200" decoding="async" loading="lazy" />
                        {{ __('Nigerian Used') }}
                      </span>
                      <p class="mt-1 text-xs font-medium text-zinc-600">{{ __('Locally sourced Nigerian-used listings.') }}</p>
                    </div>
                    <span class="material-symbols-outlined shrink-0 text-lg text-[#1280DF] transition group-hover:translate-x-0.5 group-hover:text-[#0a5cb3]">chevron_right</span>
                  </a>
                  <a href="{{ $inventoryForeignUrl }}" class="group flex items-start justify-between gap-3 bg-slate-50 px-4 py-3 transition hover:bg-slate-100 {{ $foreignActive ? 'ring-1 ring-inset ring-[#1280DF]/40' : '' }}">
                    <div>
                      <span class="block text-[13px] font-bold uppercase tracking-[0.06em] text-zinc-900 transition-colors group-hover:text-[#1280DF]">
                        {{ __('Foreign Used') }}
                      </span>
                      <p class="mt-1 text-xs font-medium text-zinc-600">{{ __('Foreign-used imports and international stock.') }}</p>
                    </div>
                    <span class="material-symbols-outlined shrink-0 text-lg text-[#1280DF] transition group-hover:translate-x-0.5 group-hover:text-[#0a5cb3]">chevron_right</span>
                  </a>
                </div>
                <div class="border-t border-slate-200 bg-slate-50/90 px-4 py-3">
                  <a href="{{ $inventoryUrl }}" class="inline-flex items-center gap-1 text-[12px] font-extrabold uppercase tracking-[0.08em] text-[#1280DF] transition-colors hover:text-[#0a5cb3]">{{ __('View full inventory') }}<span class="material-symbols-outlined text-base">arrow_forward</span></a>
                </div>
              </div>
              @if ($navMakesTop->isNotEmpty())
                <div class="flex min-w-0 w-1/2 flex-col bg-slate-50 p-3 sm:p-4" aria-label="{{ __('Shop by make') }}">
                  <p class="mb-3 text-[10px] font-extrabold uppercase tracking-[0.14em] text-zinc-500">{{ __('Shop by make') }}</p>
                  <div class="grid max-h-[min(22rem,55vh)] grid-cols-3 gap-x-2.5 gap-y-3.5 overflow-y-auto overscroll-contain pr-0.5">
                    @foreach ($navMakesTop as $makeOpt)
                      <a href="{{ route('inventory.index', ['make_listing_option_id' => $makeOpt->id]) }}" class="group flex flex-col items-center gap-1.5 rounded-lg p-2 text-center transition hover:bg-white hover:shadow-sm">
                        @if (! empty($makeOpt->logo_path))
                          <span class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md bg-white ring-1 ring-slate-200"><img src="{{ \App\Support\VehicleImageUrl::url($makeOpt->logo_path) }}" alt="" class="h-full w-full object-contain p-0.5" /></span>
                        @elseif (! empty(trim((string) ($makeOpt->flag_emoji ?? ''))))
                          <span class="flex h-12 w-12 items-center justify-center text-2xl leading-none" style="font-family: 'Segoe UI Emoji','Apple Color Emoji','Noto Color Emoji',sans-serif" aria-hidden="true">{{ trim((string) $makeOpt->flag_emoji) }}</span>
                        @else
                          <span class="flex h-12 w-12 items-center justify-center rounded-md bg-slate-200 text-[12px] font-black text-zinc-700">{{ strtoupper(\Illuminate\Support\Str::substr($makeOpt->value, 0, 2)) }}</span>
                        @endif
                        <span class="line-clamp-2 w-full text-[10px] font-bold uppercase leading-snug text-zinc-800 transition group-hover:text-[#1280DF] sm:text-[11px]">{{ $makeOpt->value }}</span>
                      </a>
                    @endforeach
                  </div>
                  <div class="mt-3 border-t border-slate-200 pt-3">
                    <a href="{{ route('makes.index') }}" class="inline-flex items-center gap-1 text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#1280DF] transition-colors hover:text-[#0a5cb3]">{{ __('All makes') }}<span class="material-symbols-outlined text-base">arrow_forward</span></a>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
        @endif

        @if ($navOn('about'))
        <a href="{{ route('about') }}" data-header-nav-link class="pointer-events-auto inline-flex items-center border-b-2 pb-1.5 text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] transition-colors whitespace-nowrap {{ request()->routeIs('about') ? 'border-[#1280DF] text-white' : 'border-transparent text-white/85 hover:text-[#1280DF]' }}">{{ __('About') }}</a>
        @endif

        @if ($navOn('faq'))
        @if (! empty($faqNavItems))
          <div class="pointer-events-auto relative flex items-end" data-header-faq-dropdown>
            <a href="{{ $faqUrl }}" data-header-faq-trigger data-header-nav-link class="inline-flex items-center gap-0.5 border-b-2 pb-1.5 text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] transition-colors whitespace-nowrap {{ request()->routeIs('faq') ? 'border-[#1280DF] text-white' : 'border-transparent text-white/85 hover:text-[#1280DF]' }}" aria-expanded="false" aria-haspopup="true">
              <span>{{ __('FAQ') }}</span>
              <span class="material-symbols-outlined text-[18px] leading-none text-inherit" aria-hidden="true">expand_more</span>
            </a>
            <div class="absolute left-1/2 top-full z-[60] hidden w-max -translate-x-1/2 pt-2" data-header-faq-panel role="region" aria-label="{{ __('Knowledge base') }}">
              <div class="w-[min(42rem,calc(100vw-2rem))] max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl ring-1 ring-black/5">
                <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                  <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-zinc-500">{{ __('Knowledge base') }}</p>
                </div>
                <div class="grid grid-cols-1 gap-px bg-slate-200 p-px sm:grid-cols-2">
                  @foreach ($faqNavItems as $faqNav)
                    <a href="{{ $faqUrl }}#{{ $faqNav['hash'] }}" class="group flex items-center gap-3 bg-white px-4 py-3.5 transition hover:bg-slate-50">
                      <span class="material-symbols-outlined shrink-0 text-[26px] text-[#ffb129]" aria-hidden="true">{{ $faqNav['icon'] }}</span>
                      <span class="min-w-0 flex-1 text-[12px] font-bold uppercase leading-snug tracking-[0.06em] text-zinc-900 transition-colors group-hover:text-[#1280DF] sm:text-[13px]">{{ $faqNav['title'] }}</span>
                      <span class="material-symbols-outlined shrink-0 text-lg text-[#1280DF] transition group-hover:translate-x-0.5 group-hover:text-[#0a5cb3]">chevron_right</span>
                    </a>
                  @endforeach
                </div>
                <div class="border-t border-slate-200 bg-slate-50/90 px-4 py-3">
                  <a href="{{ $faqUrl }}" class="inline-flex items-center gap-1 text-[12px] font-extrabold uppercase tracking-[0.08em] text-[#1280DF] transition-colors hover:text-[#0a5cb3]">{{ __('Open FAQ page') }}<span class="material-symbols-outlined text-base">arrow_forward</span></a>
                </div>
              </div>
            </div>
          </div>
        @else
          <a href="{{ $faqUrl }}" data-header-nav-link class="pointer-events-auto inline-flex items-center border-b-2 pb-1.5 text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] transition-colors whitespace-nowrap {{ request()->routeIs('faq') ? 'border-[#1280DF] text-white' : 'border-transparent text-white/85 hover:text-[#1280DF]' }}">{{ __('FAQ') }}</a>
        @endif
        @endif

        @if ($navOn('contact'))
        <a href="{{ route('contact') }}" data-header-nav-link class="pointer-events-auto inline-flex items-center border-b-2 pb-1.5 text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] transition-colors whitespace-nowrap {{ request()->routeIs('contact') ? 'border-[#1280DF] text-white' : 'border-transparent text-white/85 hover:text-[#1280DF]' }}">{{ __('Contact') }}</a>
        @endif
      </nav>

      <div class="relative z-20 flex shrink-0 items-center justify-end gap-2 sm:gap-4">
        {{-- Compare + My account: desktop xl+ only (mobile finds them inside the sidebar after nav links). --}}
        @if ($navOn('compare'))
        <a href="{{ route('compare') }}" class="group hidden items-center gap-2 xl:inline-flex" title="{{ __('Compare vehicles') }}">
          <span data-header-action-text class="hidden text-[11px] font-extrabold uppercase tracking-[0.06em] text-white transition-colors group-hover:text-[#1280DF] xl:inline">{{ __('Compare') }}</span>
          <span class="relative ml-0 inline-flex">
            <span data-header-icon class="material-symbols-outlined text-[24px] text-white transition-colors group-hover:text-[#1280DF] xl:text-[24px]">speed</span>
            <span class="absolute -right-1 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-[#1280DF] px-1 text-[10px] font-extrabold text-white">{{ $compareCount }}</span>
          </span>
        </a>

        <span class="mx-0 hidden h-7 w-px shrink-0 bg-white/15 xl:block" aria-hidden="true"></span>

        @php
          $myAccountUrl = auth()->check() ? route('dashboard') : route('login');
        @endphp
        <a href="{{ $myAccountUrl }}" data-header-account-link class="hidden h-9 items-center justify-center rounded border border-white/15 bg-white/[0.06] px-3.5 text-center text-[13px] font-extrabold uppercase leading-none tracking-[0.07em] text-white/90 shadow-none backdrop-blur-sm transition hover:bg-white/10 xl:inline-flex" title="{{ __('My account') }}">
          {{ __('My account') }}
        </a>

        <button data-header-menu-button class="inline-flex h-11 w-11 items-center justify-center rounded border border-white/15 text-white hover:bg-white/10 xl:hidden" type="button" data-mobile-menu-toggle aria-label="{{ __('Menu') }}">
          <span data-header-menu-icon class="material-symbols-outlined text-2xl">menu</span>
        </button>
      </div>
    </div>
  </div>
</header>

<div class="fixed inset-0 z-[55] hidden bg-black/55 xl:hidden" data-mobile-menu-overlay aria-hidden="true"></div>
<div class="fixed right-0 top-0 z-[60] flex h-full min-h-0 w-[min(22rem,calc(100vw-2rem))] translate-x-full flex-col bg-white shadow-2xl ring-1 ring-black/5 transition-transform duration-200 ease-out xl:hidden" data-mobile-menu-panel id="site-mobile-nav">
  <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4">
    <span class="font-headline text-lg font-black italic text-zinc-900">{{ strtolower($brandName) }}</span>
    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-md text-zinc-600 hover:bg-slate-100 hover:text-zinc-900" data-mobile-menu-close aria-label="{{ __('Close') }}">
      <span class="material-symbols-outlined text-2xl">close</span>
    </button>
  </div>
  <nav class="flex min-h-0 flex-1 flex-col gap-2 overflow-y-auto overflow-x-hidden px-4 py-4 text-zinc-800" aria-label="{{ __('Mobile') }}">
    <div class="shrink-0 border-b border-slate-200 pb-4">
      <a href="{{ route('compare') }}" class="flex items-center justify-between rounded-md border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-black uppercase tracking-[0.06em] text-zinc-900 transition hover:border-slate-300 hover:bg-slate-100">
        <span class="inline-flex items-center gap-2">
          <span class="material-symbols-outlined text-[22px] text-[#1280DF]">speed</span>
          {{ __('Compare') }}
        </span>
        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-[#1280DF] px-2 text-xs font-extrabold text-white">{{ $compareCount }}</span>
      </a>
    </div>
    @endif

    @if ($navOn('home'))
    <a href="{{ route('home') }}" class="shrink-0 rounded-sm px-3 py-3.5 text-sm font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-100 hover:text-[#1280DF]">{{ __('Home') }}</a>
    @endif
    @if ($navOn('inventory'))
    <div class="shrink-0 rounded-sm border border-slate-200 bg-slate-50/80">
      <button type="button" class="flex w-full items-center justify-between px-3 py-3.5 text-left text-sm font-bold uppercase tracking-[0.06em] text-zinc-900 transition hover:bg-slate-100" data-mobile-inventory-toggle aria-expanded="false" aria-controls="mobile-inventory-subnav">
        <span>{{ __('Inventory') }}</span>
        <span class="material-symbols-outlined text-[22px] text-zinc-500 transition-transform duration-200" data-mobile-inventory-chevron aria-hidden="true">expand_more</span>
      </button>
      <div id="mobile-inventory-subnav" class="hidden max-h-[min(78dvh,36rem)] overflow-y-auto overscroll-y-contain border-t border-slate-200 bg-white" data-mobile-inventory-panel>
        <a href="{{ $inventoryNigeriaUrl }}" class="block border-b border-slate-100 px-4 py-3 text-xs font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-50 hover:text-[#1280DF]">
          {{ __('Nigerian Used') }}
        </a>
        <a href="{{ $inventoryForeignUrl }}" class="block border-b border-slate-100 px-4 py-3 text-xs font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-50 hover:text-[#1280DF]">
          {{ __('Foreign Used') }}
        </a>
        <a href="{{ $inventoryUrl }}" class="block border-b border-slate-100 bg-slate-50/80 px-4 py-3 text-xs font-extrabold uppercase tracking-[0.08em] text-[#1280DF] transition hover:bg-slate-100 hover:text-[#0a5cb3]">{{ __('View full inventory') }}</a>
        @if ($navMakesTop->isNotEmpty())
          <div class="border-t border-slate-200 bg-slate-50">
            <p class="bg-slate-50 px-4 pb-1 pt-3 text-[10px] font-extrabold uppercase tracking-[0.14em] text-zinc-500">{{ __('Shop by make') }}</p>
            <div class="flex flex-col pb-2">
              @foreach ($navMakesTop as $makeOpt)
                <a href="{{ route('inventory.index', ['make_listing_option_id' => $makeOpt->id]) }}" class="group flex items-center gap-3 border-t border-slate-100 px-4 py-2.5 transition hover:bg-white">
                  @if (! empty($makeOpt->logo_path))
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-md bg-white ring-1 ring-slate-200"><img src="{{ \App\Support\VehicleImageUrl::url($makeOpt->logo_path) }}" alt="" class="h-full w-full object-contain p-0.5" /></span>
                  @elseif (! empty(trim((string) ($makeOpt->flag_emoji ?? ''))))
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center text-lg leading-none" style="font-family: 'Segoe UI Emoji','Apple Color Emoji','Noto Color Emoji',sans-serif" aria-hidden="true">{{ trim((string) $makeOpt->flag_emoji) }}</span>
                  @else
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-slate-200 text-[10px] font-black text-zinc-700">{{ strtoupper(\Illuminate\Support\Str::substr($makeOpt->value, 0, 2)) }}</span>
                  @endif
                  <span class="text-left text-[11px] font-bold uppercase tracking-[0.05em] text-zinc-800 transition group-hover:text-[#1280DF]">{{ $makeOpt->value }}</span>
                </a>
              @endforeach
              <a href="{{ route('makes.index') }}" class="border-t border-slate-200 bg-white px-4 py-3 text-[11px] font-extrabold uppercase tracking-[0.08em] text-[#1280DF] transition hover:bg-slate-50">{{ __('All makes') }}</a>
            </div>
          </div>
        @endif
      </div>
    </div>
    @endif
    @if ($navOn('about'))
    <a href="{{ route('about') }}" class="shrink-0 rounded-sm px-3 py-3.5 text-sm font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-100 hover:text-[#1280DF]">{{ __('About') }}</a>
    @endif
    @if ($navOn('faq'))
    @if (! empty($faqNavItems))
      <div class="shrink-0 rounded-sm border border-slate-200 bg-slate-50/80">
        <button type="button" class="flex w-full items-center justify-between px-3 py-3.5 text-left text-sm font-bold uppercase tracking-[0.06em] text-zinc-900 transition hover:bg-slate-100" data-mobile-faq-toggle aria-expanded="false" aria-controls="mobile-faq-subnav">
          <span>{{ __('FAQ') }}</span>
          <span class="material-symbols-outlined text-[22px] text-zinc-500 transition-transform duration-200" data-mobile-faq-chevron aria-hidden="true">expand_more</span>
        </button>
        <div id="mobile-faq-subnav" class="hidden max-h-[min(60vh,28rem)] overflow-y-auto overscroll-contain border-t border-slate-200 bg-white" data-mobile-faq-panel>
          @foreach ($faqNavItems as $faqNav)
            <a href="{{ $faqUrl }}#{{ $faqNav['hash'] }}" class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 text-xs font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-50 hover:text-[#1280DF]">
              <span class="material-symbols-outlined shrink-0 text-[22px] text-[#ffb129]" aria-hidden="true">{{ $faqNav['icon'] }}</span>
              <span class="text-left">{{ $faqNav['title'] }}</span>
            </a>
          @endforeach
          <a href="{{ $faqUrl }}" class="block bg-slate-50/80 px-4 py-3 text-xs font-extrabold uppercase tracking-[0.08em] text-[#1280DF] transition hover:bg-slate-100 hover:text-[#0a5cb3]">{{ __('Full help center') }}</a>
        </div>
      </div>
    @else
      <a href="{{ $faqUrl }}" class="shrink-0 rounded-sm px-3 py-3.5 text-sm font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-100 hover:text-[#1280DF]">{{ __('FAQ') }}</a>
    @endif
    @endif
    @if ($navOn('contact'))
    <a href="{{ route('contact') }}" class="shrink-0 rounded-sm px-3 py-3.5 text-sm font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-100 hover:text-[#1280DF]">{{ __('Contact') }}</a>
    @endif

    <div class="mt-auto shrink-0 border-t border-slate-200 pt-4">
      <div class="flex flex-col gap-2">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex h-11 items-center justify-center rounded border border-slate-200 bg-zinc-900 px-3 text-sm font-extrabold uppercase tracking-[0.07em] text-white transition hover:bg-zinc-800">{{ __('My account') }}</a>
        @auth
          <div class="border-t border-slate-200 pt-2">
            <form method="post" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="flex w-full items-center justify-between rounded-sm border border-slate-200 px-3 py-3 text-sm font-bold uppercase tracking-[0.06em] text-zinc-800 transition hover:bg-slate-100">
                <span>{{ __('Logout') }}</span><span class="material-symbols-outlined text-xl text-zinc-600">logout</span>
              </button>
            </form>
          </div>
        @endauth
      </div>
    </div>

    @if ($address !== '' || $phone !== '')
      <div class="shrink-0 border-t border-slate-200 pt-4 text-xs text-zinc-600">
        @if ($address !== '')
          <p class="line-clamp-2">{{ $address }}</p>
        @endif
        @if ($phone !== '')
          <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="mt-2 inline-flex text-sm font-semibold text-[#1280DF] hover:text-[#0a5cb3]">{{ $phone }}</a>
        @endif
      </div>
    @endif
  </nav>
</div>



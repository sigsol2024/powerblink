<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
      $site = $site ?? [];
      $siteDisplayName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : config('app.name');
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
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
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
    <link rel="stylesheet" href="{{ asset('asset/css/site.css') }}" />
    @if (!empty($site['favicon_path'] ?? ''))
      <link rel="icon" href="{{ \App\Support\VehicleImageUrl::url($site['favicon_path']) }}" />
    @endif
    @stack('head')
  </head>

  <body class="bg-page_bg font-body text-on_surface selection:bg-brand_blue/20 {{ $bodyClass ?? '' }}" data-currency-label-prefix="{{ e(__('Currency')) }}" data-currency-ui="{{ e(json_encode($currencyUi ?? ['default' => 'USD', 'selected' => 'USD', 'symbols' => ['USD' => '$'], 'rates' => ['USD' => 1.0]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}">
    <!-- Global Loading Screen: logo centered; only outer ring animates (no box behind logo) -->
    <div id="global-loader" class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#1E2229] transition-opacity duration-700 ease-in-out">
      <div class="relative flex h-36 w-36 items-center justify-center">
        <div
          class="pointer-events-none absolute inset-0 rounded-full border-2 border-transparent border-t-white border-r-white/25 border-b-white/10 border-l-white/40 animate-spin"
          style="animation-duration: 0.95s; animation-timing-function: linear;"
          aria-hidden="true"
        ></div>
        <div class="relative z-10 flex h-24 w-24 items-center justify-center">
          @if (! empty($loaderLogoPath))
            <img src="{{ \App\Support\VehicleImageUrl::url($loaderLogoPath) }}" alt="" class="h-[4.5rem] w-[4.5rem] max-h-[90%] max-w-[90%] object-contain bg-transparent" />
          @else
            <span class="max-w-[90%] truncate px-1 text-center font-headline text-sm font-black uppercase tracking-tight text-white">{{ $siteDisplayName }}</span>
          @endif
        </div>
      </div>
      <!-- Fading Site Name -->
      <div class="mt-8 animate-pulse text-center font-headline text-2xl font-black italic tracking-widest text-white uppercase">
        {{ strtoupper($siteDisplayName) }}
      </div>
    </div>
    <script>
      window.addEventListener('load', function() {
        const loader = document.getElementById('global-loader');
        if (loader) {
          loader.classList.add('opacity-0');
          setTimeout(() => {
              loader.style.display = 'none';
          }, 700);
        }
      });
    </script>

    @include('partials.header')
    {{-- Header is sticky (dealer-style top strip + nav); no artificial pt-* needed — avoids hero/content hiding under a fixed bar --}}
    <main id="main">
      @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.whatsapp-widget')
    <div id="currencySelectionModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/65 p-4">
      <div class="w-full max-w-xl rounded-xl bg-white p-6 text-zinc-900 shadow-2xl">
        <h2 class="text-xl font-black">{{ __('Use your local currency?') }}</h2>
        <p class="mt-2 text-sm text-zinc-600">{{ __('We detected your likely currency. You can always change this later from the header switcher.') }}</p>
        <div class="mt-5 space-y-3 rounded-lg bg-zinc-50 p-4">
          <div class="flex items-center justify-between text-sm">
            <span class="font-semibold">{{ __('Detected') }}</span>
            <span id="detectedCurrencyDisplay" class="font-black">—</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="font-semibold">{{ __('Site default') }}</span>
            <span id="defaultCurrencyDisplay" class="font-black">—</span>
          </div>
        </div>
        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
          <button type="button" id="currencyKeepDefaultBtn" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-50">{{ __('Keep default') }}</button>
          <button type="button" id="currencyUseDetectedBtn" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">{{ __('Use detected currency') }}</button>
        </div>
      </div>
    </div>
    <script src="{{ asset('asset/js/main.js') }}" defer></script>
    <script>
      const bodyEl = document.body;
      let parsedCurrencyUi = {default: 'USD', selected: 'USD', symbols: {USD: '$'}, rates: {USD: 1}};
      try {
        const rawCurrencyUi = bodyEl?.getAttribute('data-currency-ui') || '{}';
        parsedCurrencyUi = JSON.parse(rawCurrencyUi);
      } catch (_) {
        parsedCurrencyUi = {default: 'USD', selected: 'USD', symbols: {USD: '$'}, rates: {USD: 1}};
      }
      window.siteCurrency = parsedCurrencyUi;

      (() => {
        const cfg = window.siteCurrency || {};
        const modal = document.getElementById('currencySelectionModal');
        if (!modal) return;

        const supported = cfg.supported || {};
        const defaultCurrency = String(cfg.default || 'USD').toUpperCase();
        const selectedCurrency = String(cfg.selected || defaultCurrency).toUpperCase();
        const promptDismissed = Boolean(cfg.promptDismissed);
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const shouldSkip = promptDismissed || localStorage.getItem('currency_modal_dismissed') === '1';
        if (shouldSkip) return;

        const locale = Intl.DateTimeFormat().resolvedOptions().locale || '';
        const localeCountry = locale.includes('-') ? locale.split('-').pop().toUpperCase() : '';
        const countryToCurrency = {
          US: 'USD', GB: 'GBP', NG: 'NGN', CA: 'CAD', AE: 'AED',
          IE: 'EUR', DE: 'EUR', FR: 'EUR', ES: 'EUR', IT: 'EUR', NL: 'EUR', BE: 'EUR'
        };
        const detectedCurrency = countryToCurrency[localeCountry] || defaultCurrency;
        if (!supported[detectedCurrency] || detectedCurrency === selectedCurrency) return;

        const detectedEl = document.getElementById('detectedCurrencyDisplay');
        const defaultEl = document.getElementById('defaultCurrencyDisplay');
        const useBtn = document.getElementById('currencyUseDetectedBtn');
        const keepBtn = document.getElementById('currencyKeepDefaultBtn');
        if (!detectedEl || !defaultEl || !useBtn || !keepBtn) return;

        detectedEl.textContent = detectedCurrency + ' - ' + (supported[detectedCurrency] || detectedCurrency);
        defaultEl.textContent = defaultCurrency + ' - ' + (supported[defaultCurrency] || defaultCurrency);
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const persist = async (currency) => {
          const endpointEl = document.getElementById('currency-select-endpoint');
          const endpoint = endpointEl?.value || '';
          if (!endpoint) return;
          useBtn.setAttribute('disabled', 'disabled');
          keepBtn.setAttribute('disabled', 'disabled');
          try {
            await fetch(endpoint, {
              method: 'POST',
              credentials: 'same-origin',
              headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
              body: JSON.stringify({ currency, markAsShown: true }),
            });
            localStorage.setItem('currency_modal_dismissed', '1');
            window.location.reload();
          } finally {
            useBtn.removeAttribute('disabled');
            keepBtn.removeAttribute('disabled');
          }
        };

        useBtn.addEventListener('click', () => persist(detectedCurrency));
        keepBtn.addEventListener('click', () => persist(defaultCurrency));
      })();
    </script>
    @stack('scripts')
  </body>
</html>
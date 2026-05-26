@php
  $site = $site ?? [];
  $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : 'VOGUE DRESS';
  $cartCount = \App\Support\Cart::count();
  $shopActive = request()->routeIs('shop.index', 'inventory.index', 'product.show', 'inventory.show');
  $cartActive = request()->routeIs('cart.*');
@endphp
@push('head')
<style>
  .luxe-cart-pulse { animation: luxe-cart-pulse 600ms ease-out; }
  @keyframes luxe-cart-pulse {
    0%   { transform: scale(1); }
    35%  { transform: scale(1.18); }
    100% { transform: scale(1); }
  }
</style>
@endpush
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
    <a href="{{ route('shop.index') }}" class="text-primary hover:scale-110 transition-transform hidden sm:inline-flex items-center justify-center" aria-label="{{ __('Search') }}">
      <x-icon name="search" class="w-5 h-5" />
    </a>
    <button type="button" class="text-primary hover:scale-110 transition-transform relative inline-flex items-center justify-center {{ $cartActive ? 'font-bold' : '' }}" aria-label="{{ __('Bag') }}" data-cart-toggle>
      <x-icon name="shopping-bag" class="w-6 h-6" />
      <span class="luxe-cart-badge absolute -top-1 -right-1 min-w-[1rem] h-4 px-1 bg-primary text-on-primary text-[9px] font-bold flex items-center justify-center {{ $cartCount > 0 ? '' : 'hidden' }}" data-cart-count-badge>{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
    </button>
    @auth
      <a href="{{ route('dashboard') }}" class="text-primary hover:scale-110 transition-transform hidden md:inline-flex items-center justify-center" aria-label="{{ __('Account') }}">
        <x-icon name="user" class="w-5 h-5" />
      </a>
    @else
      <a href="{{ route('login') }}" class="text-primary hover:scale-110 transition-transform hidden md:inline-flex items-center justify-center" aria-label="{{ __('Sign in') }}">
        <x-icon name="user" class="w-5 h-5" />
      </a>
    @endauth
    <button type="button" class="text-primary md:hidden inline-flex items-center justify-center" data-luxe-mobile-nav-toggle aria-expanded="false" aria-controls="luxe-mobile-nav" aria-label="{{ __('Menu') }}">
      <x-icon name="menu" class="w-6 h-6" />
    </button>
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

{{-- Cart slide-over drawer (right side, mobile-first) --}}
<div class="luxe-store fixed inset-0 z-[60] hidden" data-cart-drawer aria-hidden="true">
  <div class="absolute inset-0 bg-black/40" data-cart-overlay></div>
  <aside class="absolute top-0 right-0 h-full w-full sm:max-w-md bg-background shadow-2xl flex flex-col" role="dialog" aria-label="{{ __('Shopping bag') }}">
    <header class="flex items-center justify-between px-5 py-5 border-b border-outline-variant">
      <h2 class="font-headline-md text-headline-md uppercase tracking-widest text-primary">{{ __('Shopping bag') }}</h2>
      <button type="button" class="text-primary inline-flex items-center justify-center" aria-label="{{ __('Close') }}" data-cart-close>
        <x-icon name="close" class="w-6 h-6" />
      </button>
    </header>

    <div class="flex-1 overflow-y-auto px-5 py-4" data-cart-lines>
      {{-- Lines injected by JS --}}
      <p class="py-12 text-center text-on-surface-variant font-body-md text-body-md" data-cart-empty>{{ __('Your bag is empty.') }}</p>
    </div>

    <footer class="border-t border-outline-variant px-5 py-5 space-y-4 bg-background">
      <div class="flex justify-between items-center">
        <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ __('Subtotal') }}</span>
        <span class="font-body-lg text-body-lg font-semibold text-primary" data-cart-subtotal>{{ format_currency(\App\Support\Cart::subtotal()) }}</span>
      </div>
      <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-primary text-on-primary py-4 font-button-text text-button-text uppercase tracking-[0.25em] hover:opacity-90 transition-opacity">
        {{ __('Checkout') }}
      </a>
      <a href="{{ route('cart.index') }}" class="block w-full text-center border border-primary text-primary py-4 font-button-text text-button-text uppercase tracking-[0.25em] hover:bg-primary hover:text-on-primary transition-colors">
        {{ __('View bag') }}
      </a>
    </footer>
  </aside>
</div>

@push('scripts')
<script id="luxe-cart-config" type="application/json">
{!! json_encode([
  'urls' => [
    'state'  => route('cart.state'),
    'add'    => route('cart.add'),
    'remove' => route('cart.remove'),
  ],
  'i18n' => [
    'empty'      => __('Your bag is empty.'),
    'qty'        => __('Qty'),
    'remove'     => __('Remove'),
    'added'      => __('Added').' ✓',
    'errorAdd'   => __('Could not add to bag.'),
    'errorOther' => __('Could not update bag.'),
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
  (function () {
    var cfg = {};
    try { cfg = JSON.parse(document.getElementById('luxe-cart-config').textContent); } catch (e) { return; }
    var URLS = cfg.urls;
    var I18N = cfg.i18n;
    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function (c) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
      });
    }

    function updateBadge(count) {
      qsa('[data-cart-count-badge]').forEach(function (el) {
        el.textContent = count > 9 ? '9+' : String(count);
        el.classList.toggle('hidden', !(count > 0));
      });
    }

    function openDrawer() {
      var d = qs('[data-cart-drawer]');
      if (!d) return;
      d.classList.remove('hidden');
      d.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
      var d = qs('[data-cart-drawer]');
      if (!d) return;
      d.classList.add('hidden');
      d.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    function renderLines(state) {
      var container = qs('[data-cart-lines]');
      if (!container) return;
      container.innerHTML = '';

      if (!state.lines || state.lines.length === 0) {
        var empty = document.createElement('p');
        empty.className = 'py-12 text-center text-on-surface-variant font-body-md text-body-md';
        empty.textContent = I18N.empty;
        container.appendChild(empty);
        return;
      }

      state.lines.forEach(function (line) {
        var imgHtml = line.image ? '<img src="' + escapeHtml(line.image) + '" alt="" class="w-full h-full object-cover">' : '';
        var variantHtml = line.variant_label ? '<p class="text-[10px] uppercase tracking-widest text-on-surface-variant mt-1">' + escapeHtml(line.variant_label) + '</p>' : '';
        var variantAttr = line.vehicle_variant_id ? ' data-variant-id="' + line.vehicle_variant_id + '"' : '';

        var row = document.createElement('div');
        row.className = 'flex gap-4 py-4 border-b border-outline-variant';
        row.innerHTML =
          '<div class="w-20 h-24 bg-surface-container shrink-0 overflow-hidden">' + imgHtml + '</div>' +
          '<div class="flex-1 min-w-0">' +
            '<div class="flex justify-between gap-2">' +
              '<h4 class="font-body-md text-body-md text-primary uppercase truncate">' + escapeHtml(line.name) + '</h4>' +
              '<p class="font-body-md font-semibold whitespace-nowrap">' + escapeHtml(line.line_total_formatted) + '</p>' +
            '</div>' +
            variantHtml +
            '<div class="mt-3 flex justify-between items-center gap-3">' +
              '<span class="text-xs text-on-surface-variant">' + escapeHtml(I18N.qty) + ': ' + line.qty + '</span>' +
              '<button type="button" class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant hover:text-error" data-cart-remove data-vehicle-id="' + line.vehicle_id + '"' + variantAttr + '>' + escapeHtml(I18N.remove) + '</button>' +
            '</div>' +
          '</div>';
        container.appendChild(row);
      });
    }

    function applyState(state) {
      updateBadge(state.count || 0);
      renderLines(state);
      var sub = qs('[data-cart-subtotal]');
      if (sub && state.subtotal_formatted) sub.textContent = state.subtotal_formatted;
    }

    function fetchState() {
      return fetch(URLS.state, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      }).then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) { if (data) applyState(data); })
        .catch(function () { /* ignore */ });
    }

    function postForm(url, formData) {
      return fetch(url, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
        body: formData,
        credentials: 'same-origin'
      }).then(function (res) {
        if (!res.ok) {
          return res.json().catch(function () { return {}; }).then(function (j) {
            throw new Error((j && j.message) || I18N.errorOther);
          });
        }
        return res.json();
      });
    }

    document.addEventListener('click', function (e) {
      var toggle = e.target.closest('[data-cart-toggle]');
      if (toggle) {
        e.preventDefault();
        fetchState();
        openDrawer();
        return;
      }
      if (e.target.closest('[data-cart-close]') || e.target.matches('[data-cart-overlay]')) {
        closeDrawer();
        return;
      }
      var removeBtn = e.target.closest('[data-cart-remove]');
      if (removeBtn) {
        e.preventDefault();
        var fd = new FormData();
        fd.append('vehicle_id', removeBtn.dataset.vehicleId);
        if (removeBtn.dataset.variantId) fd.append('vehicle_variant_id', removeBtn.dataset.variantId);
        postForm(URLS.remove, fd).then(applyState).catch(function () {});
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeDrawer();
    });

    function flashAdded() {
      // Quick pulse on the bag icon so the user sees something happened without taking over the page.
      var btn = qs('[data-cart-toggle]');
      if (!btn) return;
      btn.classList.add('luxe-cart-pulse');
      setTimeout(function () { btn.classList.remove('luxe-cart-pulse'); }, 700);
    }

    document.addEventListener('submit', function (e) {
      var form = e.target.closest('[data-cart-add-form]');
      if (!form) return;
      e.preventDefault();
      var submit = form.querySelector('[type="submit"]');
      // Cache the real label ONCE per button — rapid re-submits during the 'Added ✓' confirmation
      // would otherwise capture the confirmation text and leave the button permanently stuck.
      if (submit && typeof submit.dataset.originalLabel === 'undefined') {
        submit.dataset.originalLabel = submit.innerHTML;
      }
      var originalLabel = submit ? submit.dataset.originalLabel : '';
      if (submit) {
        if (submit._addedTimer) { clearTimeout(submit._addedTimer); submit._addedTimer = null; }
        submit.disabled = true;
        submit.classList.add('opacity-60');
      }
      var fd = new FormData(form);
      postForm(form.action, fd)
        .then(function (state) {
          applyState(state);
          flashAdded();
          // Briefly confirm the action inline on the button itself rather than opening the drawer.
          if (submit) {
            submit.innerHTML = I18N.added || originalLabel;
            submit._addedTimer = setTimeout(function () {
              submit.innerHTML = originalLabel;
              submit._addedTimer = null;
            }, 1400);
          }
        })
        .catch(function (err) {
          if (submit) submit.innerHTML = originalLabel;
          alert((err && err.message) || I18N.errorAdd);
        })
        .then(function () {
          if (submit) { submit.disabled = false; submit.classList.remove('opacity-60'); }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
      var btn = document.querySelector('[data-luxe-mobile-nav-toggle]');
      var nav = document.querySelector('[data-luxe-mobile-nav]');
      if (btn && nav) {
        btn.addEventListener('click', function () {
          nav.classList.toggle('hidden');
          btn.setAttribute('aria-expanded', nav.classList.contains('hidden') ? 'false' : 'true');
        });
      }
    });
  })();
</script>
@endpush

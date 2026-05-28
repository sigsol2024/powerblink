@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $logoPath = trim((string) ($site['logo_path'] ?? ''));
  if ($logoPath === '') {
    $logoPath = trim((string) ($site['logo_light_path'] ?? ''));
  }
  if ($logoPath === '') {
    $logoPath = trim((string) ($site['logo_url'] ?? ''));
  }
  $cartCount = \App\Support\Cart::count();
  $shopActive = request()->routeIs('shop.index', 'inventory.index', 'product.show', 'inventory.show');
  $cartActive = request()->routeIs('cart.*');
  $navCategories = collect(\App\Support\VehicleListingCatalog::filterOptions()['categories'] ?? [])->take(4);
  $activeCategoryId = (int) request('product_category_listing_option_id', 0);
  $featuredShopUrl = route('shop.index', ['featured' => 1]);
  $featuredActive = $shopActive && request()->boolean('featured');
@endphp
@push('head')
<style>
  .luxe-cart-pulse { animation: luxe-cart-pulse 600ms ease-out; }
  @keyframes luxe-cart-pulse {
    0%   { transform: scale(1); }
    35%  { transform: scale(1.18); }
    100% { transform: scale(1); }
  }
  .luxe-mobile-nav-panel {
    transform: translateX(-100%);
    transition: transform 280ms cubic-bezier(0.4, 0, 0.2, 1);
  }
  .luxe-mobile-nav-panel.is-open {
    transform: translateX(0);
  }
  .luxe-mobile-nav-backdrop {
    opacity: 0;
    transition: opacity 280ms ease;
  }
  .luxe-mobile-nav-backdrop.is-open {
    opacity: 1;
  }
</style>
@endpush
<header class="fixed top-0 w-full z-50 flex justify-between items-center px-margin-mobile md:px-gutter py-4 bg-background/95 backdrop-blur-sm border-b border-outline-variant luxe-store">
  <div class="flex items-center gap-4 md:gap-8 min-w-0">
    <a href="{{ route('home') }}" class="flex min-w-0 shrink items-center">
      @if ($logoPath !== '')
        <img src="{{ \App\Support\VehicleImageUrl::url($logoPath) }}" alt="{{ $brandName }}" class="h-11 w-auto max-w-[220px] object-contain sm:h-12 md:max-w-[260px]" />
      @else
        <span class="font-display-lg text-[22px] sm:text-display-lg-mobile md:text-display-lg text-primary uppercase tracking-tighter truncate">
          {{ strtoupper($brandName) }}
        </span>
      @endif
    </a>
    <nav class="hidden md:flex items-center gap-2 lg:gap-4 flex-wrap min-w-0">
      <a
        href="{{ $featuredShopUrl }}"
        class="text-[10px] lg:text-xs xl:text-sm tracking-wide lg:tracking-widest py-1 whitespace-nowrap {{ $featuredActive ? 'text-primary font-bold border-b border-primary' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}"
      >{{ __('FEATURED ITEMS') }}</a>
      @forelse ($navCategories as $cat)
        @php
          $catActive = $shopActive && ! $featuredActive && $activeCategoryId === (int) $cat->id;
          $catUrl = route('shop.index', ['product_category_listing_option_id' => $cat->id]);
        @endphp
        <a href="{{ $catUrl }}" class="text-[10px] lg:text-xs xl:text-sm tracking-wide lg:tracking-widest py-1 whitespace-nowrap {{ $catActive ? 'text-primary font-bold border-b border-primary' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}">{{ strtoupper($cat->value) }}</a>
      @empty
        <a href="{{ route('shop.index') }}" class="text-[10px] lg:text-xs xl:text-sm tracking-wide lg:tracking-widest py-1 {{ $shopActive && ! $featuredActive && $activeCategoryId === 0 ? 'text-primary font-bold border-b border-primary' : 'text-on-surface-variant hover:text-primary transition-colors duration-300' }}">{{ __('SHOP') }}</a>
      @endforelse
    </nav>
  </div>
  <div class="flex items-center gap-4 md:gap-6 shrink-0">
    <a href="{{ route('shop.index') }}" class="hidden md:inline-flex items-center justify-center bg-black text-white px-5 py-2 font-button-text text-button-text uppercase tracking-[0.2em] hover:opacity-90 transition-opacity" aria-label="{{ __('Shop now') }}">
      {{ __('Shop now') }}
    </a>
    <button type="button" class="text-primary hover:scale-110 transition-transform relative inline-flex items-center justify-center {{ $cartActive ? 'font-bold' : '' }}" aria-label="{{ __('Bag') }}" data-cart-toggle>
      <x-icon name="shopping-bag" class="w-6 h-6" />
      <span class="luxe-cart-badge absolute -top-1 -right-1 min-w-[1rem] h-4 px-1 bg-primary text-on-primary text-[9px] font-bold flex items-center justify-center {{ $cartCount > 0 ? '' : 'hidden' }}" data-cart-count-badge>{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
    </button>
    <button type="button" class="text-primary md:hidden inline-flex items-center justify-center w-10 h-10 -mr-1" data-luxe-mobile-nav-toggle aria-expanded="false" aria-controls="luxe-mobile-nav-drawer" aria-label="{{ __('Menu') }}">
      <span class="inline-flex" data-luxe-mobile-nav-icon-menu><x-icon name="menu" class="w-7 h-7" /></span>
      <span class="hidden inline-flex" data-luxe-mobile-nav-icon-close><x-icon name="close" class="w-7 h-7" /></span>
    </button>
  </div>
</header>

{{-- Mobile navigation sidebar (left) --}}
<div
  id="luxe-mobile-nav-drawer"
  class="luxe-store fixed inset-0 z-[55] md:hidden hidden"
  data-luxe-mobile-nav-drawer
  aria-hidden="true"
>
  <div class="absolute inset-0 bg-black/50 luxe-mobile-nav-backdrop" data-luxe-mobile-nav-overlay></div>
  <aside
    class="luxe-mobile-nav-panel absolute top-0 left-0 h-full w-[min(100%,20rem)] sm:w-80 max-w-[85vw] bg-background shadow-2xl flex flex-col border-r border-outline-variant"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('Menu') }}"
    data-luxe-mobile-nav-panel
  >
    <header class="flex items-center justify-between gap-4 px-5 py-5 border-b border-outline-variant shrink-0">
      <a href="{{ route('home') }}" class="min-w-0 flex items-center" data-luxe-mobile-nav-close-link>
        @if ($logoPath !== '')
          <img src="{{ \App\Support\VehicleImageUrl::url($logoPath) }}" alt="{{ $brandName }}" class="h-10 w-auto max-w-[160px] object-contain" />
        @else
          <span class="font-display-lg text-lg text-primary uppercase tracking-tighter truncate">{{ strtoupper($brandName) }}</span>
        @endif
      </a>
      <button type="button" class="inline-flex items-center justify-center w-10 h-10 text-on-surface-variant hover:text-primary transition-colors shrink-0" aria-label="{{ __('Close menu') }}" data-luxe-mobile-nav-close>
        <x-icon name="close" class="w-7 h-7" />
      </button>
    </header>

    <nav class="flex-1 overflow-y-auto px-5 py-2" data-luxe-mobile-nav>
      <p class="pt-4 pb-1 text-[9px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">{{ __('Shop') }}</p>
      <ul class="divide-y divide-outline-variant">
        <li>
          <a
            href="{{ $featuredShopUrl }}"
            class="flex items-center justify-between py-3 text-sm font-semibold uppercase tracking-[0.1em] transition-colors {{ $featuredActive ? 'text-[#3A3C94]' : 'text-on-surface hover:text-[#3A3C94]' }}"
            data-luxe-mobile-nav-close-link
          >
            {{ __('FEATURED ITEMS') }}
            <x-icon name="chevron-right" class="w-4 h-4 opacity-40 shrink-0" />
          </a>
        </li>
        @forelse ($navCategories as $cat)
          @php $catActive = $shopActive && ! $featuredActive && $activeCategoryId === (int) $cat->id; @endphp
          <li>
            <a
              href="{{ route('shop.index', ['product_category_listing_option_id' => $cat->id]) }}"
              class="flex items-center justify-between py-3 text-sm font-semibold uppercase tracking-[0.1em] transition-colors {{ $catActive ? 'text-[#3A3C94]' : 'text-on-surface hover:text-[#3A3C94]' }}"
              data-luxe-mobile-nav-close-link
            >
              {{ strtoupper($cat->value) }}
              <x-icon name="chevron-right" class="w-4 h-4 opacity-40 shrink-0" />
            </a>
          </li>
        @empty
          <li>
            <a
              href="{{ route('shop.index') }}"
              class="flex items-center justify-between py-3 text-sm font-semibold uppercase tracking-[0.1em] transition-colors {{ $shopActive && ! $featuredActive ? 'text-[#3A3C94]' : 'text-on-surface hover:text-[#3A3C94]' }}"
              data-luxe-mobile-nav-close-link
            >
              {{ __('Shop all') }}
              <x-icon name="chevron-right" class="w-4 h-4 opacity-40 shrink-0" />
            </a>
          </li>
        @endforelse
      </ul>

      <p class="pt-6 pb-1 text-[9px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">{{ __('More') }}</p>
      <ul class="divide-y divide-outline-variant">
        <li>
          <a href="{{ route('about') }}" class="block py-3 text-xs font-medium uppercase tracking-[0.12em] text-on-surface-variant hover:text-[#3A3C94] transition-colors" data-luxe-mobile-nav-close-link>{{ __('About us') }}</a>
        </li>
        <li>
          <a href="{{ route('contact') }}" class="block py-3 text-xs font-medium uppercase tracking-[0.12em] text-on-surface-variant hover:text-[#3A3C94] transition-colors" data-luxe-mobile-nav-close-link>{{ __('Contact') }}</a>
        </li>
      </ul>
    </nav>

    <footer class="shrink-0 border-t border-outline-variant px-5 py-5 space-y-3 bg-surface-container-low">
      @auth
        <a
          href="{{ route('dashboard') }}"
          class="flex w-full items-center justify-center border-2 border-[#3A3C94] text-[#3A3C94] px-5 py-3.5 text-xs font-bold uppercase tracking-[0.2em] hover:bg-[#3A3C94] hover:text-white transition-colors"
          data-luxe-mobile-nav-close-link
        >
          {{ __('My account') }}
        </a>
      @else
        <a
          href="{{ route('login') }}"
          class="flex w-full items-center justify-center border-2 border-[#3A3C94] text-[#3A3C94] px-5 py-3.5 text-xs font-bold uppercase tracking-[0.2em] hover:bg-[#3A3C94] hover:text-white transition-colors"
          data-luxe-mobile-nav-close-link
        >
          {{ __('Sign in') }}
        </a>
      @endauth
      <a
        href="{{ route('shop.index') }}"
        class="flex w-full items-center justify-center bg-black text-white px-5 py-4 text-xs font-bold uppercase tracking-[0.25em] hover:opacity-90 transition-opacity"
        data-luxe-mobile-nav-close-link
      >
        {{ __('Shop now') }}
      </a>
    </footer>
  </aside>
</div>

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
    'addedToCart' => __(':product has been added to cart'),
    'item'       => __('item'),
    'items'      => __('items'),
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
      closeMobileNav();
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
      var mobileOpen = qs('[data-luxe-mobile-nav-panel]') && qs('[data-luxe-mobile-nav-panel]').classList.contains('is-open');
      if (!mobileOpen) document.body.style.overflow = '';
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

    function updateFloatingWidget(state) {
      var w = qs('[data-luxe-cart-widget]');
      if (!w) return;
      var count = state.count || 0;
      if (count <= 0) {
        w.classList.add('hidden');
        w.setAttribute('aria-hidden', 'true');
        return;
      }
      w.classList.remove('hidden');
      w.setAttribute('aria-hidden', 'false');
      var countEl = qs('[data-luxe-cart-widget-count]', w);
      var totalEl = qs('[data-luxe-cart-widget-total]', w);
      if (countEl) {
        countEl.textContent = count + ' ' + (count === 1 ? (I18N.item || 'item') : (I18N.items || 'items'));
      }
      if (totalEl && state.subtotal_formatted) {
        totalEl.textContent = state.subtotal_formatted;
      }
    }

    var toastTimer = null;
    function showAddedToast(productName) {
      var toast = qs('[data-luxe-cart-toast]');
      var msg = qs('[data-luxe-cart-toast-message]', toast);
      if (!toast || !msg || !productName) return;
      var tpl = I18N.addedToCart || ':product has been added to cart';
      msg.textContent = tpl.replace(':product', productName);
      toast.classList.remove('hidden');
      if (toastTimer) clearTimeout(toastTimer);
      toastTimer = setTimeout(function () { toast.classList.add('hidden'); }, 4500);
    }

    function applyState(state) {
      updateBadge(state.count || 0);
      renderLines(state);
      updateFloatingWidget(state);
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
      if (e.key === 'Escape') {
        closeDrawer();
        closeMobileNav();
      }
    });

    function openMobileNav() {
      closeDrawer();
      var drawer = qs('[data-luxe-mobile-nav-drawer]');
      var panel = qs('[data-luxe-mobile-nav-panel]');
      var backdrop = qs('[data-luxe-mobile-nav-overlay]');
      var btn = qs('[data-luxe-mobile-nav-toggle]');
      var iconMenu = qs('[data-luxe-mobile-nav-icon-menu]');
      var iconClose = qs('[data-luxe-mobile-nav-icon-close]');
      if (!drawer || !panel) return;
      drawer.classList.remove('hidden');
      drawer.setAttribute('aria-hidden', 'false');
      requestAnimationFrame(function () {
        panel.classList.add('is-open');
        if (backdrop) backdrop.classList.add('is-open');
      });
      document.body.style.overflow = 'hidden';
      if (btn) btn.setAttribute('aria-expanded', 'true');
      if (iconMenu) iconMenu.classList.add('hidden');
      if (iconClose) iconClose.classList.remove('hidden');
    }

    function closeMobileNav() {
      var drawer = qs('[data-luxe-mobile-nav-drawer]');
      var panel = qs('[data-luxe-mobile-nav-panel]');
      var backdrop = qs('[data-luxe-mobile-nav-overlay]');
      var btn = qs('[data-luxe-mobile-nav-toggle]');
      var iconMenu = qs('[data-luxe-mobile-nav-icon-menu]');
      var iconClose = qs('[data-luxe-mobile-nav-icon-close]');
      if (!drawer || !panel) return;
      panel.classList.remove('is-open');
      if (backdrop) backdrop.classList.remove('is-open');
      var cartOpen = qs('[data-cart-drawer]') && !qs('[data-cart-drawer]').classList.contains('hidden');
      if (!cartOpen) document.body.style.overflow = '';
      setTimeout(function () {
        if (!panel.classList.contains('is-open')) {
          drawer.classList.add('hidden');
          drawer.setAttribute('aria-hidden', 'true');
        }
      }, 300);
      if (btn) btn.setAttribute('aria-expanded', 'false');
      if (iconMenu) iconMenu.classList.remove('hidden');
      if (iconClose) iconClose.classList.add('hidden');
    }

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
          if (state && state.added_product_name) {
            showAddedToast(state.added_product_name);
          }
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

    document.addEventListener('click', function (e) {
      if (e.target.closest('[data-luxe-mobile-nav-toggle]')) {
        e.preventDefault();
        var drawer = qs('[data-luxe-mobile-nav-drawer]');
        var panel = qs('[data-luxe-mobile-nav-panel]');
        if (drawer && panel && panel.classList.contains('is-open')) {
          closeMobileNav();
        } else {
          openMobileNav();
        }
        return;
      }
      if (e.target.closest('[data-luxe-mobile-nav-close]') || e.target.matches('[data-luxe-mobile-nav-overlay]')) {
        closeMobileNav();
        return;
      }
      if (e.target.closest('[data-luxe-mobile-nav-close-link]')) {
        closeMobileNav();
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      fetchState();
    });
  })();
</script>
@endpush

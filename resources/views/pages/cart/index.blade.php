@extends('layouts.site')

@section('content')
  @php
    $lineCount = \App\Support\Cart::count();
  @endphp
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md" data-cart-page>
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <nav class="mb-8 md:mb-12 flex items-center gap-2 font-label-caps text-label-caps text-on-surface-variant">
        <a href="{{ route('shop.index') }}" class="hover:text-black transition-colors">{{ __('Shop') }}</a>
        <span>/</span>
        <span class="text-primary underline">{{ __('Shopping Bag') }}</span>
      </nav>

      <div class="mb-8 rounded border border-outline-variant bg-white px-4 py-3 text-sm hidden" data-cart-flash role="status"></div>

      @if ($lines === [])
        <div class="py-20 text-center" data-cart-empty>
          <p class="text-lg font-medium mb-6">{{ __('Your bag is empty.') }}</p>
          <a href="{{ route('shop.index') }}" class="inline-block bg-black text-white px-8 py-4 text-xs font-bold uppercase tracking-[0.2em] hover:opacity-90">{{ __('Continue shopping') }}</a>
        </div>
      @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16" data-cart-content>
          <section class="lg:col-span-8 space-y-8" data-cart-lines>
            <div class="flex justify-between items-end border-b border-outline-variant pb-4">
              <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase">{{ __('Your Bag') }}</h1>
              <span class="font-label-caps text-label-caps text-on-surface-variant" data-cart-item-count>{{ trans_choice(':count item|:count items', $lineCount, ['count' => $lineCount]) }}</span>
            </div>

            @foreach ($lines as $line)
              <div
                class="flex flex-col md:flex-row gap-8 py-8 border-b border-[#cfc4c5] group"
                data-cart-line
                data-vehicle-id="{{ $line['vehicle_id'] }}"
                @if (!empty($line['vehicle_variant_id'])) data-variant-id="{{ $line['vehicle_variant_id'] }}" @endif
              >
                <div class="w-full md:w-40 h-[200px] md:h-[220px] bg-[#eeeeee] overflow-hidden shrink-0">
                  @if (!empty($line['image']))
                    <img src="{{ $line['image'] }}" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" data-cart-line-image />
                  @endif
                </div>
                <div class="flex-grow flex flex-col justify-between min-w-0">
                  <div class="flex justify-between items-start gap-4">
                    <div>
                      <h2 class="text-xl uppercase tracking-wide font-medium" data-cart-line-name>{{ $line['name'] }}</h2>
                      @if (!empty($line['variant_label']))
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#4c4546] mt-4" data-cart-line-variant>{{ $line['variant_label'] }}</p>
                      @endif
                      @if (!empty($line['sku']))
                        <p class="text-[10px] text-[#7e7576] mt-1">{{ __('SKU') }}: {{ $line['sku'] }}</p>
                      @endif
                    </div>
                    <p class="text-xl font-medium whitespace-nowrap" data-cart-line-unit-price>{{ format_currency($line['unit_price']) }}</p>
                  </div>
                  <div class="flex flex-wrap justify-between items-center gap-4 mt-8">
                    <div class="flex items-center border border-[#7e7576] px-4 py-2" data-cart-qty-controls>
                      <button type="button" class="hover:text-black inline-flex items-center disabled:opacity-40" data-cart-qty-dec aria-label="{{ __('Decrease quantity') }}">
                        <x-icon name="minus" class="w-4 h-4" />
                      </button>
                      <span class="mx-6 min-w-[20px] text-center text-sm" data-cart-line-qty>{{ $line['qty'] }}</span>
                      <button type="button" class="hover:text-black inline-flex items-center disabled:opacity-40" data-cart-qty-inc aria-label="{{ __('Increase quantity') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                      </button>
                    </div>
                    <button type="button" class="text-[10px] font-bold uppercase tracking-widest text-[#4c4546] hover:text-[#ba1a1a] flex items-center gap-1.5" data-cart-remove>
                      <x-icon name="close" class="w-3.5 h-3.5" /> {{ __('Remove') }}
                    </button>
                  </div>
                </div>
              </div>
            @endforeach
          </section>

          <aside class="lg:col-span-4 lg:sticky lg:top-32 h-fit bg-white p-8 border border-[#cfc4c5]">
            <h2 class="text-xl uppercase mb-8 border-b border-[#cfc4c5] pb-4">{{ __('Order Summary') }}</h2>
            <div class="space-y-6">
              <div class="flex justify-between">
                <span class="text-[#4c4546]">{{ __('Subtotal') }}</span>
                <span class="font-bold" data-cart-subtotal>{{ format_currency($subtotal) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#4c4546]">{{ __('Shipping') }}</span>
                <span class="text-[#78582f]">{{ __('Calculated at checkout') }}</span>
              </div>
              <div class="border-t border-[#cfc4c5] pt-6 flex justify-between items-center">
                <span class="text-sm font-bold uppercase tracking-widest">{{ __('Total') }}</span>
                <span class="text-2xl font-medium" data-cart-total>{{ format_currency($subtotal) }}</span>
              </div>
              <a href="{{ route('checkout.index') }}" class="block w-full bg-black text-white text-center py-5 text-xs font-bold uppercase tracking-[0.25em] hover:opacity-90 transition-opacity">
                {{ __('Proceed to checkout') }}
              </a>
              <a href="{{ route('shop.index') }}" class="block text-center text-[10px] font-bold uppercase tracking-widest text-[#4c4546] hover:text-black pt-2">
                {{ __('Continue shopping') }}
              </a>
            </div>
          </aside>
        </div>
      @endif
    </div>
  </div>
@endsection

@push('scripts')
<script id="luxe-cart-page-config" type="application/json">
{!! json_encode([
  'urls' => [
    'update' => route('cart.update'),
    'remove' => route('cart.remove'),
    'state' => route('cart.state'),
  ],
  'i18n' => [
    'item' => __('item'),
    'items' => __('items'),
    'empty' => __('Your bag is empty.'),
    'error' => __('Could not update bag.'),
    'continueShopping' => __('Continue shopping'),
  ],
  'shopUrl' => route('shop.index'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
(function () {
  var root = document.querySelector('[data-cart-page]');
  if (!root) return;

  var cfg = {};
  try { cfg = JSON.parse(document.getElementById('luxe-cart-page-config').textContent); } catch (e) { return; }
  var URLS = cfg.urls;
  var I18N = cfg.i18n;
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  function qs(sel, el) { return (el || root).querySelector(sel); }
  function qsa(sel, el) { return Array.prototype.slice.call((el || root).querySelectorAll(sel)); }

  function flash(msg) {
    var el = qs('[data-cart-flash]');
    if (!el) return;
    el.textContent = msg || '';
    el.classList.toggle('hidden', !msg);
  }

  function itemCountLabel(count) {
    return count + ' ' + (count === 1 ? I18N.item : I18N.items);
  }

  function post(url, data) {
    var fd = new FormData();
    Object.keys(data).forEach(function (k) { fd.append(k, data[k]); });
    return fetch(url, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
      body: fd,
      credentials: 'same-origin',
    }).then(function (res) {
      if (!res.ok) {
        return res.json().catch(function () { return {}; }).then(function (j) {
          throw new Error((j && j.message) || I18N.error);
        });
      }
      return res.json();
    });
  }

  function syncHeaderBadge(count) {
    document.querySelectorAll('[data-cart-count-badge]').forEach(function (el) {
      el.textContent = count > 9 ? '9+' : String(count);
      el.classList.toggle('hidden', !(count > 0));
    });
  }

  function syncFloatingWidget(state) {
    var w = document.querySelector('[data-luxe-cart-widget]');
    if (!w) return;
    var count = state.count || 0;
    if (count <= 0) {
      w.classList.add('hidden');
      return;
    }
    w.classList.remove('hidden');
    var countEl = w.querySelector('[data-luxe-cart-widget-count]');
    var totalEl = w.querySelector('[data-luxe-cart-widget-total]');
    if (countEl) countEl.textContent = count + ' ' + (count === 1 ? 'item' : 'items');
    if (totalEl && state.subtotal_formatted) totalEl.textContent = state.subtotal_formatted;
  }

  function applyState(state) {
    syncHeaderBadge(state.count || 0);
    syncFloatingWidget(state);

    var sub = state.subtotal_formatted || '';
    qsa('[data-cart-subtotal], [data-cart-total]').forEach(function (el) { el.textContent = sub; });

    var countEl = qs('[data-cart-item-count]');
    if (countEl) countEl.textContent = itemCountLabel(state.count || 0);

    if (!state.lines || state.lines.length === 0) {
      var content = qs('[data-cart-content]');
      if (content) content.classList.add('hidden');
      var empty = qs('[data-cart-empty]');
      if (!empty) {
        empty = document.createElement('div');
        empty.className = 'py-20 text-center';
        empty.setAttribute('data-cart-empty', '');
        empty.innerHTML = '<p class="text-lg font-medium mb-6">' + I18N.empty + '</p><a href="' + cfg.shopUrl + '" class="inline-block bg-black text-white px-8 py-4 text-xs font-bold uppercase tracking-[0.2em]">' + I18N.continueShopping + '</a>';
        root.querySelector('.max-w-max-container').appendChild(empty);
      } else {
        empty.classList.remove('hidden');
      }
      return;
    }

    var empty = qs('[data-cart-empty]');
    if (empty) empty.classList.add('hidden');
    var content = qs('[data-cart-content]');
    if (content) content.classList.remove('hidden');

    state.lines.forEach(function (line) {
      var row = qsa('[data-cart-line]').find(function (r) {
        var vid = r.getAttribute('data-vehicle-id');
        var varId = r.getAttribute('data-variant-id') || '';
        var lineVar = line.vehicle_variant_id ? String(line.vehicle_variant_id) : '';
        return vid === String(line.vehicle_id) && varId === lineVar;
      });
      if (!row) return;
      var qtyEl = qs('[data-cart-line-qty]', row);
      if (qtyEl) qtyEl.textContent = String(line.qty);
      var dec = qs('[data-cart-qty-dec]', row);
      var inc = qs('[data-cart-qty-inc]', row);
      if (dec) dec.disabled = line.qty <= 1;
      if (inc) inc.disabled = line.qty >= 99;
    });
  }

  function linePayload(row) {
    var data = { vehicle_id: row.getAttribute('data-vehicle-id') };
    var vid = row.getAttribute('data-variant-id');
    if (vid) data.vehicle_variant_id = vid;
    return data;
  }

  root.addEventListener('click', function (e) {
    var row = e.target.closest('[data-cart-line]');
    if (!row) return;

    if (e.target.closest('[data-cart-qty-dec]')) {
      e.preventDefault();
      var qty = Math.max(1, parseInt(qs('[data-cart-line-qty]', row).textContent, 10) - 1);
      var payload = linePayload(row);
      payload.qty = qty;
      post(URLS.update, payload).then(function (state) {
        applyState(state);
        if (state.message) flash(state.message);
      }).catch(function (err) { flash(err.message); });
      return;
    }

    if (e.target.closest('[data-cart-qty-inc]')) {
      e.preventDefault();
      var qty2 = Math.min(99, parseInt(qs('[data-cart-line-qty]', row).textContent, 10) + 1);
      var payload2 = linePayload(row);
      payload2.qty = qty2;
      post(URLS.update, payload2).then(function (state) {
        applyState(state);
        if (state.message) flash(state.message);
      }).catch(function (err) { flash(err.message); });
      return;
    }

    if (e.target.closest('[data-cart-remove]')) {
      e.preventDefault();
      post(URLS.remove, linePayload(row)).then(function (state) {
        row.remove();
        applyState(state);
        if (state.message) flash(state.message);
      }).catch(function (err) { flash(err.message); });
    }
  });
})();
</script>
@endpush

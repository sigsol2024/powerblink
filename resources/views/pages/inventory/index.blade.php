@extends('layouts.site')

@push('head')
  @include('partials.luxe-home-styles')
@endpush

@section('content')
  @php
    $activeFilterCount = collect([
      'q', 'street_q', 'condition_listing_option_id', 'body_type_listing_option_id',
      'make_listing_option_id', 'model_listing_option_id', 'transmission_listing_option_id',
      'fuel_type_listing_option_id', 'drive_listing_option_id', 'country_listing_option_id',
      'type_listing_option_id', 'exterior_color', 'year_min', 'year_max', 'mileage_min',
      'mileage_max', 'price_min', 'price_max',
    ])->filter(function ($k) use ($filters) {
        $v = $filters[$k] ?? '';
        if ($v === null || $v === '') {
            return false;
        }
        if (is_numeric($v) && (int) $v === 0) {
            return false;
        }

        return trim((string) $v) !== '';
    })->count();
    $conditionChoices = $filterOptions['conditions'] ?? collect();
    if (! $conditionChoices instanceof \Illuminate\Support\Collection) {
      $conditionChoices = collect();
    }
    $modelMatrix = $filterOptions['model_matrix'] ?? collect();
    if (! $modelMatrix instanceof \Illuminate\Support\Collection) {
      $modelMatrix = collect();
    }
    $conditions = $conditionChoices;
    $countries = collect($filterOptions['countries'] ?? []);
    $originTypes = collect($filterOptions['vehicle_origin_types'] ?? []);
    $extColors = collect($filterOptions['exterior_colors'] ?? []);
    $nigerianTypeId = \App\Support\VehicleListingCatalog::vehicleOriginTypeIdByLabel('Nigerian');
  @endphp

  <main class="luxe-store pt-28 md:pt-32 pb-24 md:pb-section-py-desktop max-w-max-container mx-auto px-margin-mobile md:px-gutter luxe-geometric-bg bg-background text-on-background min-h-screen font-body-md">
    <div class="mb-10 md:mb-16 border-b border-outline-variant pb-6 md:pb-8 flex flex-col md:flex-row justify-between items-end gap-6">
      <div>
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">{{ __('CURATED SERIES') }}</p>
        <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase">{{ $sections['heading'] ?? ($page?->title ?? __('Collections')) }}</h1>
      </div>
      <div class="flex items-center gap-4 w-full md:w-auto">
        <span class="font-label-caps text-label-caps text-on-surface-variant shrink-0">{{ __('SORT BY') }}</span>
        <div class="relative inline-block text-left flex-1 md:flex-initial min-w-[12rem]">
          <select id="inventory-sort" name="sort" form="inventory-filter-form" class="appearance-none bg-transparent border-none font-body-md text-body-md pr-8 focus:ring-0 cursor-pointer text-primary font-medium w-full">
            <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('NEWEST ARRIVALS') }}</option>
            <option value="price_low" @selected(($filters['sort'] ?? '') === 'price_low')>{{ __('PRICE: LOW TO HIGH') }}</option>
            <option value="price_high" @selected(($filters['sort'] ?? '') === 'price_high')>{{ __('PRICE: HIGH TO LOW') }}</option>
            <option value="year_new" @selected(($filters['sort'] ?? '') === 'year_new')>{{ __('YEAR: NEWEST') }}</option>
            <option value="year_old" @selected(($filters['sort'] ?? '') === 'year_old')>{{ __('YEAR: OLDEST') }}</option>
          </select>
          <span class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none"><x-icon name="chevron-down" class="w-3.5 h-3.5" /></span>
        </div>
      </div>
    </div>

    @if (!empty($page?->content_html))
      <section class="mb-8 border border-outline-variant bg-surface-container-lowest p-6 prose prose-sm max-w-none text-on-background">
        {!! $page->content_html !!}
      </section>
    @endif

    <div class="flex flex-col md:flex-row gap-8 md:gap-10">
      <aside class="w-full md:w-64 shrink-0 hidden md:block">
        @include('pages.inventory.partials.shop-luxe-sidebar', [
          'formId' => 'inventory-filter-form',
          'filters' => $filters,
          'filterOptions' => $filterOptions,
          'conditions' => $conditions,
          'countries' => $countries,
          'originTypes' => $originTypes,
          'extColors' => $extColors,
          'nigerianTypeId' => $nigerianTypeId,
        ])
      </aside>

      <div class="flex-1 min-w-0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 md:gap-10">
          @forelse ($vehicles as $vehicle)
            @php
              $vehicleUrl = route('product.show', ['slug' => $vehicle->slug]);
              $invFallback = \App\Support\PlaceholderMedia::url($sections['fallback_image'] ?? 'asset/images/media/inventory-listing-fallback.jpg');
            @endphp
            <article class="group cursor-pointer">
              <a href="{{ $vehicleUrl }}" class="block relative aspect-[3/4] overflow-hidden bg-surface-container mb-4">
                @include('partials.vehicle-hover-gallery', [
                  'vehicle' => $vehicle,
                  'fallback' => $invFallback,
                  'imgClass' => 'w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110',
                ])
                @if ($vehicle->is_special)
                  <span class="absolute top-4 left-4 bg-background text-primary px-3 py-1 font-label-caps text-[10px] border border-outline-variant">{{ __('NEW ARRIVAL') }}</span>
                @endif
              </a>
              <a href="{{ $vehicleUrl }}" class="block">
                <h2 class="font-body-md text-body-md uppercase tracking-wider text-primary mb-1 line-clamp-2">{{ $vehicle->title ?? ($vehicle->modelOption?->value ?? __('Product')) }}</h2>
                <p class="font-body-md text-on-surface-variant">
                  @if (! is_null($vehicle->price))
                    {{ format_currency($vehicle->price) }}
                  @else
                    {{ __('Price on request') }}
                  @endif
                </p>
              </a>
            </article>
          @empty
            <div class="col-span-full py-16 text-center font-body-md text-on-surface-variant">{{ __('No products found.') }}</div>
          @endforelse
        </div>

        @if ($vehicles->hasPages())
          <div class="mt-16 md:mt-20 luxe-pagination flex justify-center">
            {{ $vehicles->links() }}
          </div>
        @endif
      </div>
    </div>
  </main>

  <button
    id="mobile-filter-trigger"
    type="button"
    class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-primary text-on-primary px-6 py-4 font-button-text text-button-text uppercase tracking-widest shadow-[0_-4px_24px_rgba(0,0,0,0.12)] flex items-center justify-center gap-2"
  >
    <x-icon name="filter" class="w-4 h-4" />
    {{ __('Filters') }}@if ($activeFilterCount > 0) ({{ $activeFilterCount }})@endif
  </button>

  <div id="mobile-filter-modal" class="md:hidden fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm" aria-hidden="true">
    <div class="absolute inset-x-0 bottom-0 top-16 bg-background border-t border-outline-variant flex flex-col max-h-[92vh] rounded-t-lg overflow-hidden">
      <div class="flex items-center justify-between px-margin-mobile py-4 border-b border-outline-variant shrink-0">
        <h3 class="font-label-caps text-label-caps text-primary uppercase tracking-widest">{{ __('Filters') }}</h3>
        <button id="mobile-filter-close" type="button" class="text-primary p-2 inline-flex items-center" aria-label="{{ __('Close') }}"><x-icon name="close" class="w-5 h-5" /></button>
      </div>
      <div class="flex-1 overflow-y-auto luxe-hide-scrollbar px-margin-mobile py-6 pb-24">
        @include('pages.inventory.partials.shop-luxe-sidebar', [
          'formId' => 'inventory-filter-form-mobile',
          'filters' => $filters,
          'filterOptions' => $filterOptions,
          'conditions' => $conditions,
          'countries' => $countries,
          'originTypes' => $originTypes,
          'extColors' => $extColors,
          'nigerianTypeId' => $nigerianTypeId,
        ])
      </div>
    </div>
  </div>

  <script type="application/json" id="inventoryModelMatrixJson">@json($modelMatrix->values()->all())</script>
  <script>
    (() => {
      const sort = document.getElementById('inventory-sort');
      const form = document.getElementById('inventory-filter-form');
      const mobileForm = document.getElementById('inventory-filter-form-mobile');
      const mobileSortHidden = mobileForm?.querySelector('[data-mobile-sort-field]');

      if (sort && form) {
        sort.addEventListener('change', () => {
          if (mobileSortHidden) mobileSortHidden.value = sort.value;
          form.submit();
        });
      }

      const mobileTrigger = document.getElementById('mobile-filter-trigger');
      const mobileModal = document.getElementById('mobile-filter-modal');
      const mobileClose = document.getElementById('mobile-filter-close');
      function openModal() {
        if (!mobileModal) return;
        mobileModal.classList.remove('hidden');
        mobileModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (sort && mobileSortHidden) {
          mobileSortHidden.value = sort.value;
        }
      }
      function closeModal() {
        if (!mobileModal) return;
        mobileModal.classList.add('hidden');
        mobileModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }
      mobileTrigger?.addEventListener('click', openModal);
      mobileClose?.addEventListener('click', closeModal);
      mobileModal?.addEventListener('click', (e) => { if (e.target === mobileModal) closeModal(); });

      document.querySelectorAll('[data-price-range-slider]').forEach((slider) => {
        const wrap = slider.closest('form') || slider.parentElement;
        const minInput = wrap?.querySelector('[data-price-min-input]');
        const maxInput = wrap?.querySelector('[data-price-max-input]');
        const maxLabel = wrap?.querySelector('[data-price-max-label]');
        slider.addEventListener('input', () => {
          const v = parseInt(slider.value, 10) || 0;
          if (maxInput) maxInput.value = String(v);
          if (maxLabel) maxLabel.textContent = v >= parseInt(slider.max, 10) ? '{{ __('$5,000+') }}' : String(v);
        });
      });

      document.querySelectorAll('[data-luxe-palette]').forEach((wrap) => {
        const input = wrap.querySelector('[data-luxe-palette-input]');
        const formEl = wrap.closest('form');
        wrap.querySelectorAll('[data-luxe-palette-value]').forEach((btn) => {
          btn.addEventListener('click', () => {
            if (!input || !formEl) return;
            input.value = btn.getAttribute('data-luxe-palette-value') || '';
            formEl.submit();
          });
        });
      });

      const matrixEl = document.getElementById('inventoryModelMatrixJson');
      const matrix = matrixEl ? JSON.parse(matrixEl.textContent || '[]') : [];
      function bindInventoryMakeModel(formEl) {
        if (!matrix.length || !formEl) return;
        const makeEl = formEl.querySelector('select[name="make_listing_option_id"]');
        const modelEl = formEl.querySelector('select[name="model_listing_option_id"]');
        if (!makeEl || !modelEl) return;
        const initialMake = makeEl.value || '';
        const initialModel = String(modelEl.dataset.initialModel || '');
        function rebuild() {
          const mk = makeEl.value || '';
          modelEl.innerHTML = '<option value="">{{ __('Any') }}</option>';
          if (!mk) return;
          matrix.forEach((r) => {
            if (!r || !r.model_id) return;
            if (String(r.make_id) !== mk) return;
            const o = document.createElement('option');
            o.value = String(r.model_id);
            o.textContent = r.model || '';
            if (String(r.model_id) === String(initialModel) && String(r.make_id) === String(initialMake)) o.selected = true;
            modelEl.appendChild(o);
          });
        }
        makeEl.addEventListener('change', rebuild);
        rebuild();
      }
      bindInventoryMakeModel(form);
      bindInventoryMakeModel(mobileForm);

      function bindInventoryTypeCountry(formEl) {
        if (!formEl) return;
        const typeEl = formEl.querySelector('select[name="type_listing_option_id"]');
        const countryWrap = formEl.querySelector('[data-country-wrap]');
        const countryEl = formEl.querySelector('select[name="country_listing_option_id"]');
        if (!typeEl || !countryWrap || !countryEl) return;
        const nigerianId = String(typeEl.getAttribute('data-nigerian-id') || '');
        function sync() {
          const isNigerian = nigerianId && String(typeEl.value || '') === nigerianId;
          countryWrap.classList.toggle('hidden', !!isNigerian);
          if (isNigerian) countryEl.value = '';
        }
        typeEl.addEventListener('change', sync);
        sync();
      }
      bindInventoryTypeCountry(form);
      bindInventoryTypeCountry(mobileForm);
    })();
  </script>
@endsection

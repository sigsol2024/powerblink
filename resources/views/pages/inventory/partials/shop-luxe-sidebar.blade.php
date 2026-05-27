@php
  $formId = $formId ?? 'inventory-filter-form';
  $categories = collect($filterOptions['categories'] ?? []);
  $activeCategory = (int) ($filters['product_category_listing_option_id'] ?? 0);
  $priceMinVal = (int) ($filters['price_min'] ?? 0);
  $priceMaxVal = (int) ($filters['price_max'] ?? 0);
  $sliderMax = max($priceMaxVal, 5000);
  $sliderMin = min($priceMinVal > 0 ? $priceMinVal : 100, $sliderMax - 1);
  $sliderValue = $priceMaxVal > 0 ? min($priceMaxVal, $sliderMax) : (int) round(($sliderMin + $sliderMax) / 2);
@endphp

<form id="{{ $formId }}" method="get" action="{{ route('shop.index') }}" class="shop-luxe-filter-form space-y-12">
  @if ($formId === 'inventory-filter-form-mobile')
    <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}" data-mobile-sort-field />
  @endif

  <section>
    <h3 class="font-label-caps text-label-caps text-primary mb-6 border-b border-outline-variant pb-2">{{ __('CATEGORIES') }}</h3>
    <ul class="space-y-4 font-body-md text-body-md">
      <li>
        <a
          href="{{ route('shop.index', request()->except(['product_category_listing_option_id', 'page'])) }}"
          class="{{ $activeCategory === 0 ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary transition-colors' }}"
        >{{ __('ALL PRODUCTS') }}</a>
      </li>
      @foreach ($categories as $row)
        @php
          $isActive = $activeCategory === (int) $row->id;
          $catUrl = route('shop.index', array_merge(
            request()->except(['product_category_listing_option_id', 'page']),
            ['product_category_listing_option_id' => $row->id]
          ));
        @endphp
        <li>
          <a href="{{ $catUrl }}" class="{{ $isActive ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary transition-colors' }}">
            {{ strtoupper($row->value) }}
          </a>
        </li>
      @endforeach
    </ul>
  </section>

  <section>
    <h3 class="font-label-caps text-label-caps text-primary mb-6 border-b border-outline-variant pb-2">{{ __('PRICE RANGE') }}</h3>
    <div class="space-y-6">
      <input
        type="range"
        class="shop-luxe-range w-full h-px bg-outline-variant appearance-none accent-primary cursor-pointer"
        min="0"
        max="{{ $sliderMax }}"
        value="{{ $sliderValue }}"
        data-price-range-slider
        aria-label="{{ __('Price range') }}"
      />
      <div class="flex justify-between font-body-md text-body-md text-on-surface-variant">
        <span data-price-min-label>{{ $priceMinVal > 0 ? format_currency($priceMinVal) : __('$100') }}</span>
        <span data-price-max-label>{{ $priceMaxVal > 0 ? format_currency($priceMaxVal) : __('$5,000+') }}</span>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <input name="price_min" type="number" value="{{ $filters['price_min'] ?? '' }}" placeholder="{{ __('Min') }}" class="shop-luxe-field w-full border-b border-outline-variant bg-transparent py-2 font-label-caps text-[11px] focus:border-primary focus:outline-none" data-price-min-input />
        <input name="price_max" type="number" value="{{ $filters['price_max'] ?? '' }}" placeholder="{{ __('Max') }}" class="shop-luxe-field w-full border-b border-outline-variant bg-transparent py-2 font-label-caps text-[11px] focus:border-primary focus:outline-none" data-price-max-input />
      </div>
    </div>
  </section>

  <section>
    <h3 class="font-label-caps text-label-caps text-primary mb-6 border-b border-outline-variant pb-2">{{ __('SEARCH') }}</h3>
    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search collection...') }}" class="shop-luxe-field w-full border-b border-outline-variant bg-transparent py-2 font-label-caps text-[11px] focus:border-primary focus:outline-none" />
  </section>

  <div class="flex flex-col gap-3 pt-2">
    <button type="submit" class="w-full bg-primary text-on-primary py-4 font-button-text text-button-text uppercase tracking-widest hover:opacity-90 transition-opacity">
      {{ __('Apply filters') }}
    </button>
    <a href="{{ route('shop.index') }}" class="w-full text-center border border-outline-variant py-4 font-button-text text-button-text uppercase tracking-widest text-on-surface-variant hover:border-primary hover:text-primary transition-colors">
      {{ __('Reset all') }}
    </a>
  </div>
</form>

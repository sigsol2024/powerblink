@php
  $formId = $formId ?? 'inventory-filter-form';
  $makes = collect($filterOptions['makes'] ?? []);
  $bodyTypes = collect($filterOptions['body_types'] ?? []);
  $categoryRows = $bodyTypes->isNotEmpty() ? $bodyTypes : $makes;
  $activeMake = (int) ($filters['make_listing_option_id'] ?? 0);
  $activeBody = (int) ($filters['body_type_listing_option_id'] ?? 0);
  $activeColor = (string) ($filters['exterior_color'] ?? '');
  $priceMinVal = (int) ($filters['price_min'] ?? 0);
  $priceMaxVal = (int) ($filters['price_max'] ?? 0);
  $sliderMax = max($priceMaxVal, 5000);
  $sliderMin = min($priceMinVal > 0 ? $priceMinVal : 100, $sliderMax - 1);
  $sliderValue = $priceMaxVal > 0 ? min($priceMaxVal, $sliderMax) : (int) round(($sliderMin + $sliderMax) / 2);
  $paletteSwatches = [
    ['value' => '', 'hex' => 'transparent', 'ring' => true, 'label' => __('All')],
  ];
  foreach ($extColors->take(8) as $ec) {
    $paletteSwatches[] = [
      'value' => $ec,
      'hex' => match (strtolower($ec)) {
        'black', 'onyx', 'ebony' => '#000000',
        'white', 'ivory', 'pristine' => '#ffffff',
        'brown', 'ochre', 'tan' => '#78582f',
        default => '#cfc4c5',
      },
      'ring' => $activeColor === $ec,
      'label' => $ec,
    ];
  }
  if (count($paletteSwatches) === 1) {
    $paletteSwatches = array_merge($paletteSwatches, [
      ['value' => 'Onyx', 'hex' => '#000000', 'ring' => $activeColor === 'Onyx', 'label' => 'Ebony'],
      ['value' => 'Ivory', 'hex' => '#ffffff', 'ring' => $activeColor === 'Ivory', 'label' => 'Pristine'],
      ['value' => 'Ochre', 'hex' => '#78582f', 'ring' => $activeColor === 'Ochre', 'label' => 'Ochre'],
      ['value' => 'Slate', 'hex' => '#cfc4c5', 'ring' => $activeColor === 'Slate', 'label' => 'Slate'],
    ]);
  }
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
          href="{{ route('shop.index', request()->except(['make_listing_option_id', 'body_type_listing_option_id', 'page'])) }}"
          class="{{ ($activeMake === 0 && $activeBody === 0) ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary transition-colors' }}"
        >{{ __('ALL APPAREL') }}</a>
      </li>
      @foreach ($categoryRows as $row)
        @php
          $paramKey = $bodyTypes->isNotEmpty() ? 'body_type_listing_option_id' : 'make_listing_option_id';
          $isActive = $bodyTypes->isNotEmpty()
            ? $activeBody === (int) $row->id
            : $activeMake === (int) $row->id;
          $catUrl = route('shop.index', array_merge(
            request()->except([$paramKey === 'body_type_listing_option_id' ? 'make_listing_option_id' : 'body_type_listing_option_id', 'page']),
            [$paramKey => $row->id]
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
    <h3 class="font-label-caps text-label-caps text-primary mb-6 border-b border-outline-variant pb-2">{{ __('PALETTE') }}</h3>
    <div class="flex flex-wrap gap-3" data-luxe-palette>
      @foreach ($paletteSwatches as $swatch)
        @if ($swatch['value'] === '')
          <button
            type="button"
            class="w-6 h-6 rounded-full border-2 border-dashed border-outline-variant {{ $activeColor === '' ? 'ring-2 ring-offset-2 ring-primary' : '' }}"
            data-luxe-palette-value=""
            title="{{ $swatch['label'] }}"
            aria-label="{{ __('All colors') }}"
          ></button>
        @else
          <button
            type="button"
            class="w-6 h-6 rounded-full border border-outline-variant {{ $swatch['ring'] ? 'ring-2 ring-offset-2 ring-primary' : '' }}"
            style="background-color: {{ $swatch['hex'] }}"
            data-luxe-palette-value="{{ $swatch['value'] }}"
            title="{{ $swatch['label'] }}"
            aria-label="{{ $swatch['label'] }}"
          ></button>
        @endif
      @endforeach
      <input type="hidden" name="exterior_color" value="{{ $activeColor }}" data-luxe-palette-input />
    </div>
  </section>

  <section>
    <details class="group border-t border-outline-variant pt-6">
      <summary class="flex cursor-pointer list-none items-center justify-between font-label-caps text-label-caps text-primary uppercase tracking-widest">
        {{ __('Refine') }}
        <span class="material-symbols-outlined text-sm group-open:rotate-180 transition-transform">expand_more</span>
      </summary>
      <div class="mt-6 space-y-4 font-body-md">
        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search collection...') }}" class="shop-luxe-field w-full border-b border-outline-variant bg-transparent py-2 font-label-caps text-[11px] focus:border-primary focus:outline-none" />

        @if ($conditions->isNotEmpty())
          <label class="block">
            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase">{{ __('Condition') }}</span>
            <select name="condition_listing_option_id" class="shop-luxe-field mt-1 w-full border-b border-outline-variant bg-transparent py-2 text-sm focus:border-primary focus:outline-none">
              <option value="">{{ __('Any') }}</option>
              @foreach ($conditions as $row)
                <option value="{{ $row->id }}" @selected((int) ($filters['condition_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>
              @endforeach
            </select>
          </label>
        @endif

        @if ($originTypes->isNotEmpty())
          <label class="block">
            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase">{{ __('Type') }}</span>
            <select name="type_listing_option_id" data-nigerian-id="{{ (int) ($nigerianTypeId ?? 0) }}" class="shop-luxe-field mt-1 w-full border-b border-outline-variant bg-transparent py-2 text-sm focus:border-primary focus:outline-none">
              <option value="">{{ __('Any') }}</option>
              @foreach ($originTypes as $row)
                <option value="{{ $row->id }}" @selected((int) ($filters['type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>
              @endforeach
            </select>
          </label>
        @endif

        <label class="block">
          <span class="font-label-caps text-[10px] text-on-surface-variant uppercase">{{ __('Category (make)') }}</span>
          <select name="make_listing_option_id" class="shop-luxe-field mt-1 w-full border-b border-outline-variant bg-transparent py-2 text-sm focus:border-primary focus:outline-none">
            <option value="">{{ __('Any') }}</option>
            @foreach ($makes as $row)
              <option value="{{ $row->id }}" @selected($activeMake === (int) $row->id)>{{ $row->value }}</option>
            @endforeach
          </select>
        </label>

        <label class="block">
          <span class="font-label-caps text-[10px] text-on-surface-variant uppercase">{{ __('Model') }}</span>
          <select name="model_listing_option_id" data-initial-model="{{ (int) ($filters['model_listing_option_id'] ?? 0) }}" class="shop-luxe-field mt-1 w-full border-b border-outline-variant bg-transparent py-2 text-sm focus:border-primary focus:outline-none">
            <option value="">{{ __('Any') }}</option>
          </select>
        </label>

        @if ($bodyTypes->isNotEmpty())
          <label class="block">
            <span class="font-label-caps text-[10px] text-on-surface-variant uppercase">{{ __('Body / style') }}</span>
            <select name="body_type_listing_option_id" class="shop-luxe-field mt-1 w-full border-b border-outline-variant bg-transparent py-2 text-sm focus:border-primary focus:outline-none">
              <option value="">{{ __('Any') }}</option>
              @foreach ($bodyTypes as $row)
                <option value="{{ $row->id }}" @selected($activeBody === (int) $row->id)>{{ $row->value }}</option>
              @endforeach
            </select>
          </label>
        @endif

        @if ($countries->isNotEmpty())
          <div data-country-wrap>
            <label class="block">
              <span class="font-label-caps text-[10px] text-on-surface-variant uppercase">{{ __('Country') }}</span>
              <select name="country_listing_option_id" class="shop-luxe-field mt-1 w-full border-b border-outline-variant bg-transparent py-2 text-sm focus:border-primary focus:outline-none">
                <option value="">{{ __('Any') }}</option>
                @foreach ($countries as $row)
                  <option value="{{ $row->id }}" @selected((int) ($filters['country_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>
                @endforeach
              </select>
            </label>
          </div>
        @endif
      </div>
    </details>
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

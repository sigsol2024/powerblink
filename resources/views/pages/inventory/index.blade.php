@extends('layouts.site')

@section('content')
  @php
    $activeFilterCount = collect([
      'q',
      'street_q',
      'condition_listing_option_id',
      'body_type_listing_option_id',
      'make_listing_option_id',
      'model_listing_option_id',
      'transmission_listing_option_id',
      'fuel_type_listing_option_id',
      'drive_listing_option_id',
      'country_listing_option_id',
      'type_listing_option_id',
      'exterior_color',
      'year_min',
      'year_max',
      'mileage_min',
      'mileage_max',
      'price_min',
      'price_max',
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
  <div class="max-w-[1400px] mx-auto px-6 md:px-12 pb-20 pt-10 bg-black text-white min-h-screen">
    <section class="py-10 flex flex-col md:flex-row md:justify-between md:items-end border-b border-white/10 mb-8 gap-4">
      <div>
        <h1 class="text-2xl font-black font-headline uppercase tracking-tight">{{ $sections['heading'] ?? ($page?->title ?? 'Vehicles For Sale') }}</h1>
      </div>
      <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:justify-end">
        <div class="relative w-full min-w-0 sm:max-w-[220px] sm:w-auto">
          <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-white/50">search</span>
          <input
            type="search"
            id="inventory-search"
            name="q"
            form="inventory-filter-form"
            value="{{ $filters['q'] ?? '' }}"
            placeholder="{{ __('Search') }}"
            autocomplete="off"
            class="inventory-filter-input w-full rounded-sm py-2.5 pl-9 pr-3 text-xs font-semibold uppercase"
          />
        </div>
        <div class="flex items-center gap-3">
          <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ __('Sort by') }}:</span>
          <div class="relative min-w-[12rem]">
            <select id="inventory-sort" name="sort" form="inventory-filter-form" class="inventory-filter-select w-full cursor-pointer rounded-sm py-2.5 pl-3 pr-8 text-xs font-medium">
              <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Date: newest first</option>
              <option value="price_low" @selected(($filters['sort'] ?? '') === 'price_low')>Price: low to high</option>
              <option value="price_high" @selected(($filters['sort'] ?? '') === 'price_high')>Price: high to low</option>
              <option value="year_new" @selected(($filters['sort'] ?? '') === 'year_new')>Year: newest</option>
              <option value="year_old" @selected(($filters['sort'] ?? '') === 'year_old')>Year: oldest</option>
            </select>
            <span class="material-symbols-outlined pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
        </div>
      </div>
    </section>

    @if (!empty($page?->content_html))
      <section class="mb-8 rounded bg-white/5 border border-white/10 p-6 prose prose-invert max-w-none">
        {!! $page->content_html !!}
      </section>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">
      <div class="flex-1 space-y-6">
        @forelse ($vehicles as $vehicle)
          @php
            $vehicleUrl = route('inventory.show', ['slug' => $vehicle->slug]);
            $invFallback = \App\Support\PlaceholderMedia::url($sections['fallback_image'] ?? 'asset/images/media/inventory-listing-fallback.jpg');
          @endphp
          <article class="bg-card_bg overflow-hidden flex flex-col md:flex-row relative group">
            <div class="md:w-[320px] h-[240px] relative overflow-hidden shrink-0">
              <a href="{{ $vehicleUrl }}" class="block h-full w-full">
                @include('partials.vehicle-hover-gallery', [
                  'vehicle' => $vehicle,
                  'fallback' => $invFallback,
                  'imgClass' => 'w-full h-full object-cover',
                ])
              </a>
              @if($vehicle->is_special)
                <div class="sold-ribbon">Special</div>
              @endif
            </div>
            <div class="flex-1 p-6 flex flex-col justify-between">
              <div class="flex justify-between items-start gap-4">
                <div>
                  <a href="{{ $vehicleUrl }}" class="text-white text-xs font-bold uppercase tracking-wider font-body hover:text-primary transition-colors">{{ $vehicle->title ?? ($vehicle->modelOption?->value ?? 'Vehicle') }}</a>
                  <p class="text-3xl font-black font-headline leading-none mt-1">{{ $vehicle->year ?? '----' }}</p>
                </div>
                <div class="bg-brand_blue px-4 py-2 rounded-sm text-white font-bold text-xs uppercase text-right">
                  <span class="opacity-70 font-normal">Our Price</span>
                  @if (!is_null($vehicle->price))
                    {{ format_currency($vehicle->price) }}
                  @else
                    Ask
                  @endif
                </div>
              </div>

              <div class="flex flex-wrap gap-8 mt-6">
                <div class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary">speed</span>
                  <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Mileage</p>
                    <p class="text-[11px] font-bold">{{ number_format((int) ($vehicle->mileage ?? 0)) }} mi</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary">local_gas_station</span>
                  <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Fuel Type</p>
                    <p class="text-[11px] font-bold">{{ $vehicle->fuelTypeOption?->value ?? 'N/A' }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary">settings</span>
                  <div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Engine</p>
                    <p class="text-[11px] font-bold">{{ $vehicle->engine_size ?? 'N/A' }}</p>
                  </div>
                </div>
              </div>

              <div class="flex flex-wrap gap-3 mt-6">
                <a href="{{ $vehicleUrl }}" class="border border-white/20 hover:bg-white/5 px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-primary">play_circle</span> Details</a>
                <form method="post" action="{{ route('compare.add', ['vehicle' => $vehicle->id]) }}">@csrf<button class="border border-white/20 hover:bg-white/5 px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-2" type="submit"><span class="material-symbols-outlined text-[14px] text-primary">compare_arrows</span> Add to Compare</button></form>
                <button class="js-share-listing border border-white/20 hover:bg-white/5 px-4 py-2 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-2" type="button" data-share-url="{{ $vehicleUrl }}" data-share-title="{{ $vehicle->title }}"><span class="material-symbols-outlined text-[14px] text-primary">share</span> Share This</button>
              </div>
            </div>
          </article>
        @empty
          <div class="text-center py-12 text-slate-400">No vehicles found.</div>
        @endforelse

        @if($vehicles->hasPages())
          <div class="pt-8">{{ $vehicles->links() }}</div>
        @endif
      </div>

      <aside class="inventory-filter-sidebar hidden w-full space-y-4 lg:block lg:w-[280px] lg:shrink-0 lg:py-[65px]">
        <h2 class="mb-4 text-xl font-bold font-headline uppercase">{{ __('Search options') }}</h2>
        <form id="inventory-filter-form" method="get" action="{{ route('inventory.index') }}" class="space-y-2">
          <div class="relative">
            <select name="condition_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Condition</option>@foreach($conditions as $row)<option value="{{ $row->id }}" @selected((int) ($filters['condition_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          @if ($originTypes->isNotEmpty())
          <div class="relative">
            <select name="type_listing_option_id" data-nigerian-id="{{ (int) ($nigerianTypeId ?? 0) }}" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">{{ __('Type') }}</option>@foreach($originTypes as $row)<option value="{{ $row->id }}" @selected((int) ($filters['type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          @endif
          <div class="relative">
            <select name="body_type_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Body</option>@foreach(($filterOptions['body_types'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['body_type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          <div class="relative">
            <select name="make_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Make</option>@foreach(($filterOptions['makes'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['make_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          <div class="relative">
            <select name="model_listing_option_id" data-initial-model="{{ (int) ($filters['model_listing_option_id'] ?? 0) }}" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Model</option></select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          <div class="relative">
            <select name="transmission_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Transmission</option>@foreach(($filterOptions['transmissions'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['transmission_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          <div class="relative">
            <select name="fuel_type_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Fuel Type</option>@foreach(($filterOptions['fuel_types'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['fuel_type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          <div class="relative">
            <select name="drive_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Drive</option>@foreach(($filterOptions['drives'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['drive_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          <div class="relative">
            <input name="street_q" value="{{ $filters['street_q'] ?? '' }}" class="inventory-filter-input w-full rounded-sm px-10 py-3 text-[11px] font-bold uppercase" placeholder="{{ __('Street address') }}" type="text" autocomplete="off" />
            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-white/50">location_on</span>
          </div>
          @if ($countries->isNotEmpty())
          <div class="relative" data-country-wrap>
            <select name="country_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Country</option>@foreach($countries as $row)<option value="{{ $row->id }}" @selected((int) ($filters['country_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          @endif
          @if ($extColors->isNotEmpty())
          <div class="relative">
            <select name="exterior_color" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase transition-colors"><option value="">Exterior</option>@foreach($extColors as $ec)<option value="{{ $ec }}" @selected(($filters['exterior_color'] ?? '') === $ec)>{{ $ec }}</option>@endforeach</select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span>
          </div>
          @endif
          <div class="grid grid-cols-2 gap-2">
            <input name="year_min" value="{{ $filters['year_min'] ?? '' }}" type="number" placeholder="{{ __('Year min') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" />
            <input name="year_max" value="{{ $filters['year_max'] ?? '' }}" type="number" placeholder="{{ __('Year max') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" />
          </div>
          <div class="grid grid-cols-2 gap-2">
            <input name="mileage_min" value="{{ $filters['mileage_min'] ?? '' }}" type="number" placeholder="{{ __('Mileage min') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" />
            <input name="mileage_max" value="{{ $filters['mileage_max'] ?? '' }}" type="number" placeholder="{{ __('Mileage max') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" />
          </div>
          <div class="grid grid-cols-2 gap-2">
            <input name="price_min" value="{{ $filters['price_min'] ?? '' }}" type="number" placeholder="{{ __('Price min') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" />
            <input name="price_max" value="{{ $filters['price_max'] ?? '' }}" type="number" placeholder="{{ __('Price max') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" />
          </div>
          <button class="w-full bg-brand_blue hover:bg-brand_blue/90 text-white font-bold py-3 uppercase text-[11px] tracking-widest flex items-center justify-center gap-2 mt-4" type="submit"><span class="material-symbols-outlined text-[16px]">search</span> Apply Filters</button>
          <a href="{{ route('inventory.index') }}" class="w-full bg-brand_blue hover:bg-brand_blue/90 text-white font-bold py-3 uppercase text-[11px] tracking-widest flex items-center justify-center gap-2 mt-2"><span class="material-symbols-outlined text-[16px]">restart_alt</span> Reset All</a>
        </form>
      </aside>
    </div>
  </div>

  <button id="mobile-filter-trigger" type="button" class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-brand_blue text-white px-6 py-4 font-bold uppercase tracking-widest text-xs shadow-[0_-6px_20px_rgba(0,0,0,0.3)]">
    Filters @if($activeFilterCount > 0) ({{ $activeFilterCount }}) @endif
  </button>
  <div id="mobile-filter-modal" class="lg:hidden fixed inset-0 z-50 hidden bg-black/70 p-4">
    <div class="mx-auto mt-4 flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-lg bg-[#111316] text-white">
      <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
        <h3 class="text-sm font-semibold uppercase tracking-widest">Search Options</h3>
        <button id="mobile-filter-close" type="button" class="text-white/70 hover:text-white">✕</button>
      </div>
      <div class="hide-scrollbar overflow-y-auto p-4">
        <form id="inventory-filter-form-mobile" method="get" action="{{ route('inventory.index') }}" class="space-y-2">
          <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}" />
          <div class="relative">
            <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-sm text-white/50">search</span>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search') }}" autocomplete="off" class="inventory-filter-input w-full rounded-sm py-2.5 pl-9 pr-3 text-xs font-semibold uppercase" />
          </div>
          <div class="relative"><select name="condition_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Condition</option>@foreach($conditions as $row)<option value="{{ $row->id }}" @selected((int) ($filters['condition_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          @if ($originTypes->isNotEmpty())
          <div class="relative"><select name="type_listing_option_id" data-nigerian-id="{{ (int) ($nigerianTypeId ?? 0) }}" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">{{ __('Type') }}</option>@foreach($originTypes as $row)<option value="{{ $row->id }}" @selected((int) ($filters['type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          @endif
          <div class="relative"><select name="body_type_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Body</option>@foreach(($filterOptions['body_types'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['body_type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          <div class="relative"><select name="make_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Make</option>@foreach(($filterOptions['makes'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['make_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          <div class="relative"><select name="model_listing_option_id" data-initial-model="{{ (int) ($filters['model_listing_option_id'] ?? 0) }}" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Model</option></select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          <div class="relative"><select name="transmission_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Transmission</option>@foreach(($filterOptions['transmissions'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['transmission_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          <div class="relative"><select name="fuel_type_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Fuel Type</option>@foreach(($filterOptions['fuel_types'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['fuel_type_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          <div class="relative"><select name="drive_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Drive</option>@foreach(($filterOptions['drives'] ?? collect()) as $row)<option value="{{ $row->id }}" @selected((int) ($filters['drive_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          <div class="relative">
            <input name="street_q" value="{{ $filters['street_q'] ?? '' }}" class="inventory-filter-input w-full rounded-sm px-10 py-3 text-[11px] font-bold uppercase" placeholder="{{ __('Street address') }}" type="text" autocomplete="off" />
            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-white/50">location_on</span>
          </div>
          @if ($countries->isNotEmpty())
          <div class="relative" data-country-wrap><select name="country_listing_option_id" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Country</option>@foreach($countries as $row)<option value="{{ $row->id }}" @selected((int) ($filters['country_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          @endif
          @if ($extColors->isNotEmpty())
          <div class="relative"><select name="exterior_color" class="inventory-filter-select w-full appearance-none rounded-sm px-4 py-3 text-[11px] font-bold uppercase"><option value="">Exterior</option>@foreach($extColors as $ec)<option value="{{ $ec }}" @selected(($filters['exterior_color'] ?? '') === $ec)>{{ $ec }}</option>@endforeach</select><span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-white/60">expand_more</span></div>
          @endif
          <div class="grid grid-cols-2 gap-2"><input name="year_min" value="{{ $filters['year_min'] ?? '' }}" type="number" placeholder="{{ __('Year min') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" /><input name="year_max" value="{{ $filters['year_max'] ?? '' }}" type="number" placeholder="{{ __('Year max') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" /></div>
          <div class="grid grid-cols-2 gap-2"><input name="mileage_min" value="{{ $filters['mileage_min'] ?? '' }}" type="number" placeholder="{{ __('Mileage min') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" /><input name="mileage_max" value="{{ $filters['mileage_max'] ?? '' }}" type="number" placeholder="{{ __('Mileage max') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" /></div>
          <div class="grid grid-cols-2 gap-2"><input name="price_min" value="{{ $filters['price_min'] ?? '' }}" type="number" placeholder="{{ __('Price min') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" /><input name="price_max" value="{{ $filters['price_max'] ?? '' }}" type="number" placeholder="{{ __('Price max') }}" class="inventory-filter-input w-full rounded-sm px-3 py-2 text-[11px] font-bold uppercase" /></div>
          <div class="sticky bottom-0 bg-[#111316] pt-4 pb-2 space-y-2">
            <button class="w-full bg-brand_blue hover:bg-brand_blue/90 text-white font-bold py-3 uppercase text-[11px] tracking-widest" type="submit">Apply Filters</button>
            <a href="{{ route('inventory.index') }}" class="w-full block text-center bg-brand_blue hover:bg-brand_blue/90 text-white font-bold py-3 uppercase text-[11px] tracking-widest">Reset All</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script type="application/json" id="inventoryModelMatrixJson">@json($modelMatrix->values()->all())</script>
  <script>
    (() => {
      const sort = document.getElementById('inventory-sort');
      const form = document.getElementById('inventory-filter-form');
      if (sort && form) {
        sort.addEventListener('change', () => form.submit());
      }

      const mobileTrigger = document.getElementById('mobile-filter-trigger');
      const mobileModal = document.getElementById('mobile-filter-modal');
      const mobileClose = document.getElementById('mobile-filter-close');
      if (mobileTrigger && mobileModal) {
        mobileTrigger.addEventListener('click', () => mobileModal.classList.remove('hidden'));
        mobileClose?.addEventListener('click', () => mobileModal.classList.add('hidden'));
        mobileModal.addEventListener('click', (event) => {
          if (event.target === mobileModal) mobileModal.classList.add('hidden');
        });
      }

      const matrixEl = document.getElementById('inventoryModelMatrixJson');
      const matrix = matrixEl ? JSON.parse(matrixEl.textContent || '[]') : [];
      function bindInventoryMakeModel(form) {
        if (!matrix.length || !form) return;
        const makeEl = form.querySelector('select[name="make_listing_option_id"]');
        const modelEl = form.querySelector('select[name="model_listing_option_id"]');
        if (!makeEl || !modelEl) return;
        const initialMake = makeEl.value || '';
        const initialModel = String(modelEl.dataset.initialModel || '');
        function rebuild() {
          const mk = makeEl.value || '';
          modelEl.innerHTML = '<option value="">Model</option>';
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
      bindInventoryMakeModel(document.getElementById('inventory-filter-form'));
      bindInventoryMakeModel(document.getElementById('inventory-filter-form-mobile'));

      function bindInventoryTypeCountry(form) {
        if (!form) return;
        const typeEl = form.querySelector('select[name="type_listing_option_id"]');
        const countryWrap = form.querySelector('[data-country-wrap]');
        const countryEl = form.querySelector('select[name="country_listing_option_id"]');
        if (!typeEl || !countryWrap || !countryEl) return;
        const nigerianId = String(typeEl.getAttribute('data-nigerian-id') || '');
        function sync() {
          const isNigerian = nigerianId && String(typeEl.value || '') === nigerianId;
          countryWrap.classList.toggle('hidden', !!isNigerian);
          if (isNigerian) {
            countryEl.value = '';
          }
        }
        typeEl.addEventListener('change', sync);
        sync();
      }

      bindInventoryTypeCountry(document.getElementById('inventory-filter-form'));
      bindInventoryTypeCountry(document.getElementById('inventory-filter-form-mobile'));

      document.querySelectorAll('.js-share-listing').forEach((button) => {
        button.addEventListener('click', async () => {
          const shareUrl = button.getAttribute('data-share-url');
          const title = button.getAttribute('data-share-title') || 'Vehicle listing';
          if (!shareUrl) return;
          try {
            if (navigator.share) {
              await navigator.share({ title, url: shareUrl });
              return;
            }
            await navigator.clipboard.writeText(shareUrl);
            const original = button.innerHTML;
            button.innerHTML = '<span class="material-symbols-outlined text-[14px] text-primary">check</span> Copied';
            setTimeout(() => {
              button.innerHTML = original;
            }, 1400);
          } catch (_) {
            // Ignore user cancellation or clipboard restrictions.
          }
        });
      });
    })();
  </script>
@endsection
@php
  $makeRows = $makeRows ?? collect();
  $vehicle = $vehicle ?? null;
  $opts = $listingOptions ?? [];
  $matrix = collect($opts['model_matrix'] ?? []);
  $useMakePicker = $makeRows->isNotEmpty();
  $cascadeModels = $useMakePicker && $matrix->isNotEmpty();
  $listingModelsUrlTemplate = rtrim(url('/'), '/').'/dashboard/listing-models/__MAKE_ID__';
@endphp

@if ($useMakePicker && $matrix->isEmpty())
  <span id="listing-catalog-submit-guard" class="sr-only">{{ __('Catalog make/model linkage incomplete') }}</span>
  <p class="text-sm text-amber-800 rounded-md border border-amber-200 bg-amber-50 p-3">
    {{ __('Models are not linked to makes in the catalog yet. Add model rows under each make in Admin → Listing options. Submit is disabled until models are linked under each make.') }}
  </p>
  @push('body-end')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var g = document.getElementById('listing-catalog-submit-guard');
        if (!g) return;
        var form = g.closest('form');
        if (!form) return;
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
          btn.setAttribute('disabled', 'disabled');
          btn.setAttribute('aria-disabled', 'true');
        });
      });
    </script>
  @endpush
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  @if ($cascadeModels)
    <div>
      <x-input-label for="vehicle_make_catalog" :value="__('Make')" />
      <select id="vehicle_make_catalog" name="make_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($makeRows as $row)
          <option
            value="{{ $row->id }}"
            @selected((int) old('make_listing_option_id', $vehicle->make_listing_option_id ?? 0) === (int) $row->id)
          >{{ $row->value }}</option>
        @endforeach
      </select>
      <x-input-error :messages="$errors->get('make_listing_option_id')" class="mt-2" />
    </div>
    <div>
      <x-input-label for="vehicle_model_catalog" :value="__('Model')" />
      <select id="vehicle_model_catalog" name="model_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
      </select>
      <x-input-error :messages="$errors->get('model_listing_option_id')" class="mt-2" />
    </div>
  @else
    <p class="text-sm text-amber-800 sm:col-span-2">{{ __('No make options are configured. Add makes in Admin → Listing options before publishing listings.') }}</p>
  @endif
</div>

@php
  $conditions = collect($opts['conditions'] ?? []);
  $fuelTypes = collect($opts['fuel_types'] ?? []);
  $transmissions = collect($opts['transmissions'] ?? []);
  $bodyTypes = collect($opts['body_types'] ?? []);
  $drives = collect($opts['drives'] ?? []);
  $countries = collect($opts['countries'] ?? []);
  $exteriorColors = collect($opts['exterior_colors'] ?? []);
  $originTypes = collect($opts['vehicle_origin_types'] ?? []);
  $nigeriaCountryId = \App\Support\VehicleListingCatalog::nigeriaCountryListingOptionId();
  $nigerianTypeId = \App\Support\VehicleListingCatalog::vehicleOriginTypeIdByLabel('Nigerian');
  $foreignTypeId = \App\Support\VehicleListingCatalog::vehicleOriginTypeIdByLabel('Foreign');
  $defaultTypeId = (int) ($nigerianTypeId ?? $foreignTypeId ?? 0);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div>
    <x-input-label for="condition_listing_option_id" :value="__('Condition')" />
    @if ($conditions->isNotEmpty())
      <select id="condition_listing_option_id" name="condition_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($conditions as $row)
          <option value="{{ $row->id }}" @selected((int) old('condition_listing_option_id', $vehicle->condition_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
    @else
      <p class="text-sm text-amber-800">{{ __('Add condition options in Admin → Listing options.') }}</p>
    @endif
    <x-input-error :messages="$errors->get('condition_listing_option_id')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="street_address" :value="__('Street address')" />
    <x-text-input id="street_address" name="street_address" type="text" class="mt-1 block w-full" placeholder="{{ __('Street, city, region') }}" value="{{ old('street_address', $vehicle->street_address ?? '') }}" />
    <x-input-error :messages="$errors->get('street_address')" class="mt-2" />
  </div>
</div>

@if ($originTypes->isNotEmpty())
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <x-input-label for="vehicle_type_listing_option_id">
        {{ __('Type') }}<span class="text-red-600" aria-hidden="true">*</span>
      </x-input-label>
      <select
        id="vehicle_type_listing_option_id"
        name="type_listing_option_id"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        data-nigerian-id="{{ (int) ($nigerianTypeId ?? 0) }}"
        data-foreign-id="{{ (int) ($foreignTypeId ?? 0) }}"
        data-nigeria-country-id="{{ (int) ($nigeriaCountryId ?? 0) }}"
      >
        <option value="">—</option>
        @foreach ($originTypes as $row)
          <option value="{{ $row->id }}" @selected((int) old('type_listing_option_id', $vehicle->type_listing_option_id ?? $defaultTypeId) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
      <x-input-error :messages="$errors->get('type_listing_option_id')" class="mt-2" />
    </div>
    <div>
      <x-input-label for="country_listing_option_id">
        {{ __('Country') }}<span class="text-red-600" aria-hidden="true">*</span>
      </x-input-label>
      @if ($countries->isNotEmpty())
        <select id="country_listing_option_id" name="country_listing_option_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option value="">—</option>
          @foreach ($countries as $row)
            <option value="{{ $row->id }}" @selected((int) old('country_listing_option_id', $vehicle->country_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
          @endforeach
        </select>
      @else
        <p class="text-sm text-red-700 font-medium">{{ __('No countries are configured. Listings cannot be saved until an administrator adds country options.') }}</p>
      @endif
      <x-input-error :messages="$errors->get('country_listing_option_id')" class="mt-2" />
    </div>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
      <x-input-label for="map_location" :value="__('Map location (pin / map query only)')" />
      <x-text-input id="map_location" name="map_location" type="text" class="mt-1 block w-full" value="{{ old('map_location', $vehicle->map_location ?? '') }}" />
      <x-input-error :messages="$errors->get('map_location')" class="mt-2" />
    </div>
  </div>
  @push('body-end')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var typeEl = document.getElementById('vehicle_type_listing_option_id');
        var countryEl = document.getElementById('country_listing_option_id');
        if (!typeEl || !countryEl) return;
        var form = typeEl.closest('form');
        var nigerian = String(typeEl.getAttribute('data-nigerian-id') || '');
        var nigeria = String(typeEl.getAttribute('data-nigeria-country-id') || '');
        function sync() {
          if (nigerian && nigeria && typeEl.value === nigerian) {
            countryEl.value = nigeria;
            countryEl.setAttribute('disabled', 'disabled');
            countryEl.setAttribute('aria-disabled', 'true');
            countryEl.classList.add('bg-slate-100', 'cursor-not-allowed');
          } else {
            countryEl.removeAttribute('disabled');
            countryEl.removeAttribute('aria-disabled');
            countryEl.classList.remove('bg-slate-100', 'cursor-not-allowed');
          }
        }
        typeEl.addEventListener('change', sync);
        sync();
        if (form) {
          form.addEventListener('submit', function () {
            if (countryEl.hasAttribute('disabled')) countryEl.removeAttribute('disabled');
          });
        }
      });
    </script>
  @endpush
@else
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div>
    <x-input-label for="country_listing_option_id">
      {{ __('Country') }}<span class="text-red-600" aria-hidden="true">*</span>
    </x-input-label>
    @if ($countries->isNotEmpty())
      <select id="country_listing_option_id" name="country_listing_option_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($countries as $row)
          <option value="{{ $row->id }}" @selected((int) old('country_listing_option_id', $vehicle->country_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
    @else
      <p class="text-sm text-red-700 font-medium">{{ __('No countries are configured. Listings cannot be saved until an administrator adds country options.') }}</p>
    @endif
    <x-input-error :messages="$errors->get('country_listing_option_id')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="map_location" :value="__('Map location (pin / map query only)')" />
    <x-text-input id="map_location" name="map_location" type="text" class="mt-1 block w-full" value="{{ old('map_location', $vehicle->map_location ?? '') }}" />
    <x-input-error :messages="$errors->get('map_location')" class="mt-2" />
  </div>
</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
  <div>
    <x-input-label for="mileage" :value="__('Mileage')" />
    <x-text-input id="mileage" name="mileage" type="number" class="mt-1 block w-full" value="{{ old('mileage', $vehicle->mileage ?? '') }}" />
    <x-input-error :messages="$errors->get('mileage')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="fuel_type_listing_option_id" :value="__('Fuel type')" />
    @if ($fuelTypes->isNotEmpty())
      <select id="fuel_type_listing_option_id" name="fuel_type_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($fuelTypes as $row)
          <option value="{{ $row->id }}" @selected((int) old('fuel_type_listing_option_id', $vehicle->fuel_type_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
    @else
      <p class="text-sm text-amber-800">{{ __('Add fuel type options in Admin → Listing options.') }}</p>
    @endif
    <x-input-error :messages="$errors->get('fuel_type_listing_option_id')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="transmission_listing_option_id" :value="__('Transmission')" />
    @if ($transmissions->isNotEmpty())
      <select id="transmission_listing_option_id" name="transmission_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($transmissions as $row)
          <option value="{{ $row->id }}" @selected((int) old('transmission_listing_option_id', $vehicle->transmission_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
    @else
      <p class="text-sm text-amber-800">{{ __('Add transmission options in Admin → Listing options.') }}</p>
    @endif
    <x-input-error :messages="$errors->get('transmission_listing_option_id')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="body_type_listing_option_id" :value="__('Body type')" />
    @if ($bodyTypes->isNotEmpty())
      <select id="body_type_listing_option_id" name="body_type_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($bodyTypes as $row)
          <option value="{{ $row->id }}" @selected((int) old('body_type_listing_option_id', $vehicle->body_type_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
    @else
      <p class="text-sm text-amber-800">{{ __('Add body type options in Admin → Listing options.') }}</p>
    @endif
    <x-input-error :messages="$errors->get('body_type_listing_option_id')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="drive_listing_option_id" :value="__('Drive')" />
    @if ($drives->isNotEmpty())
      <select id="drive_listing_option_id" name="drive_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($drives as $row)
          <option value="{{ $row->id }}" @selected((int) old('drive_listing_option_id', $vehicle->drive_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
        @endforeach
      </select>
    @else
      <p class="text-sm text-amber-800">{{ __('Add drive options in Admin → Listing options.') }}</p>
    @endif
    <x-input-error :messages="$errors->get('drive_listing_option_id')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="vin" :value="__('VIN')" />
    <x-text-input id="vin" name="vin" type="text" class="mt-1 block w-full" value="{{ old('vin', $vehicle->vin ?? '') }}" />
    <x-input-error :messages="$errors->get('vin')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="exterior_color" :value="__('Exterior color')" />
    @if ($exteriorColors->isNotEmpty())
      <select id="exterior_color" name="exterior_color" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">—</option>
        @foreach ($exteriorColors as $v)
          <option value="{{ $v }}" @selected(old('exterior_color', $vehicle->exterior_color ?? '') === $v)>{{ $v }}</option>
        @endforeach
      </select>
    @else
      <x-text-input id="exterior_color" name="exterior_color" type="text" class="mt-1 block w-full" value="{{ old('exterior_color', $vehicle->exterior_color ?? '') }}" />
    @endif
    <x-input-error :messages="$errors->get('exterior_color')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="interior_color" :value="__('Interior color')" />
    <x-text-input id="interior_color" name="interior_color" type="text" class="mt-1 block w-full" value="{{ old('interior_color', $vehicle->interior_color ?? '') }}" />
    <x-input-error :messages="$errors->get('interior_color')" class="mt-2" />
  </div>
  <div>
    <x-input-label for="engine_size" :value="__('Engine size')" />
    <x-text-input id="engine_size" name="engine_size" type="text" class="mt-1 block w-full" placeholder="{{ __('e.g. 2.0L turbo') }}" value="{{ old('engine_size', $vehicle->engine_size ?? '') }}" />
    <x-input-error :messages="$errors->get('engine_size')" class="mt-2" />
  </div>
</div>

@if ($cascadeModels)
  @push('body-end')
    <script type="application/json" id="listingModelsUrlTemplateJson">@json($listingModelsUrlTemplate)</script>
    <script type="application/json" id="listingInitialModelIdJson">@json((int) old('model_listing_option_id', $vehicle->model_listing_option_id ?? 0))</script>
    <script>
      (() => {
        const templateEl = document.getElementById('listingModelsUrlTemplateJson');
        const template = templateEl ? JSON.parse(templateEl.textContent || '""') : '';
        const makeEl = document.getElementById('vehicle_make_catalog');
        const modelEl = document.getElementById('vehicle_model_catalog');
        if (!makeEl || !modelEl) return;
        const initialModelEl = document.getElementById('listingInitialModelIdJson');
        const initialModelId = initialModelEl ? (parseInt(JSON.parse(initialModelEl.textContent || '0'), 10) || 0) : 0;

        async function loadModels() {
          const makeId = makeEl.value || '';
          modelEl.innerHTML = '<option value="">—</option>';
          if (!makeId) return;
          try {
            const res = await fetch(template.replace('__MAKE_ID__', String(makeId)), {
              headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            (data.models || []).forEach((row) => {
              const o = document.createElement('option');
              o.value = String(row.id);
              o.textContent = row.value;
              if (parseInt(row.id, 10) === initialModelId) o.selected = true;
              modelEl.appendChild(o);
            });
          } catch (_) {}
        }

        makeEl.addEventListener('change', () => {
          loadModels();
        });
        loadModels();
      })();
    </script>
  @endpush
@endif

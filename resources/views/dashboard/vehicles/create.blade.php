<x-app-layout>
  @component('dashboard.vehicles.partials.luxe-form-shell', [
    'title' => __('Add New Product'),
    'formAction' => route('dashboard.vehicles.store'),
    'cancelUrl' => route('dashboard.vehicles.index'),
    'submitLabel' => __('Create'),
    'formMethod' => 'post',
  ])
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
      <div class="space-y-10">
        <section class="space-y-6">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase">{{ __('Core Identity') }}</h3>
          <div>
            <x-input-label for="title" :value="__('Product Name')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            <div>
              <x-input-label for="price" :value="__('Price')" />
              <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" value="{{ old('price') }}" />
              <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="stock" :value="__('Stock')" />
              <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" value="{{ old('stock', 0) }}" min="0" />
              <x-input-error :messages="$errors->get('stock')" class="mt-2" />
            </div>
          </div>
          <div>
            <x-input-label for="product_category_listing_option_id" :value="__('Category')" />
            <select id="product_category_listing_option_id" name="product_category_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">{{ __('— Select a category —') }}</option>
              @foreach (($productCategories ?? collect()) as $row)
                <option value="{{ $row->id }}" @selected((int) old('product_category_listing_option_id') === (int) $row->id)>{{ $row->value }}</option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('product_category_listing_option_id')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="vin" :value="__('SKU')" />
            <x-text-input id="vin" name="vin" type="text" class="mt-1 block w-full" value="{{ old('vin') }}" placeholder="VD-DEMO-001" />
            <p class="mt-1 text-xs text-on-surface-variant">{{ __('Stock-keeping unit. Optional but recommended for inventory tracking.') }}</p>
            <x-input-error :messages="$errors->get('vin')" class="mt-2" />
          </div>
          @include('dashboard.vehicles.partials.variant-dimensions')
        </section>

        <section class="space-y-6">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase">{{ __('The Story') }}</h3>
          <div>
            <x-input-label for="features_text" :value="__('Features (one per line)')" />
            <textarea id="features_text" name="features_text" class="mt-1 block w-full" rows="4" placeholder="Leather seats&#10;Sunroof">{{ old('features_text') }}</textarea>
            <x-input-error :messages="$errors->get('features_text')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="overview" :value="__('Short description')" />
            <textarea id="overview" name="overview" class="mt-1 block w-full" rows="4" placeholder="{{ __('Shown under the product title on the shop page.') }}">{{ old('overview') }}</textarea>
            <x-input-error :messages="$errors->get('overview')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="composition_care" :value="__('Composition & care')" />
            <textarea id="composition_care" name="composition_care" class="mt-1 block w-full" rows="4" placeholder="{{ __('Materials, care instructions…') }}">{{ old('composition_care') }}</textarea>
            <x-input-error :messages="$errors->get('composition_care')" class="mt-2" />
          </div>
          <div>
            <x-input-label for="shipping_returns" :value="__('Shipping & returns')" />
            <textarea id="shipping_returns" name="shipping_returns" class="mt-1 block w-full" rows="4" placeholder="{{ __('Delivery times, return policy…') }}">{{ old('shipping_returns') }}</textarea>
            <x-input-error :messages="$errors->get('shipping_returns')" class="mt-2" />
          </div>
        </section>
      </div>

      <div class="space-y-10">
        @if(auth()->user()?->hasRole('admin'))
          <section class="space-y-4">
            <h3 class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase">{{ __('Visibility') }}</h3>
            <div class="flex items-center justify-between p-4 border border-outline-variant bg-surface-container-lowest gap-4">
              <div>
                <p class="font-body-md font-medium text-primary">{{ __('Featured product') }}</p>
                <p class="text-xs text-on-surface-variant">{{ __('Highlights this product with a featured badge on shop and product pages.') }}</p>
              </div>
              <input id="is_special" name="is_special" type="checkbox" value="1" class="rounded-none border-outline-variant text-primary focus:ring-primary" @checked(old('is_special')) />
            </div>
            <div class="flex items-center justify-between p-4 border border-outline-variant bg-surface-container-lowest gap-4">
              <div>
                <p class="font-body-md font-medium text-primary">{{ __('Approve immediately') }}</p>
                <p class="text-xs text-on-surface-variant">{{ __('Live on public shop when saved.') }}</p>
              </div>
              <input id="approve_listing" name="approve_listing" type="checkbox" value="1" class="rounded-none border-outline-variant text-primary focus:ring-primary" @checked(old('approve_listing')) />
            </div>
          </section>
        @endif

        <section class="space-y-6 border border-outline-variant p-4 md:p-6">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase">{{ __('Product Visuals') }}</h3>
          <p class="text-xs text-on-surface-variant">{{ __('Use one featured image plus gallery images from the media library.') }}</p>

          <div class="space-y-4">
            <div class="border border-dashed border-outline-variant p-4 bg-surface-container-lowest">
              <x-input-label :value="__('Main image')" />
              <input type="hidden" id="main_image_path" name="main_image_path" value="{{ old('main_image_path', '') }}" />
              <div class="mt-3 flex flex-wrap items-center gap-3">
                <button type="button" id="main-image-library" class="admin-luxe-btn-primary !py-2 !px-4 text-xs">{{ __('Media library') }}</button>
                <button type="button" id="main-image-clear" class="border border-outline-variant px-4 py-2 font-label-caps text-[11px] hover:bg-surface-container-high disabled:opacity-50" disabled>{{ __('Clear') }}</button>
              </div>
              <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
              <x-input-error :messages="$errors->get('main_image_path')" class="mt-2" />
              <div id="main-image-preview" class="mt-3 hidden"></div>
            </div>

            <div class="border border-dashed border-outline-variant p-4 bg-surface-container-lowest">
              <x-input-label :value="__('Gallery images')" />
              <div id="gallery-paths-holder"></div>
              <div class="mt-3 flex flex-wrap items-center gap-3">
                <button type="button" id="gallery-library" class="admin-luxe-btn-primary !py-2 !px-4 text-xs">{{ __('Media library') }}</button>
                <button type="button" id="gallery-clear-all" class="border border-outline-variant px-4 py-2 font-label-caps text-[11px] hover:bg-surface-container-high disabled:opacity-50" disabled>{{ __('Clear') }}</button>
              </div>
              <x-input-error :messages="$errors->get('images')" class="mt-2" />
              <x-input-error :messages="$errors->get('gallery_image_paths')" class="mt-2" />
              <div id="gallery-preview" class="mt-3 hidden grid grid-cols-2 gap-3 sm:grid-cols-3"></div>
            </div>
          </div>
        </section>
      </div>
    </div>
  @endcomponent

  @include('dashboard.vehicles.partials.image-manager', ['supportsExistingGalleryDelete' => false])
</x-app-layout>

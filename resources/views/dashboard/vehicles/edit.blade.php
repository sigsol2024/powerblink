@php
  $isAdminEdit = $isAdminEdit ?? false;
@endphp
<x-app-layout>
  @push('head')
    @include('dashboard.vehicles.partials.luxe-form-styles')
  @endpush

  <div class="min-h-full flex flex-col">
    <header class="sticky top-0 z-30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white shrink-0">
      <h2 class="text-lg font-semibold text-wp-text">{{ $isAdminEdit ? __('Edit Product') : __('Edit listing') }}</h2>
      <div class="flex items-center gap-2">
        <a href="{{ route('dashboard.vehicles.index') }}" class="text-sm px-3 py-1.5 border border-wp-border text-wp-text bg-white rounded hover:bg-wp-bg transition-colors">{{ __('Cancel') }}</a>
        <button type="submit" form="luxe-product-form" class="admin-luxe-btn-primary">{{ __('Save Product') }}</button>
      </div>
    </header>

    <div class="max-w-[1100px] mx-auto py-6 md:py-8 px-4 md:px-6 w-full flex-1 space-y-6">

  @if($vehicle->status === 'approved')
  <div class="admin-content-toolbar">
    <div class="admin-content-toolbar__actions">
      <a href="{{ route('inventory.show', ['slug' => $vehicle->slug]) }}" class="admin-btn">{{ __('View public page') }}</a>
    </div>
  </div>
  @endif
  <div class="w-full space-y-6">
      @if($isAdminEdit)
        @php $u = $vehicle->user; @endphp
        <div class="rounded-xl border border-indigo-200 bg-indigo-50/80 p-4 text-sm text-indigo-950 shadow-sm ring-1 ring-indigo-100">
          <div class="font-semibold text-indigo-900">{{ __('Listing owner') }}</div>
          <div class="mt-2 grid gap-2 sm:grid-cols-2">
            <div><span class="text-indigo-700">{{ __('Name') }}:</span> {{ $u?->name ?? '—' }}</div>
            <div><span class="text-indigo-700">{{ __('Email') }}:</span> {{ $u?->email ?? '—' }}</div>
            <div><span class="text-indigo-700">{{ __('Created') }}:</span> {{ $vehicle->created_at?->format('M j, Y g:i a') }}</div>
            <div><span class="text-indigo-700">{{ __('Submitted') }}:</span> {{ $vehicle->submitted_at?->format('M j, Y g:i a') ?? '—' }}</div>
            <div class="sm:col-span-2">
              <span class="text-indigo-700">{{ __('Account type') }}:</span>
              @if($u?->hasRole('admin'))
                {{ __('Staff (admin role)') }}
              @else
                {{ __('Dealer') }}
              @endif
            </div>
          </div>
        </div>
      @endif
      <div class="border border-outline-variant bg-surface-container-lowest">
        <div class="p-6 text-on-background luxe-product-form">
          <form id="luxe-product-form" method="post" action="{{ route('dashboard.vehicles.update', $vehicle) }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
              <x-input-label for="title" value="Title" />
              <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required value="{{ old('title', $vehicle->title) }}" />
              <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <x-input-label for="price" value="Price" />
                <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" value="{{ old('price', $vehicle->price) }}" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="stock" value="Stock" />
                <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" value="{{ old('stock', $vehicle->stock ?? 0) }}" min="0" />
                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
              </div>
            </div>

            <div>
              <x-input-label for="product_category_listing_option_id" value="Category" />
              <select id="product_category_listing_option_id" name="product_category_listing_option_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('— Select a category —') }}</option>
                @foreach (($productCategories ?? collect()) as $row)
                  <option value="{{ $row->id }}" @selected((int) old('product_category_listing_option_id', $vehicle->product_category_listing_option_id ?? 0) === (int) $row->id)>{{ $row->value }}</option>
                @endforeach
              </select>
              <x-input-error :messages="$errors->get('product_category_listing_option_id')" class="mt-2" />
            </div>

            <div>
              <x-input-label for="vin" value="SKU" />
              <x-text-input id="vin" name="vin" type="text" class="mt-1 block w-full" value="{{ old('vin', $vehicle->vin) }}" placeholder="VD-DEMO-001" />
              <p class="mt-1 text-xs text-gray-500">{{ __('Stock-keeping unit. Optional but recommended for inventory tracking.') }}</p>
              <x-input-error :messages="$errors->get('vin')" class="mt-2" />
            </div>

            @include('dashboard.vehicles.partials.variant-dimensions')

            <div>
              <x-input-label for="features_text" value="Features (one per line)" />
              <textarea id="features_text" name="features_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4">{{ old('features_text', is_array($vehicle->features) ? implode("\n", $vehicle->features) : '') }}</textarea>
              <x-input-error :messages="$errors->get('features_text')" class="mt-2" />
            </div>

            <section class="rounded-lg border border-gray-200 p-4 space-y-4">
              <h3 class="text-base font-semibold text-gray-900">Detail page configuration</h3>

              <div>
                <x-input-label for="overview" value="Vehicle overview (long-form text)" />
                <textarea id="overview" name="overview" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="5">{{ old('overview', $vehicle->overview) }}</textarea>
                <x-input-error :messages="$errors->get('overview')" class="mt-2" />
              </div>

            </section>

            @if($isAdminEdit)
              <section class="space-y-4">
                <h3 class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase">{{ __('Visibility') }}</h3>
                <div class="flex items-center justify-between p-4 border border-outline-variant bg-surface-container-lowest gap-4">
                  <div>
                    <p class="font-body-md font-medium text-primary">{{ __('Featured product') }}</p>
                    <p class="text-xs text-on-surface-variant">{{ __('Featured products appear in the homepage “The Bestsellers” section.') }}</p>
                  </div>
                  <input id="is_special" name="is_special" type="checkbox" value="1" class="rounded-none border-outline-variant text-primary focus:ring-primary" @checked(old('is_special', $vehicle->is_special)) />
                </div>
                @if(in_array($vehicle->status, ['pending', 'draft', 'rejected'], true))
                  <div class="flex items-center justify-between p-4 border border-outline-variant bg-surface-container-lowest gap-4">
                    <div>
                      <p class="font-body-md font-medium text-primary">{{ __('Approve immediately') }}</p>
                      <p class="text-xs text-on-surface-variant">{{ __('Live on public shop when saved.') }}</p>
                    </div>
                    <input id="approve_listing" name="approve_listing" type="checkbox" value="1" class="rounded-none border-outline-variant text-primary focus:ring-primary" @checked(old('approve_listing')) />
                  </div>
                @endif
              </section>
            @endif

            <section class="rounded-lg border border-gray-200 p-4">
              <h3 class="text-base font-semibold text-gray-900">Images</h3>
              <p class="mt-1 text-sm text-gray-600">Main and gallery images use the same preview/remove pattern. Click a preview to open larger.</p>

              <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="rounded-md border border-gray-200 p-3">
                  <x-input-label value="Main image" />
                  <input type="hidden" id="main_image_path" name="main_image_path" value="{{ old('main_image_path', '') }}" />
                  <p class="mt-1 text-xs text-gray-500">{{ __('Choose a featured image from the media library (upload new files inside the library).') }}</p>
                  <div class="mt-3 flex flex-wrap items-center gap-3">
                    <button type="button" id="main-image-library" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Media library') }}</button>
                    <button type="button" id="main-image-clear" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" disabled>Clear selection</button>
                  </div>
                  <x-input-error :messages="$errors->get('main_image')" class="mt-2" />
                  <x-input-error :messages="$errors->get('main_image_path')" class="mt-2" />
                  <div id="main-image-preview" class="mt-3 hidden"></div>
                </div>

                <div class="rounded-md border border-gray-200 p-3">
                  <x-input-label value="Gallery images" />
                  <div id="gallery-paths-holder"></div>
                  <p class="mt-1 text-xs text-gray-500">{{ __('Add images from the media library (Ctrl/Cmd-click, Shift-click range).') }}</p>
                  <div class="mt-3 flex flex-wrap items-center gap-3">
                    <button type="button" id="gallery-library" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">{{ __('Media library') }}</button>
                    <button type="button" id="gallery-clear-all" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50" disabled>Clear selection</button>
                  </div>
                  <x-input-error :messages="$errors->get('images')" class="mt-2" />
                  <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
                  <x-input-error :messages="$errors->get('gallery_image_paths')" class="mt-2" />
                  <x-input-error :messages="$errors->get('gallery_image_paths.*')" class="mt-2" />
                  <div id="gallery-preview" class="mt-3 hidden grid grid-cols-2 gap-3 sm:grid-cols-3"></div>
                </div>
              </div>

              <div class="mt-5">
                <h4 class="text-sm font-semibold text-gray-900">Current Gallery</h4>
                <p class="mt-1 text-xs text-gray-500">The first image is the featured image across the public site.</p>
                @if(session('status'))
                  <div class="mt-3 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                  </div>
                @endif

                @if($vehicle->images->isEmpty())
                  <p class="mt-3 text-sm text-gray-500">No images uploaded yet.</p>
                @else
                  <div id="existing-gallery-grid" class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($vehicle->images as $image)
                      <div class="rounded-lg border border-gray-200 p-3" data-image-card data-image-id="{{ $image->id }}">
                        <img
                          src="{{ \App\Support\VehicleImageUrl::url($image->path) }}"
                          alt=""
                          class="h-40 w-full cursor-zoom-in rounded-md object-cover"
                          data-preview-image
                        />
                        <div class="mt-3 flex items-center justify-between gap-3">
                          <span data-image-role class="text-xs font-medium {{ $loop->first ? 'text-indigo-600' : 'text-gray-500' }}">
                            {{ $loop->first ? 'Featured image' : 'Gallery image' }}
                          </span>
                          <button
                            type="button"
                            class="text-sm text-red-700 hover:underline"
                            data-clear-existing-image
                            data-image-id="{{ $image->id }}"
                          >
                            Clear on save
                          </button>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </section>

            <div class="flex items-center gap-3">
              <x-primary-button>Save</x-primary-button>
              <a href="{{ route('dashboard.vehicles.index') }}" class="text-sm text-gray-600 hover:underline">Back</a>
            </div>
          </form>
        </div>
      </div>

      @unless($isAdminEdit)
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h3 class="font-semibold">Submit for approval</h3>
              <p class="text-sm text-gray-600 mt-1">
                Status: <span class="font-medium">{{ strtoupper($vehicle->status) }}</span>
              </p>
              @if($vehicle->status === 'rejected' && $vehicle->rejection_reason)
                <p class="text-sm text-red-600 mt-2">Reason: {{ $vehicle->rejection_reason }}</p>
              @endif
            </div>
            <form method="post" action="{{ route('dashboard.vehicles.submit', $vehicle) }}">
              @csrf
              <x-primary-button>Submit</x-primary-button>
            </form>
          </div>
        </div>
      </div>
      @else
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h3 class="font-semibold">Listing status</h3>
          <p class="text-sm text-gray-600 mt-1">
            Status: <span class="font-medium">{{ strtoupper($vehicle->status) }}</span>
          </p>
          @if($vehicle->status === 'rejected' && $vehicle->rejection_reason)
            <p class="text-sm text-red-600 mt-2">Reason: {{ $vehicle->rejection_reason }}</p>
          @endif
        </div>
      </div>
      @endunless

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
          <h3 class="font-semibold text-red-700">Delete listing</h3>
          <p class="mt-1 text-sm text-gray-600">This permanently removes the listing and any linked images.</p>
          <form method="post" action="{{ route('dashboard.vehicles.destroy', $vehicle) }}" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">
              Delete listing
            </button>
          </form>
        </div>
      </div>
      @include('dashboard.vehicles.partials.image-manager', ['supportsExistingGalleryDelete' => true])
      </div>
    @include('admin.partials.luxe-footer', ['footerClass' => 'mt-8'])
    </div>
  </div>
</x-app-layout>


<x-app-layout>
  <x-slot name="header">
    <div class="admin-page-header flex flex-col gap-2 sm:gap-3">
      <h2 class="admin-page-title">{{ __('New Vehicle Listing') }}</h2>
    </div>
  </x-slot>

  <div class="w-full">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 text-gray-900 sm:p-6">
          <form method="post" action="{{ route('dashboard.vehicles.store') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf

            <div>
              <x-input-label for="title" value="Title" />
              <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
              <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <x-input-label for="year" value="Year" />
                <x-text-input id="year" name="year" type="number" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('year')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="price" value="Price" />
                <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
              </div>
            </div>

            @include('dashboard.vehicles.partials.listing-catalog-fields', [
              'listingOptions' => $listingOptions ?? [],
              'makeRows' => $makeRows ?? collect(),
              'vehicle' => null,
            ])

            <div>
              <x-input-label for="features_text" value="Features (one per line)" />
              <textarea id="features_text" name="features_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4" placeholder="Leather seats&#10;Sunroof">{{ old('features_text') }}</textarea>
              <x-input-error :messages="$errors->get('features_text')" class="mt-2" />
            </div>

            <section class="rounded-lg border border-gray-200 p-4 space-y-4">
              <h3 class="text-base font-semibold text-gray-900">Detail page configuration</h3>

              <div>
                <x-input-label for="overview" value="Vehicle overview (long-form text)" />
                <textarea id="overview" name="overview" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="5">{{ old('overview') }}</textarea>
                <x-input-error :messages="$errors->get('overview')" class="mt-2" />
              </div>

            </section>

            @if(auth()->user()?->hasRole('admin'))
              <div class="flex items-center gap-2 rounded-md border border-amber-200 bg-amber-50/80 px-3 py-2">
                <input id="is_special" name="is_special" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_special')) />
                <x-input-label for="is_special" value="{{ __('Special listing (shows “Special” ribbon on homepage cards)') }}" class="!mb-0" />
              </div>
            @endif

            @if(auth()->user()?->hasRole('admin'))
              <div class="flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50/80 px-3 py-2">
                <input id="approve_listing" name="approve_listing" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('approve_listing')) />
                <x-input-label for="approve_listing" value="{{ __('Approve immediately (live on public inventory)') }}" class="!mb-0" />
              </div>
            @endif

            <section class="rounded-lg border border-gray-200 p-4">
              <h3 class="text-base font-semibold text-gray-900">Images</h3>
              <p class="mt-1 text-sm text-gray-600">Use one featured image plus gallery images. Click any preview to open it larger.</p>

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
            </section>

            <div class="flex items-center gap-3">
              <x-primary-button>Create</x-primary-button>
              <a href="{{ route('dashboard.vehicles.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    @include('dashboard.vehicles.partials.image-manager', ['supportsExistingGalleryDelete' => false])
  </div>
</x-app-layout>


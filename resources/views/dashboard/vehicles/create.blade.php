<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('New Vehicle Listing') }}
    </h2>
  </x-slot>

  <div class="w-full">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
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

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <x-input-label for="video_url" value="Video URL (optional)" />
                  <x-text-input id="video_url" name="video_url" type="url" class="mt-1 block w-full" value="{{ old('video_url') }}" />
                  <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                </div>
              </div>

              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <x-input-label for="city_mpg" value="City MPG" />
                  <x-text-input id="city_mpg" name="city_mpg" type="number" class="mt-1 block w-full" value="{{ old('city_mpg') }}" />
                </div>
                <div>
                  <x-input-label for="hwy_mpg" value="Highway MPG" />
                  <x-text-input id="hwy_mpg" name="hwy_mpg" type="number" class="mt-1 block w-full" value="{{ old('hwy_mpg') }}" />
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <x-input-label for="contact_phone" value="Contact phone (listing detail)" />
                  <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full" value="{{ old('contact_phone') }}" />
                </div>
                <div>
                  <x-input-label for="contact_email" value="Contact email (listing detail)" />
                  <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" value="{{ old('contact_email') }}" />
                </div>
                <div class="sm:col-span-2">
                  <x-input-label for="map_location" value="Map location (pin / map query only)" />
                  <x-text-input id="map_location" name="map_location" type="text" class="mt-1 block w-full" value="{{ old('map_location') }}" />
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                  <x-input-label for="engine_layout" value="Tech spec: engine layout" />
                  <x-text-input id="engine_layout" name="engine_layout" type="text" class="mt-1 block w-full" value="{{ old('engine_layout') }}" />
                </div>
                <div>
                  <x-input-label for="top_track_speed" value="Tech spec: top track speed" />
                  <x-text-input id="top_track_speed" name="top_track_speed" type="text" class="mt-1 block w-full" value="{{ old('top_track_speed') }}" />
                </div>
                <div>
                  <x-input-label for="zero_to_sixty" value="Tech spec: 0-70 / 0-60 time" />
                  <x-text-input id="zero_to_sixty" name="zero_to_sixty" type="text" class="mt-1 block w-full" value="{{ old('zero_to_sixty') }}" />
                </div>
                <div>
                  <x-input-label for="number_of_gears" value="Tech spec: number of gears" />
                  <x-text-input id="number_of_gears" name="number_of_gears" type="text" class="mt-1 block w-full" value="{{ old('number_of_gears') }}" />
                </div>
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


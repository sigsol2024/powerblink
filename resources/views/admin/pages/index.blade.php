<x-app-layout>
  <header class="px-margin-mobile md:px-gutter py-6 md:py-8 border-b border-outline-variant shrink-0">
    <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary tracking-tight">{{ __('Page editors') }}</h2>
  </header>

  <div class="w-full px-margin-mobile md:px-gutter py-8 max-w-max-container mx-auto">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <p class="text-sm text-gray-600 mb-6">
          Edit the public pages one by one. This mirrors the page-editor pattern from your reference platform.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach ($pages as $slug => $info)
            @php
              $entry = $existing[$slug] ?? null;
              $isSaved = $entry !== null;
              $isActive = $entry?->is_active ?? true;
            @endphp
            <a href="{{ route('admin.pages.edit', ['slug' => $slug]) }}" class="block border rounded-lg p-4 hover:shadow transition">
              <div class="flex items-center justify-between gap-2">
                <h3 class="font-semibold text-gray-900">{{ $info['label'] }}</h3>
                <div class="flex items-center gap-2">
                  <span class="text-xs px-2 py-1 rounded {{ $isSaved ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $isSaved ? 'Saved' : 'Default' }}
                  </span>
                  <span class="text-xs px-2 py-1 rounded {{ $isActive ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $isActive ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
              <p class="mt-2 text-sm text-gray-600">{{ $info['default_description'] }}</p>
              <p class="mt-3 text-indigo-600 text-sm font-medium">Open editor →</p>
            </a>
          @endforeach
        </div>
      </div>
  </div>
  @include('admin.partials.luxe-footer', ['footerClass' => 'mt-8'])
</x-app-layout>


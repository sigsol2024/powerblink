<x-app-layout>
  <x-admin.page-header :subtitle="__('Edit public pages one by one.')" />

  <x-admin.page-content>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      @foreach ($pages as $slug => $info)
        @php
          $entry = $existing[$slug] ?? null;
          $isSaved = $entry !== null;
          $isActive = $entry?->is_active ?? true;
        @endphp
        <a href="{{ route('admin.pages.edit', ['slug' => $slug]) }}" class="block group">
          <x-admin.card class="h-full transition-transform hover:scale-[1.01] card-hover">
            <div class="flex items-start justify-between gap-2 mb-3">
              <h3 class="font-headline-md text-headline-md text-primary group-hover:text-secondary transition-colors">{{ $info['label'] }}</h3>
              <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                <x-admin.status-pill :variant="$isSaved ? 'activated' : 'neutral'">{{ $isSaved ? __('Saved') : __('Default') }}</x-admin.status-pill>
                <x-admin.status-pill :variant="$isActive ? 'activated' : 'pending_review'">{{ $isActive ? __('Active') : __('Inactive') }}</x-admin.status-pill>
              </div>
            </div>
            <p class="text-sm text-on-surface-variant leading-relaxed">{{ $info['default_description'] }}</p>
            <p class="mt-4 text-sm font-semibold text-secondary inline-flex items-center gap-1">
              {{ __('Open editor') }}
              <x-icon name="chevron_right" class="w-4 h-4" />
            </p>
          </x-admin.card>
        </a>
      @endforeach
    </div>
  </x-admin.page-content>
</x-app-layout>

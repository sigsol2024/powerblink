<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.announcements.index')"
    :back-label="__('Announcements')"
    :subtitle="$announcement->published_at?->format('M j, Y H:i').' · '.$announcement->audience"
  >
    <x-slot name="actions">
      @can('announcements.manage')
        <x-admin.button variant="primary" :href="route('admin.announcements.edit', $announcement)">{{ __('Edit') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <div class="mb-4 flex flex-wrap gap-2">
        <x-admin.status-pill variant="neutral">{{ $announcement->channel }}</x-admin.status-pill>
        <x-admin.status-pill :variant="$announcement->published_at ? 'activated' : 'pending_review'">{{ $announcement->published_at ? __('Published') : __('Draft') }}</x-admin.status-pill>
      </div>
      <div class="prose prose-sm max-w-none text-on-surface whitespace-pre-wrap leading-relaxed">{{ $announcement->body }}</div>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

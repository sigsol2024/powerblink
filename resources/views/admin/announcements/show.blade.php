<x-app-layout>
  <x-admin.page-header :title="$announcement->title" />
  <x-admin.page-content>
    <x-admin.card>
      <p class="text-xs text-pb-muted mb-3">{{ $announcement->published_at?->format('M j, Y H:i') }} · {{ $announcement->audience }} · {{ $announcement->channel }}</p>
      <div class="prose prose-sm max-w-none text-sm whitespace-pre-wrap">{{ $announcement->body }}</div>
      @can('announcements.manage')
        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="inline-block mt-4 text-pb-green font-semibold text-sm">{{ __('Edit') }}</a>
      @endcan
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

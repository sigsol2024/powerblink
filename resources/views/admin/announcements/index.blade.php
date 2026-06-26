<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('announcements.manage')
        <x-admin.button variant="primary" :href="route('admin.announcements.create')">{{ __('New announcement') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card variant="table" class="overflow-hidden">
      <div class="overflow-x-auto">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Title') }}</th><th class="hidden sm:table-cell">{{ __('Audience') }}</th><th>{{ __('Published') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($announcements as $announcement)
            <tr>
              <td class="font-medium">{{ $announcement->title }}</td>
              <td class="hidden sm:table-cell">{{ $announcement->audience }}</td>
              <td class="whitespace-nowrap">{{ $announcement->published_at?->format('M j, Y') }}</td>
              <td class="text-right"><a href="{{ route('admin.announcements.show', $announcement) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="4" class="p-8 text-center text-on-surface-variant">{{ __('No announcements.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </x-admin.card>
    <div class="mt-4">{{ $announcements->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('tournaments.manage')
        <x-admin.button variant="primary" :href="route('admin.tournaments.create')">{{ __('Add tournament') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card variant="table" class="overflow-hidden">
      <div class="overflow-x-auto">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Title') }}</th><th>{{ __('Dates') }}</th><th>{{ __('Status') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($tournaments as $tournament)
            <tr>
              <td class="font-medium">{{ $tournament->title }}</td>
              <td class="whitespace-nowrap">{{ $tournament->start_date?->format('M j') }}@if($tournament->end_date) – {{ $tournament->end_date->format('M j, Y') }}@endif</td>
              <td><x-admin.status-pill :variant="$tournament->status">{{ $tournament->status }}</x-admin.status-pill></td>
              <td class="text-right"><a href="{{ route('admin.tournaments.show', $tournament) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="4" class="p-8 text-center text-on-surface-variant">{{ __('No tournaments found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </x-admin.card>
    <div class="mt-4">{{ $tournaments->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

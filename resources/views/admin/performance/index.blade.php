<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('performance.manage')
        <x-admin.button variant="primary" :href="route('admin.performance.create')">{{ __('New report') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card variant="table" class="overflow-hidden">
      <div class="overflow-x-auto">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Player') }}</th><th class="hidden sm:table-cell">{{ __('Coach') }}</th><th>{{ __('Overall') }}</th><th class="hidden md:table-cell">{{ __('Date') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($reports as $report)
            <tr>
              <td class="font-medium">{{ $report->player?->name }}</td>
              <td class="hidden sm:table-cell">{{ $report->coach?->name }}</td>
              <td><span class="font-stat-md text-stat-md text-primary">{{ $report->overall_score ?? '—' }}</span></td>
              <td class="hidden md:table-cell whitespace-nowrap">{{ $report->reported_at?->format('M j, Y') }}</td>
              <td class="text-right"><a href="{{ route('admin.performance.show', $report) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No reports found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </x-admin.card>
    <div class="mt-4">{{ $reports->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

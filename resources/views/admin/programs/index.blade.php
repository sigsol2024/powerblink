<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('programs.manage')
        <x-admin.button variant="primary" :href="route('admin.programs.create')">{{ __('Add program') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <div class="overflow-x-auto rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-sm">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Name') }}</th><th>{{ __('Age group') }}</th><th class="hidden md:table-cell">{{ __('Season') }}</th><th>{{ __('Active') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($programs as $program)
            <tr>
              <td class="font-medium">{{ $program->name }}</td>
              <td>{{ $program->age_group }}</td>
              <td class="hidden md:table-cell">{{ $program->season?->name }}</td>
              <td><x-admin.status-pill :variant="$program->is_active ? 'activated' : 'neutral'">{{ $program->is_active ? __('Yes') : __('No') }}</x-admin.status-pill></td>
              <td class="text-right"><a href="{{ route('admin.programs.show', $program) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No programs found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $programs->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

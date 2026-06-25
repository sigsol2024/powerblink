<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('training_sessions.manage')
        <x-admin.button variant="primary" :href="route('admin.training-sessions.create')">{{ __('Schedule session') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <div class="overflow-x-auto rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-sm">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Date') }}</th><th>{{ __('Title') }}</th><th class="hidden md:table-cell">{{ __('Program') }}</th><th class="hidden lg:table-cell">{{ __('Coach') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($sessions as $session)
            <tr>
              <td class="whitespace-nowrap">{{ $session->date?->format('M j, Y') }}</td>
              <td class="font-medium">{{ $session->title }}</td>
              <td class="hidden md:table-cell">{{ $session->program?->name }}</td>
              <td class="hidden lg:table-cell">{{ $session->coach?->name }}</td>
              <td class="text-right"><a href="{{ route('admin.training-sessions.show', $session) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No sessions found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $sessions->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

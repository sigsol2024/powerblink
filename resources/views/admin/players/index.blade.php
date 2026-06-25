<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('players.create')
        <x-admin.button variant="primary" :href="route('admin.players.create')">{{ __('Add player') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <div class="overflow-x-auto rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-sm">
      <table class="pb-admin-table min-w-full text-sm">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th class="hidden sm:table-cell">{{ __('Program') }}</th>
            <th class="hidden md:table-cell">{{ __('Guardian') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-right">{{ __('') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($players as $player)
            <tr>
              <td class="font-medium">{{ $player->name }}</td>
              <td class="hidden sm:table-cell">{{ $player->program?->name }}</td>
              <td class="hidden md:table-cell text-on-surface-variant">{{ $player->guardian?->email }}</td>
              <td><x-admin.status-pill :variant="$player->status">{{ $player->status }}</x-admin.status-pill></td>
              <td class="text-right"><a href="{{ route('admin.players.show', $player) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No players found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $players->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  <x-admin.page-header :title="$player->name" />
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-on-surface-variant text-xs uppercase">{{ __('Program') }}</dt><dd class="font-medium">{{ $player->program?->name ?? '—' }}</dd></div>
        <div><dt class="text-on-surface-variant text-xs uppercase">{{ __('Season') }}</dt><dd>{{ $player->season?->name ?? '—' }}</dd></div>
        <div><dt class="text-on-surface-variant text-xs uppercase">{{ __('Status') }}</dt><dd>{{ $player->status }}</dd></div>
        <div><dt class="text-on-surface-variant text-xs uppercase">{{ __('Position') }}</dt><dd>{{ $player->primary_position ?? '—' }}</dd></div>
        <div><dt class="text-on-surface-variant text-xs uppercase">{{ __('DOB') }}</dt><dd>{{ $player->date_of_birth?->format('M j, Y') ?? '—' }}</dd></div>
        <div><dt class="text-on-surface-variant text-xs uppercase">{{ __('Code') }}</dt><dd>{{ $player->player_code ?? '—' }}</dd></div>
      </dl>
      @can('players.update')
        <a href="{{ route('admin.players.edit', $player) }}" class="inline-block mt-4 text-secondary font-semibold text-sm">{{ __('Edit') }}</a>
      @endcan
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

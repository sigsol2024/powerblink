<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.players.index')"
    :back-label="__('All players')"
    :subtitle="__('Player profile')"
  >
    <x-slot name="actions">
      @can('players.update')
        <x-admin.button variant="primary" :href="route('admin.players.edit', $player)">{{ __('Edit player') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Program')">{{ $player->program?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Season')">{{ $player->season?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Status')">
          <x-admin.status-pill :variant="$player->status === 'active' ? 'activated' : 'neutral'">{{ $player->status }}</x-admin.status-pill>
        </x-admin.detail-field>
        <x-admin.detail-field :label="__('Position')">{{ $player->primary_position ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Date of birth')">{{ $player->date_of_birth?->format('M j, Y') ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Player code')"><span class="font-mono">{{ $player->player_code ?? '—' }}</span></x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

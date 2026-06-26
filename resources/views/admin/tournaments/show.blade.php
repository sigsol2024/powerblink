<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.tournaments.index')"
    :back-label="__('Tournaments')"
    :subtitle="__('Tournament details')"
  >
    <x-slot name="actions">
      @can('tournaments.manage')
        <x-admin.button variant="primary" :href="route('admin.tournaments.edit', $tournament)">{{ __('Edit tournament') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Season')">{{ $tournament->season?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Category')">{{ $tournament->category ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Status')">{{ $tournament->status }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Start date')">{{ $tournament->start_date?->format('M j, Y') ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('End date')">{{ $tournament->end_date?->format('M j, Y') ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Location')">{{ $tournament->location ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Squad entries')">{{ number_format($tournament->squads->count()) }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Description')" span="full" class="font-normal leading-relaxed">{{ $tournament->description ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.training-sessions.index')"
    :back-label="__('Training schedule')"
    :subtitle="$session->date?->format('M j, Y').' · '.($session->program?->name ?? '')"
  >
    <x-slot name="actions">
      @can('training-sessions.manage')
        <x-admin.button variant="primary" :href="route('admin.training-sessions.edit', $session)">{{ __('Edit session') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Date')">{{ $session->date?->format('M j, Y') ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Time')">{{ $session->start_time }} – {{ $session->end_time }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Program')">{{ $session->program?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Coach')">{{ $session->coach?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Location')">{{ $session->location ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Session type')">{{ $session->session_type ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

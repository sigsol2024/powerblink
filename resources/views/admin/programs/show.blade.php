<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.programs.index')"
    :back-label="__('All programs')"
    :subtitle="__('Program details')"
  >
    <x-slot name="actions">
      @can('programs.manage')
        <x-admin.button variant="primary" :href="route('admin.programs.edit', $program)">{{ __('Edit program') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Season')">{{ $program->season?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Age group')">{{ $program->age_group ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Status')">
          <x-admin.status-pill :variant="$program->is_active ? 'activated' : 'neutral'">{{ $program->is_active ? __('Active') : __('Inactive') }}</x-admin.status-pill>
        </x-admin.detail-field>
        <x-admin.detail-field :label="__('Registration fee')">{{ $program->registration_fee ? format_currency($program->registration_fee) : '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Monthly fee')">{{ $program->monthly_fee ? format_currency($program->monthly_fee) : '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Active players')">{{ number_format($program->players->count()) }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Description')" span="full" class="font-normal leading-relaxed">{{ $program->description ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

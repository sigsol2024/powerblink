<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.coaches.index')"
    :back-label="__('Coaching staff')"
    :subtitle="__('Coach profile')"
  >
    <x-slot name="actions">
      @can('coaches.manage')
        <x-admin.button variant="primary" :href="route('admin.coaches.edit', $coach)">{{ __('Edit coach') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Title')">{{ $coach->title ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Specialization')">{{ $coach->specialization ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Email')">{{ $coach->email ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Phone')">{{ $coach->phone ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Bio')" span="full" class="font-normal leading-relaxed">{{ $coach->bio ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

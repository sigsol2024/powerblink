<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.performance.index')"
    :back-label="__('Performance reports')"
    :subtitle="$report->reported_at?->format('M j, Y')"
  />

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Player')">{{ $report->player?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Coach')">{{ $report->coach?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Overall score')"><span class="font-stat-md text-stat-md text-primary">{{ $report->overall_score ?? '—' }}</span></x-admin.detail-field>
        @foreach (['passing', 'dribbling', 'speed', 'fitness', 'discipline', 'teamwork'] as $metric)
          <x-admin.detail-field :label="ucfirst($metric)">{{ $report->$metric ?? '—' }}</x-admin.detail-field>
        @endforeach
        <x-admin.detail-field :label="__('Comments')" span="full" class="font-normal leading-relaxed">{{ $report->comments ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

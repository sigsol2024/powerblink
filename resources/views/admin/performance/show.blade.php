<x-app-layout>
  <x-admin.page-header :title="__('Performance report')" />
  <x-admin.page-content>
    <x-admin.card>
      <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Player') }}</dt><dd class="font-medium">{{ $report->player?->name }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Coach') }}</dt><dd>{{ $report->coach?->name ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Overall') }}</dt><dd class="font-stat text-lg">{{ $report->overall_score ?? '—' }}</dd></div>
        @foreach (['passing','dribbling','speed','fitness','discipline','teamwork'] as $metric)
          <div><dt class="text-pb-muted text-xs uppercase">{{ ucfirst($metric) }}</dt><dd>{{ $report->$metric ?? '—' }}</dd></div>
        @endforeach
        <div class="col-span-full"><dt class="text-pb-muted text-xs uppercase">{{ __('Comments') }}</dt><dd class="mt-1">{{ $report->comments ?? '—' }}</dd></div>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

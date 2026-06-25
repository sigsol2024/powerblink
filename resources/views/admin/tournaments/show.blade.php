<x-app-layout>
  <x-admin.page-header :title="$tournament->title" />
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Season') }}</dt><dd>{{ $tournament->season?->name }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Category') }}</dt><dd>{{ $tournament->category ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Location') }}</dt><dd>{{ $tournament->location ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Status') }}</dt><dd>{{ $tournament->status }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-pb-muted text-xs uppercase">{{ __('Description') }}</dt><dd class="mt-1">{{ $tournament->description ?? '—' }}</dd></div>
      </dl>
      <p class="mt-4 text-sm text-pb-muted">{{ __(':count squad entries', ['count' => $tournament->squads->count()]) }}</p>
      @can('tournaments.manage')
        <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="inline-block mt-4 text-pb-green font-semibold text-sm">{{ __('Edit') }}</a>
      @endcan
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

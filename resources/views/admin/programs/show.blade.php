<x-app-layout>
  <x-admin.page-header :title="$program->name" />
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Season') }}</dt><dd>{{ $program->season?->name ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Age group') }}</dt><dd>{{ $program->age_group ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Registration fee') }}</dt><dd>{{ $program->registration_fee ? format_currency($program->registration_fee) : '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Monthly fee') }}</dt><dd>{{ $program->monthly_fee ? format_currency($program->monthly_fee) : '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-pb-muted text-xs uppercase">{{ __('Description') }}</dt><dd class="mt-1">{{ $program->description ?? '—' }}</dd></div>
      </dl>
      <p class="mt-4 text-sm text-pb-muted">{{ __(':count active players', ['count' => $program->players->count()]) }}</p>
      @can('programs.manage')
        <a href="{{ route('admin.programs.edit', $program) }}" class="inline-block mt-4 text-pb-green font-semibold text-sm">{{ __('Edit') }}</a>
      @endcan
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  <x-admin.page-header :title="$session->title" />
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Date') }}</dt><dd>{{ $session->date?->format('M j, Y') }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Time') }}</dt><dd>{{ $session->start_time }} – {{ $session->end_time }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Program') }}</dt><dd>{{ $session->program?->name }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Coach') }}</dt><dd>{{ $session->coach?->name ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Location') }}</dt><dd>{{ $session->location ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Type') }}</dt><dd>{{ $session->session_type ?? '—' }}</dd></div>
      </dl>
      @can('training_sessions.manage')
        <a href="{{ route('admin.training-sessions.edit', $session) }}" class="inline-block mt-4 text-pb-green font-semibold text-sm">{{ __('Edit') }}</a>
      @endcan
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

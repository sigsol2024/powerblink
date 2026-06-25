<x-app-layout>
  <x-admin.page-header :title="$coach->name" />
  <x-admin.page-content>
    @include('admin.partials.flash')
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Title') }}</dt><dd>{{ $coach->title ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Specialization') }}</dt><dd>{{ $coach->specialization ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Email') }}</dt><dd>{{ $coach->email ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Phone') }}</dt><dd>{{ $coach->phone ?? '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="text-pb-muted text-xs uppercase">{{ __('Bio') }}</dt><dd class="mt-1">{{ $coach->bio ?? '—' }}</dd></div>
      </dl>
      @can('coaches.manage')
        <a href="{{ route('admin.coaches.edit', $coach) }}" class="inline-block mt-4 text-pb-green font-semibold text-sm">{{ __('Edit') }}</a>
      @endcan
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

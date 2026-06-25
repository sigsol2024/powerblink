<x-app-layout>
  <x-admin.page-header>
    <x-slot name="actions">
      @can('coaches.manage')
        <x-admin.button variant="primary" :href="route('admin.coaches.create')">{{ __('Add coach') }}</x-admin.button>
      @endcan
    </x-slot>
  </x-admin.page-header>
  <x-admin.page-content>
    @include('admin.partials.flash')
    <div class="overflow-x-auto rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-sm">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Name') }}</th><th class="hidden sm:table-cell">{{ __('Title') }}</th><th class="hidden md:table-cell">{{ __('License') }}</th><th class="hidden lg:table-cell">{{ __('Specialization') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($coaches as $coach)
            <tr>
              <td class="font-medium">{{ $coach->name }}</td>
              <td class="hidden sm:table-cell">{{ $coach->title }}</td>
              <td class="hidden md:table-cell">@if($coach->license_level)<x-admin.status-pill variant="activated">{{ $coach->license_level }}</x-admin.status-pill>@else — @endif</td>
              <td class="hidden lg:table-cell">{{ $coach->specialization }}</td>
              <td class="text-right"><a href="{{ route('admin.coaches.show', $coach) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a></td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No coaches found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $coaches->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

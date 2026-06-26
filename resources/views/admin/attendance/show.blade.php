<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.attendance.index')"
    :back-label="__('Attendance')"
    :subtitle="$session->date?->format('M j, Y').' · '.($session->program?->name ?? '')"
  />

  <x-admin.page-content>
    <x-admin.card variant="table" class="p-0 overflow-hidden">
      <div class="p-5 md:p-6 border-b border-outline-variant/60">
        <h3 class="font-headline-md text-headline-md text-primary">{{ $session->title }}</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="pb-admin-table min-w-full text-sm">
          <thead>
            <tr>
              <th>{{ __('Player') }}</th>
              <th>{{ __('Status') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($session->attendance as $record)
              <tr>
                <td class="font-medium">{{ $record->player?->name }}</td>
                <td><x-admin.status-pill :variant="$record->status === 'present' ? 'activated' : 'neutral'">{{ $record->status }}</x-admin.status-pill></td>
              </tr>
            @empty
              <tr><td colspan="2" class="p-8 text-center text-on-surface-variant">{{ __('No attendance marked.') }}</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

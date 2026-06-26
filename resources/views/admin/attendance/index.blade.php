<x-app-layout>
  <x-admin.page-content>
    <form method="GET" class="mb-5 flex flex-wrap gap-3 items-end">
      <div class="min-w-[14rem] flex-1">
        <label class="block text-label-caps text-xs uppercase text-on-surface-variant mb-1">{{ __('Filter by session') }}</label>
        <select name="session_id" class="w-full min-h-11 rounded-lg border-outline-variant bg-surface text-sm input-focus" onchange="this.form.submit()">
          <option value="">{{ __('All sessions') }}</option>
          @foreach ($sessions as $s)
            <option value="{{ $s->id }}" @selected($sessionId == $s->id)>{{ $s->date?->format('M j') }} — {{ $s->title }}</option>
          @endforeach
        </select>
      </div>
    </form>
    <x-admin.card variant="table" class="overflow-hidden">
      <div class="overflow-x-auto">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Player') }}</th><th class="hidden sm:table-cell">{{ __('Session') }}</th><th>{{ __('Status') }}</th>
        </tr></thead>
        <tbody>
          @forelse ($attendance as $record)
            <tr>
              <td class="font-medium">{{ $record->player?->name }}</td>
              <td class="hidden sm:table-cell">{{ $record->trainingSession?->title }}</td>
              <td><x-admin.status-pill :variant="$record->status === 'present' ? 'activated' : 'neutral'">{{ $record->status }}</x-admin.status-pill></td>
            </tr>
          @empty
            <tr><td colspan="3" class="p-8 text-center text-on-surface-variant">{{ __('No attendance records.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </x-admin.card>
    <div class="mt-4">{{ $attendance->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

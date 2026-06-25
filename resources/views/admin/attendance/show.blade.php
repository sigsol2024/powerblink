<x-app-layout>
  <x-admin.page-header :title="$session->title" />
  <x-admin.page-content>
    <x-admin.card>
      <p class="text-sm text-pb-muted mb-4">{{ $session->date?->format('M j, Y') }} · {{ $session->program?->name }}</p>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-left text-pb-muted"><tr><th class="p-2">{{ __('Player') }}</th><th class="p-2">{{ __('Status') }}</th></tr></thead>
          <tbody>
            @forelse ($session->attendance as $record)
              <tr class="border-t border-pb-border"><td class="p-2">{{ $record->player?->name }}</td><td class="p-2">{{ $record->status }}</td></tr>
            @empty
              <tr><td colspan="2" class="p-4 text-pb-muted">{{ __('No attendance marked.') }}</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  <x-admin.page-content>
    <div class="flex flex-wrap gap-2 mb-5">
      @foreach (['pending_review', 'awaiting_payment', 'activated', 'rejected', 'all'] as $tab)
        <a href="{{ route('admin.registrations.index', ['status' => $tab]) }}"
           class="inline-flex items-center min-h-11 px-4 py-2 rounded-full text-sm font-semibold transition-colors {{ $status === $tab ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
          {{ ucfirst(str_replace('_', ' ', $tab)) }}
          @if ($tab !== 'all') ({{ $counts[$tab] ?? 0 }}) @endif
        </a>
      @endforeach
    </div>

    @include('admin.partials.flash')

    <div class="overflow-x-auto rounded-xl border border-outline-variant/60 bg-surface-container-lowest shadow-sm">
      <table class="pb-admin-table min-w-full text-sm">
        <thead>
          <tr>
            <th>{{ __('Reference') }}</th>
            <th>{{ __('Player') }}</th>
            <th class="hidden md:table-cell">{{ __('Program') }}</th>
            <th class="hidden lg:table-cell">{{ __('Guardian') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-right">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($registrations as $registration)
            <tr>
              <td class="font-mono text-xs">{{ $registration->reference_code }}</td>
              <td class="font-medium">{{ $registration->player_name }}</td>
              <td class="hidden md:table-cell">{{ $registration->program?->name }}</td>
              <td class="hidden lg:table-cell text-on-surface-variant">{{ $registration->guardian?->email }}</td>
              <td>
                <x-admin.status-pill :variant="$registration->status">{{ str_replace('_', ' ', $registration->status) }}</x-admin.status-pill>
              </td>
              <td class="text-right">
                @if ($registration->status === 'pending_review')
                  <div class="flex flex-col sm:flex-row sm:justify-end gap-2 items-stretch sm:items-center">
                    @can('registrations.approve')
                      <form method="POST" action="{{ route('admin.registrations.approve', $registration) }}" class="inline">@csrf
                        <button type="submit" class="text-secondary font-semibold text-sm min-h-11 px-2">{{ __('Approve') }}</button>
                      </form>
                    @endcan
                    @can('registrations.reject')
                      <form method="POST" action="{{ route('admin.registrations.reject', $registration) }}" class="inline-flex flex-col gap-1 items-end">
                        @csrf
                        <input type="text" name="rejected_reason" placeholder="{{ __('Reason') }}" class="rounded-lg border-outline-variant text-xs p-2 max-w-[12rem]" required>
                        <button type="submit" class="text-error font-semibold text-xs min-h-11 px-2">{{ __('Reject') }}</button>
                      </form>
                    @endcan
                  </div>
                @elseif ($registration->status === 'awaiting_payment')
                  @can('registrations.approve')
                    <form method="POST" action="{{ route('admin.registrations.regenerate-token', $registration) }}" class="inline">@csrf
                      <button type="submit" class="text-primary font-semibold text-sm min-h-11 px-2">{{ __('Resend link') }}</button>
                    </form>
                  @endcan
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="p-8 text-center text-on-surface-variant">{{ __('No registrations found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $registrations->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  <x-admin.page-header :title="__('Notifications')">
    <x-slot name="actions">
      @if (Auth::user()->unreadNotifications->isNotEmpty())
        <form method="POST" action="{{ route('notifications.read-all') }}">
          @csrf
          <x-admin.button type="submit" variant="secondary">{{ __('Mark all read') }}</x-admin.button>
        </form>
      @endif
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    <x-admin.card>
      @forelse ($notifications as $notification)
        @php
          $data = $notification->data;
          $isUnread = $notification->read_at === null;
        @endphp
        <div class="py-4 border-b border-outline-variant last:border-0 {{ $isUnread ? 'bg-secondary/5 -mx-4 px-4 rounded-lg' : '' }}">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="font-semibold text-primary-container">{{ $data['title'] ?? __('Notification') }}</p>
              <p class="text-sm text-on-surface-variant mt-1">{{ $data['body'] ?? '' }}</p>
              <p class="text-xs text-on-surface-variant mt-2">{{ $notification->created_at?->diffForHumans() }}</p>
            </div>
            @if ($isUnread)
              <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                @csrf
                <x-admin.button type="submit" variant="secondary" class="text-xs">{{ __('Mark read') }}</x-admin.button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <p class="text-sm text-on-surface-variant">{{ __('No notifications yet.') }}</p>
      @endforelse
    </x-admin.card>

    @if ($notifications->hasPages())
      <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
  </x-admin.page-content>
</x-app-layout>

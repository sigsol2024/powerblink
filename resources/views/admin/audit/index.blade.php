<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.dashboard')"
    :back-label="__('Dashboard')"
    :subtitle="__('Staff activity and change history')"
  />

  <x-admin.page-content x-data="{ openId: null, toggleOpen(id) { this.openId = this.openId === id ? null : id; } }">
    <x-admin.card>
      <form method="get" action="{{ route('admin.audit.index') }}" class="pb-admin-form max-w-none grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
        <input type="search" name="q" value="{{ $search }}" class="sm:col-span-2" placeholder="{{ __('Search action, path, route, IP…') }}" />
        <select name="action">
          <option value="">{{ __('All actions') }}</option>
          @foreach (['staff.created', 'staff.updated', 'staff.deleted', 'customer.created', 'customer.updated', 'customer.deleted', 'product.created', 'product.updated', 'product.deleted', 'product.approved', 'product.rejected'] as $actionKey)
            <option value="{{ $actionKey }}" @selected(($actionFilter ?? '') === $actionKey)>{{ $actionKey }}</option>
          @endforeach
        </select>
        <select name="method">
          <option value="">{{ __('All methods') }}</option>
          @foreach (['POST', 'PUT', 'PATCH', 'DELETE'] as $m)
            <option value="{{ $m }}" @selected($method === $m)>{{ $m }}</option>
          @endforeach
        </select>
        <select name="user_id">
          <option value="">{{ __('All staff') }}</option>
          @foreach ($staffActors as $actor)
            <option value="{{ $actor->id }}" @selected($userId === (int) $actor->id)>{{ $actor->name }} ({{ $actor->email }})</option>
          @endforeach
        </select>
        <input type="date" name="from" value="{{ $from }}" />
        <input type="date" name="to" value="{{ $to }}" />
        <div class="flex flex-col gap-2 sm:col-span-2 sm:flex-row sm:items-center lg:col-span-6">
          <x-admin.button type="submit">{{ __('Apply filters') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.audit.index')">{{ __('Clear') }}</x-admin.button>
        </div>
      </form>
    </x-admin.card>

    <x-admin.card variant="table" class="overflow-hidden p-0">
      <div class="hidden lg:block overflow-x-auto">
        <table class="pb-admin-table min-w-full text-sm">
          <thead>
            <tr>
              <th>{{ __('When') }}</th>
              <th>{{ __('Staff') }}</th>
              <th>{{ __('Action') }}</th>
              <th>{{ __('Method') }}</th>
              <th>{{ __('Route') }}</th>
              <th>{{ __('Path') }}</th>
              <th>{{ __('Status') }}</th>
              <th>{{ __('IP') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($entries as $entry)
              <tr>
                <td class="whitespace-nowrap text-on-surface-variant">{{ optional($entry->created_at)->format('M j, Y g:i a') }}</td>
                <td>{{ $entry->user?->name ?? __('Unknown') }}</td>
                <td>{{ $entry->meta['summary'] ?? ($entry->route_name ?? $entry->path) }}</td>
                <td><x-admin.status-pill variant="neutral">{{ $entry->method }}</x-admin.status-pill></td>
                <td class="text-on-surface-variant">{{ $entry->route_name ?? '—' }}</td>
                <td class="max-w-md truncate text-on-surface-variant" title="{{ $entry->path }}">{{ $entry->path }}</td>
                <td>{{ $entry->status_code ?? '—' }}</td>
                <td class="text-on-surface-variant">{{ $entry->ip_address ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="p-8 text-center text-on-surface-variant">{{ __('No audit records found.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="lg:hidden space-y-3 p-4">
        @forelse ($entries as $entry)
          @php $entryKey = 'e'.$entry->id; @endphp
          <article class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest">
            <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen(@js($entryKey))" :aria-expanded="openId === @js($entryKey) ? 'true' : 'false'">
              <span class="min-w-0 flex-1 truncate font-semibold text-on-surface">{{ $entry->meta['summary'] ?? $entry->path }}</span>
              <x-admin.status-pill variant="neutral">{{ $entry->method }}</x-admin.status-pill>
              <x-icon name="expand_more" class="w-5 h-5 shrink-0 text-on-surface-variant transition-transform" x-bind:class="openId === @js($entryKey) ? 'rotate-180' : ''" />
            </button>
            <div x-show="openId === @js($entryKey)" x-cloak class="border-t border-outline-variant bg-surface-container-low px-4 py-4 text-sm text-on-surface">
              <p><span class="font-medium text-on-surface-variant">{{ __('When') }}:</span> {{ optional($entry->created_at)->format('M j, Y g:i a') }}</p>
              <p class="mt-1"><span class="font-medium text-on-surface-variant">{{ __('Staff') }}:</span> {{ $entry->user?->name ?? __('Unknown') }}</p>
              <p class="mt-1"><span class="font-medium text-on-surface-variant">{{ __('Action') }}:</span> {{ $entry->meta['summary'] ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-on-surface-variant">{{ __('Route') }}:</span> {{ $entry->route_name ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-on-surface-variant">{{ __('Status') }}:</span> {{ $entry->status_code ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-on-surface-variant">{{ __('IP') }}:</span> {{ $entry->ip_address ?? '—' }}</p>
            </div>
          </article>
        @empty
          <p class="py-6 text-center text-sm text-on-surface-variant">{{ __('No audit records found.') }}</p>
        @endforelse
      </div>

      <div class="border-t border-outline-variant px-4 py-3">
        {{ $entries->links() }}
      </div>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

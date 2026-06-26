<x-app-layout>
  <header class="px-4 md:px-6 py-6 md:py-8 border-b border-outline-variant shrink-0">
    <h2 class="text-lg font-semibold text-wp-text tracking-tight">{{ __('Audit trail') }}</h2>
  </header>

  <div class="admin-content-toolbar px-4 md:px-6">
    <div class="admin-content-toolbar__actions">
      <a href="{{ route('admin.dashboard') }}" class="admin-btn">{{ __('Back to overview') }}</a>
    </div>
  </div>
  <div class="w-full space-y-6 px-4 md:px-6 py-6 md:py-8" x-data="{ openId: null, toggleOpen(id) { this.openId = this.openId === id ? null : id; } }">
    <div class="rounded-2xl border border-zinc-200/90 bg-white p-4 shadow-sm ring-1 ring-black/[0.02] sm:p-5">
      <form method="get" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
        <input type="search" name="q" value="{{ $search }}" class="rounded border-zinc-300 sm:col-span-2" placeholder="{{ __('Search action, path, route, IP…') }}" />
        <select name="action" class="rounded border-zinc-300">
          <option value="">{{ __('All actions') }}</option>
          @foreach (['staff.created', 'staff.updated', 'staff.deleted', 'customer.created', 'customer.updated', 'customer.deleted', 'product.created', 'product.updated', 'product.deleted', 'product.approved', 'product.rejected'] as $actionKey)
            <option value="{{ $actionKey }}" @selected(($actionFilter ?? '') === $actionKey)>{{ $actionKey }}</option>
          @endforeach
        </select>
        <select name="method" class="rounded border-zinc-300">
          <option value="">{{ __('All methods') }}</option>
          @foreach (['POST', 'PUT', 'PATCH', 'DELETE'] as $m)
            <option value="{{ $m }}" @selected($method === $m)>{{ $m }}</option>
          @endforeach
        </select>
        <select name="user_id" class="rounded border-zinc-300">
          <option value="">{{ __('All staff') }}</option>
          @foreach ($staffActors as $actor)
            <option value="{{ $actor->id }}" @selected($userId === (int) $actor->id)>{{ $actor->name }} ({{ $actor->email }})</option>
          @endforeach
        </select>
        <input type="date" name="from" value="{{ $from }}" class="rounded border-zinc-300" />
        <input type="date" name="to" value="{{ $to }}" class="rounded border-zinc-300" />
        <div class="flex flex-col gap-2 sm:col-span-2 sm:flex-row sm:items-center lg:col-span-6">
          <button type="submit" class="admin-btn-primary">{{ __('Apply filters') }}</button>
          <a href="{{ route('admin.audit.index') }}" class="admin-btn">{{ __('Clear') }}</a>
        </div>
      </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-black/[0.02]">
      <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
          <thead class="bg-zinc-50 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">
            <tr>
              <th class="px-3 py-2">{{ __('When') }}</th>
              <th class="px-3 py-2">{{ __('Staff') }}</th>
              <th class="px-3 py-2">{{ __('Action') }}</th>
              <th class="px-3 py-2">{{ __('Method') }}</th>
              <th class="px-3 py-2">{{ __('Route') }}</th>
              <th class="px-3 py-2">{{ __('Path') }}</th>
              <th class="px-3 py-2">{{ __('Status') }}</th>
              <th class="px-3 py-2">{{ __('IP') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-100 bg-white">
            @forelse ($entries as $entry)
              <tr>
                <td class="whitespace-nowrap px-3 py-2 text-zinc-700">{{ optional($entry->created_at)->format('M j, Y g:i a') }}</td>
                <td class="px-3 py-2 text-zinc-700">{{ $entry->user?->name ?? __('Unknown') }}</td>
                <td class="px-3 py-2 text-zinc-700">{{ $entry->meta['summary'] ?? ($entry->route_name ?? $entry->path) }}</td>
                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-700">{{ $entry->method }}</span></td>
                <td class="px-3 py-2 text-zinc-600">{{ $entry->route_name ?? '—' }}</td>
                <td class="max-w-md truncate px-3 py-2 text-zinc-600" title="{{ $entry->path }}">{{ $entry->path }}</td>
                <td class="px-3 py-2 text-zinc-700">{{ $entry->status_code ?? '—' }}</td>
                <td class="px-3 py-2 text-zinc-600">{{ $entry->ip_address ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-3 py-5 text-center text-zinc-500">{{ __('No audit records found.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="lg:hidden space-y-3 p-4">
        @forelse ($entries as $entry)
          @php $entryKey = 'e'.$entry->id; @endphp
          <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
            <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen(@js($entryKey))" :aria-expanded="openId === @js($entryKey) ? 'true' : 'false'">
              <span class="min-w-0 flex-1 truncate font-semibold text-zinc-900">{{ $entry->meta['summary'] ?? $entry->path }}</span>
              <span class="inline-flex shrink-0 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-700">{{ $entry->method }}</span>
              <svg class="h-5 w-5 shrink-0 text-zinc-400 transition-transform" :class="openId === @js($entryKey) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openId === @js($entryKey)" x-cloak class="border-t border-zinc-100 bg-zinc-50 px-4 py-4 text-sm text-zinc-700">
              <p><span class="font-medium text-zinc-500">{{ __('When') }}:</span> {{ optional($entry->created_at)->format('M j, Y g:i a') }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Staff') }}:</span> {{ $entry->user?->name ?? __('Unknown') }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Action') }}:</span> {{ $entry->meta['summary'] ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Route') }}:</span> {{ $entry->route_name ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Status') }}:</span> {{ $entry->status_code ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('IP') }}:</span> {{ $entry->ip_address ?? '—' }}</p>
            </div>
          </article>
        @empty
          <p class="py-6 text-center text-sm text-zinc-500">{{ __('No audit records found.') }}</p>
        @endforelse
      </div>

      <div class="border-t border-zinc-200 px-4 py-3">
        {{ $entries->links() }}
      </div>
    </div>
  </div>
</x-app-layout>

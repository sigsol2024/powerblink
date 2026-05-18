<x-app-layout>
    <x-slot name="header">
    <div>
      <p class="admin-page-eyebrow">{{ __('Admin') }}</p>
      <h2 class="admin-page-title truncate">{{ __('Overview') }}</h2>
    </div>
  </x-slot>

  <div class="space-y-10">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02] transition hover:shadow-md">
        <div class="flex items-start justify-between gap-3">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Total listings') }}</span>
          <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6.878V6a2.25 2.25 0 012.25-2.25h9.75A2.25 2.25 0 0120.25 6v.878m-15.75 1.5h15m-15 0a2.25 2.25 0 00-2.25 2.25v9.75A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25v-9.75a2.25 2.25 0 00-2.25-2.25h-15z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-4xl font-bold tabular-nums tracking-tight text-zinc-900">{{ $stats['total_listings'] ?? 0 }}</p>
      </div>
      <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02] transition hover:shadow-md">
        <div class="flex items-start justify-between gap-3">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Pending review') }}</span>
          <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-4xl font-bold tabular-nums tracking-tight text-zinc-900">{{ $stats['pending_listings'] ?? 0 }}</p>
      </div>
      <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02] transition hover:shadow-md">
        <div class="flex items-start justify-between gap-3">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Approved live') }}</span>
          <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-4xl font-bold tabular-nums tracking-tight text-zinc-900">{{ $stats['approved_listings'] ?? 0 }}</p>
      </div>
      <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02] transition hover:shadow-md">
        <div class="flex items-start justify-between gap-3">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Users') }}</span>
          <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
          </span>
        </div>
        <p class="mt-4 text-4xl font-bold tabular-nums tracking-tight text-zinc-900">{{ $stats['users_count'] ?? 0 }}</p>
      </div>
    </div>

    <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02]">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-zinc-500">{{ __('Traffic summary') }}</h2>
        <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-50">{{ __('Open analytics') }}</a>
      </div>
      @php $traffic = $analyticsSummary ?? []; @endphp
      <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Views (:days days)', ['days' => $traffic['range_days'] ?? 90]) }}</div>
          <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900">{{ number_format((int) ($traffic['total_views'] ?? 0)) }}</div>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Unique sessions') }}</div>
          <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900">{{ number_format((int) ($traffic['unique_sessions'] ?? 0)) }}</div>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Top page') }}</div>
          <div class="mt-1 truncate text-sm font-semibold text-zinc-800">{{ $traffic['top_page_label'] ?? __('No data yet') }}</div>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Top listing') }}</div>
          <div class="mt-1 truncate text-sm font-semibold text-zinc-800">{{ $traffic['top_listing']->vehicle_slug ?? __('No data yet') }}</div>
        </div>
      </div>
    </div>

    <div>
      <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-zinc-500">{{ __('Shortcuts') }}</h2>
      <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('dashboard.vehicles.index') }}" class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-amber-300/80 hover:shadow-lg">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Inventory') }}</span>
          <span class="mt-2 block text-lg font-bold text-zinc-900">{{ __('All vehicle listings') }}</span>
          <span class="mt-4 inline-flex items-center text-sm font-semibold text-amber-600 group-hover:text-amber-700">{{ __('Open →') }}</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-violet-300/80 hover:shadow-lg">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Accounts') }}</span>
          <span class="mt-2 block text-lg font-bold text-zinc-900">{{ __('Users & dealers') }}</span>
          <span class="mt-4 inline-flex items-center text-sm font-semibold text-violet-600 group-hover:text-violet-700">{{ __('Open →') }}</span>
        </a>
        <a href="{{ route('admin.pages.index') }}" class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-sky-300/80 hover:shadow-lg">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Content') }}</span>
          <span class="mt-2 block text-lg font-bold text-zinc-900">{{ __('Page editors') }}</span>
          <span class="mt-4 inline-flex items-center text-sm font-semibold text-sky-600 group-hover:text-sky-700">{{ __('Open →') }}</span>
        </a>
        <a href="{{ route('admin.audit.index') }}" class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-indigo-300/80 hover:shadow-lg">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Governance') }}</span>
          <span class="mt-2 block text-lg font-bold text-zinc-900">{{ __('Audit trail log') }}</span>
          <span class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-600 group-hover:text-indigo-700">{{ __('Open →') }}</span>
        </a>
        <a href="{{ route('admin.media.index') }}" class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-zinc-400 hover:shadow-lg">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Assets') }}</span>
          <span class="mt-2 block text-lg font-bold text-zinc-900">{{ __('Media library') }}</span>
          <span class="mt-4 inline-flex items-center text-sm font-semibold text-zinc-600 group-hover:text-zinc-800">{{ __('Open →') }}</span>
        </a>
        <a href="{{ route('inventory.index') }}" target="_blank" rel="noopener" class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-emerald-300/80 hover:shadow-lg">
          <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Public') }}</span>
          <span class="mt-2 block text-lg font-bold text-zinc-900">{{ __('Live inventory') }}</span>
          <span class="mt-4 inline-flex items-center text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">{{ __('Open site →') }}</span>
        </a>
      </div>
    </div>

    <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02]" x-data="{ openId: null, toggleOpen(id) { this.openId = this.openId === id ? null : id; } }">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-zinc-500">{{ __('Audit trail') }}</h2>
        @php $audit = $auditSummary ?? []; @endphp
        <div class="flex items-center gap-3">
          <span class="text-xs font-semibold text-zinc-500">{{ __('Last :days days', ['days' => $audit['range_days'] ?? 30]) }}</span>
          <a href="{{ route('admin.audit.index') }}" class="inline-flex items-center rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-50">{{ __('Open full log') }}</a>
        </div>
      </div>
      <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Total actions') }}</div>
          <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900">{{ number_format((int) ($audit['total_actions'] ?? 0)) }}</div>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Create (POST)') }}</div>
          <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900">{{ number_format((int) ($audit['create_actions'] ?? 0)) }}</div>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Update (PUT/PATCH)') }}</div>
          <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900">{{ number_format((int) ($audit['update_actions'] ?? 0)) }}</div>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3">
          <div class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Delete (DELETE)') }}</div>
          <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900">{{ number_format((int) ($audit['delete_actions'] ?? 0)) }}</div>
        </div>
      </div>
      <div class="hidden lg:block mt-4 overflow-hidden rounded-xl border border-zinc-200">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
          <thead class="bg-zinc-50 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">
            <tr>
              <th class="px-3 py-2">{{ __('When') }}</th>
              <th class="px-3 py-2">{{ __('Admin') }}</th>
              <th class="px-3 py-2">{{ __('Method') }}</th>
              <th class="px-3 py-2">{{ __('Route') }}</th>
              <th class="px-3 py-2">{{ __('Path') }}</th>
              <th class="px-3 py-2">{{ __('Status') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-100 bg-white">
            @forelse (($audit['recent'] ?? []) as $entry)
              <tr>
                <td class="px-3 py-2 text-zinc-700">{{ optional($entry->created_at)->format('M j, Y g:i a') }}</td>
                <td class="px-3 py-2 text-zinc-700">{{ $entry->user?->name ?? __('Unknown') }}</td>
                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-700">{{ $entry->method }}</span></td>
                <td class="px-3 py-2 text-zinc-600">{{ $entry->route_name ?? '—' }}</td>
                <td class="max-w-xs truncate px-3 py-2 text-zinc-600" title="{{ $entry->path }}">{{ $entry->path }}</td>
                <td class="px-3 py-2 text-zinc-700">{{ $entry->status_code ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-3 py-4 text-center text-zinc-500">{{ __('No audit actions yet.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="lg:hidden mt-4 space-y-3">
        @forelse (($audit['recent'] ?? []) as $entry)
          <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
            <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen({{ $entry->id }})" :aria-expanded="openId === {{ $entry->id }} ? 'true' : 'false'">
              <span class="min-w-0 flex-1 truncate font-semibold text-zinc-900">{{ $entry->path }}</span>
              <span class="inline-flex shrink-0 rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-700">{{ $entry->method }}</span>
              <svg class="h-5 w-5 shrink-0 text-zinc-400 transition-transform" :class="openId === {{ $entry->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openId === {{ $entry->id }}" x-cloak class="border-t border-zinc-100 bg-zinc-50 px-4 py-4 text-sm text-zinc-700">
              <p><span class="font-medium text-zinc-500">{{ __('When') }}:</span> {{ optional($entry->created_at)->format('M j, Y g:i a') }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Admin') }}:</span> {{ $entry->user?->name ?? __('Unknown') }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Route') }}:</span> {{ $entry->route_name ?? '—' }}</p>
              <p class="mt-1"><span class="font-medium text-zinc-500">{{ __('Status') }}:</span> {{ $entry->status_code ?? '—' }}</p>
            </div>
          </article>
        @empty
          <p class="py-4 text-center text-sm text-zinc-500">{{ __('No audit actions yet.') }}</p>
        @endforelse
      </div>

    </div>
  </div>
</x-app-layout>

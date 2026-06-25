<x-app-layout>
  @php
    $traffic = $analyticsSummary ?? [];
    $pending = (int) ($stats['pending_registrations'] ?? 0);
    $awaiting = (int) ($stats['awaiting_payment'] ?? 0);
    $players = (int) ($stats['active_players'] ?? 0);
    $visitors = (int) ($stats['visitors_total'] ?? ($traffic['total_views'] ?? 0));
  @endphp

  <x-admin.page-content>
    <div class="mb-6">
      <p class="text-label-caps text-label-caps uppercase tracking-widest text-on-surface-variant text-xs">{{ __('Elite admin portal') }}</p>
      <p class="text-sm text-on-surface-variant mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Pending review'),
        'value' => number_format($pending),
        'hint' => __('applications'),
        'accent' => 'gold',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Awaiting payment'),
        'value' => number_format($awaiting),
        'hint' => __('approved'),
        'accent' => 'navy',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Active players'),
        'value' => number_format($players),
        'hint' => __('enrolled'),
        'accent' => 'secondary',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Page visitors'),
        'value' => number_format($visitors),
        'hint' => __('last :days days', ['days' => (int) ($traffic['range_days'] ?? 90)]),
        'accent' => 'green',
      ])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <x-admin.card variant="table" class="lg:col-span-2">
        <div class="p-4 md:p-5 border-b border-outline-variant/60 flex flex-wrap justify-between items-center gap-3">
          <h4 class="font-headline-md text-headline-md text-primary">{{ __('Recent registrations') }}</h4>
          <a href="{{ route('admin.registrations.index') }}" class="text-sm font-semibold text-secondary hover:underline inline-flex items-center gap-1 min-h-11">
            {{ __('View all') }}
            <span class="material-symbols-outlined text-base">chevron_right</span>
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="pb-admin-table min-w-full text-sm">
            <thead>
              <tr>
                <th>{{ __('Submitted') }}</th>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Player') }}</th>
                <th class="hidden md:table-cell">{{ __('Program') }}</th>
                <th class="text-right">{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse (($recentRegistrations ?? []) as $registration)
                <tr>
                  <td class="text-on-surface-variant whitespace-nowrap">{{ optional($registration->submitted_at)->format('M j, Y') }}</td>
                  <td class="font-mono text-xs font-medium">{{ $registration->reference_code }}</td>
                  <td>{{ $registration->player_name }}</td>
                  <td class="hidden md:table-cell max-w-xs truncate" title="{{ $registration->program?->name }}">{{ $registration->program?->name ?? '—' }}</td>
                  <td class="text-right">
                    <x-admin.status-pill :variant="$registration->status">{{ str_replace('_', ' ', $registration->status) }}</x-admin.status-pill>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No registrations yet.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-admin.card>

      <x-admin.card variant="glass" class="p-5 md:p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h4 class="font-headline-md text-headline-md text-primary">{{ __('Site traffic') }}</h4>
            <p class="text-[10px] uppercase tracking-wide text-on-surface-variant mt-0.5">{{ __('Last :days days', ['days' => (int) ($traffic['range_days'] ?? 90)]) }}</p>
          </div>
          <a href="{{ route('admin.analytics.index') }}" class="pb-touch p-2 text-on-surface-variant hover:text-secondary" aria-label="{{ __('Open analytics') }}">
            <span class="material-symbols-outlined">analytics</span>
          </a>
        </div>
        <div class="space-y-4 text-sm">
          <div>
            <p class="text-xs uppercase tracking-wide text-on-surface-variant">{{ __('Total views') }}</p>
            <p class="font-stat-md text-stat-md text-primary mt-1">{{ number_format((int) ($traffic['total_views'] ?? 0)) }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wide text-on-surface-variant">{{ __('Unique sessions') }}</p>
            <p class="font-stat-md text-stat-md text-primary mt-1">{{ number_format((int) ($traffic['unique_sessions'] ?? 0)) }}</p>
          </div>
          <div class="pt-3 border-t border-outline-variant/50">
            <p class="text-xs uppercase tracking-wide text-on-surface-variant">{{ __('Top page') }}</p>
            <p class="text-sm mt-1 font-medium">{{ $traffic['top_page_label'] ?? __('No data yet') }}</p>
          </div>
        </div>
        <div class="mt-6 grid grid-cols-1 gap-2">
          <x-admin.button variant="primary" :href="route('admin.registrations.index')" class="w-full">{{ __('Review registrations') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.players.index')" class="w-full">{{ __('Manage players') }}</x-admin.button>
          <x-admin.button variant="ghost" :href="route('home')" target="_blank" rel="noopener" class="w-full justify-center">{{ __('View site') }}</x-admin.button>
        </div>
      </x-admin.card>
    </div>
  </x-admin.page-content>
</x-app-layout>

<x-app-layout>
  @php
    $stats = $stats ?? [];
    $players = (int) ($stats['active_players'] ?? 0);
    $registrations = (int) ($stats['active_registrations'] ?? 0);
    $revenue = (int) ($stats['monthly_revenue'] ?? 0);
    $attendance = $stats['attendance_rate'] ?? null;
    $payments = $pendingPayments ?? [];
    $trends = $performanceTrends['months'] ?? [];
    $events = $upcomingEvents ?? [];
  @endphp

  <x-admin.page-content class="space-y-6">
    <div>
      <p class="font-label-caps text-label-caps uppercase tracking-widest text-on-surface-variant text-xs">{{ __('Elite admin portal') }}</p>
      <p class="text-sm text-on-surface-variant mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6">
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Total Players'),
        'value' => number_format($players),
        'icon' => 'sports_soccer',
        'badge' => __('Active'),
        'badgeVariant' => 'success',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Active Registrations'),
        'value' => number_format($registrations),
        'icon' => 'how_to_reg',
        'badge' => __('Pipeline'),
        'badgeVariant' => 'info',
        'iconBg' => 'bg-primary-fixed/40',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Monthly Revenue'),
        'value' => format_currency($revenue),
        'icon' => 'payments',
        'badge' => __('This month'),
        'badgeVariant' => 'warning',
        'iconBg' => 'bg-tertiary-fixed/30',
      ])
      @include('partials.powerblink.dashboard-stat-card', [
        'label' => __('Attendance Rate'),
        'value' => $attendance !== null ? $attendance.'%' : '—',
        'hint' => $attendance !== null ? __('last 30 days') : __('no sessions yet'),
        'icon' => 'fact_check',
        'iconBg' => 'bg-secondary-container',
      ])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
      <x-admin.card variant="table" class="lg:col-span-2">
        <div class="p-4 md:p-6 border-b border-outline-variant/60 flex flex-wrap justify-between items-center gap-3">
          <h4 class="font-headline-md text-headline-md text-primary">{{ __('Recent registrations') }}</h4>
          <a href="{{ route('admin.registrations.index') }}" class="text-sm font-semibold text-secondary hover:underline inline-flex items-center gap-1 min-h-11">
            {{ __('View all') }}
            <x-icon name="chevron_right" class="w-4 h-4" />
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="pb-admin-table min-w-full text-sm">
            <thead>
              <tr>
                <th>{{ __('Player') }}</th>
                <th class="hidden md:table-cell">{{ __('Category') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-right">{{ __('Action') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse (($recentRegistrations ?? []) as $registration)
                @php
                  $initials = collect(explode(' ', trim((string) $registration->player_name)))
                    ->filter()
                    ->take(2)
                    ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                    ->join('');
                @endphp
                <tr class="hover:bg-surface-container-low transition-colors">
                  <td>
                    <div class="flex items-center gap-3 min-w-[10rem]">
                      <span class="h-9 w-9 rounded-full bg-primary-container text-on-primary text-xs font-bold flex items-center justify-center shrink-0">{{ $initials ?: '?' }}</span>
                      <div class="min-w-0">
                        <p class="font-medium text-on-surface truncate">{{ $registration->player_name }}</p>
                        <p class="text-xs text-on-surface-variant font-mono">{{ $registration->reference_code }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="hidden md:table-cell max-w-xs truncate text-on-surface-variant" title="{{ $registration->program?->name }}">{{ $registration->program?->name ?? '—' }}</td>
                  <td class="text-on-surface-variant whitespace-nowrap">{{ optional($registration->submitted_at)->format('M j, Y') }}</td>
                  <td>
                    <x-admin.status-pill :variant="$registration->status">{{ str_replace('_', ' ', $registration->status) }}</x-admin.status-pill>
                  </td>
                  <td class="text-right">
                    <a href="{{ route('admin.registrations.index') }}" class="text-secondary font-semibold text-xs hover:underline">{{ __('Review') }}</a>
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

      <x-admin.card variant="navy" class="p-5 md:p-6 text-on-primary">
        <div class="flex justify-between items-start mb-5">
          <div>
            <h4 class="font-headline-md text-headline-md">{{ __('Upcoming Events') }}</h4>
            <p class="text-[10px] uppercase tracking-wide opacity-70 mt-0.5">{{ __('Training & fixtures') }}</p>
          </div>
          <x-icon name="event_note" class="w-5 h-5 opacity-70" />
        </div>
        <div class="space-y-4">
          @forelse ($events as $event)
            <div class="flex gap-3 items-start">
              <div class="shrink-0 w-12 text-center rounded-lg bg-white/10 py-2">
                <p class="text-lg font-bold leading-none">{{ $event['date']->format('d') }}</p>
                <p class="text-[10px] uppercase opacity-70">{{ $event['date']->format('M') }}</p>
              </div>
              <div class="min-w-0 flex-1">
                <p class="font-semibold text-sm truncate">{{ $event['title'] }}</p>
                <p class="text-xs opacity-70 truncate">{{ $event['location'] ?? __('TBA') }}</p>
                <p class="text-[10px] mt-1 flex items-center gap-1.5">
                  <span class="h-1.5 w-1.5 rounded-full {{ $event['type'] === 'session' ? 'bg-secondary-fixed' : 'bg-tertiary-fixed' }}"></span>
                  {{ $event['category'] ?? ucfirst($event['type']) }}
                </p>
              </div>
            </div>
          @empty
            <p class="text-sm opacity-70">{{ __('No upcoming events scheduled.') }}</p>
          @endforelse
        </div>
        <a href="{{ route('admin.training-sessions.index') }}" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-secondary-fixed hover:underline">
          {{ __('View schedule') }}
          <x-icon name="arrow_forward" class="w-4 h-4" />
        </a>
      </x-admin.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
      <x-admin.card class="p-5 md:p-6">
        <div class="flex justify-between items-start mb-5">
          <div>
            <h4 class="font-headline-md text-headline-md text-primary">{{ __('Pending Payments') }}</h4>
            <p class="text-xs text-on-surface-variant mt-0.5">{{ __(':count open items', ['count' => number_format((int) ($payments['total_count'] ?? 0))]) }}</p>
          </div>
          <a href="{{ route('admin.payments.index') }}" class="pb-touch p-2 text-on-surface-variant hover:text-secondary" aria-label="{{ __('Open payments') }}">
            <x-icon name="payments" class="w-5 h-5" />
          </a>
        </div>
        <div class="space-y-4">
          <div>
            <div class="flex justify-between text-xs mb-1.5">
              <span class="font-semibold text-on-surface-variant uppercase tracking-wide">{{ __('Overdue') }}</span>
              <span class="font-bold text-error">{{ number_format((int) ($payments['overdue_count'] ?? 0)) }} · {{ format_currency((int) ($payments['overdue_amount'] ?? 0)) }}</span>
            </div>
            <div class="h-2 rounded-full bg-surface-container-high overflow-hidden">
              <div class="h-full rounded-full bg-error transition-all" style="width: {{ (int) ($payments['overdue_pct'] ?? 0) }}%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-xs mb-1.5">
              <span class="font-semibold text-on-surface-variant uppercase tracking-wide">{{ __('Due this week') }}</span>
              <span class="font-bold text-secondary">{{ number_format((int) ($payments['due_week_count'] ?? 0)) }} · {{ format_currency((int) ($payments['due_week_amount'] ?? 0)) }}</span>
            </div>
            <div class="h-2 rounded-full bg-surface-container-high overflow-hidden">
              <div class="h-full rounded-full bg-secondary transition-all" style="width: {{ (int) ($payments['due_week_pct'] ?? 0) }}%"></div>
            </div>
          </div>
        </div>
      </x-admin.card>

      <x-admin.card class="p-5 md:p-6">
        <div class="flex justify-between items-start mb-5">
          <div>
            <h4 class="font-headline-md text-headline-md text-primary">{{ __('Performance Trends') }}</h4>
            <p class="text-xs text-on-surface-variant mt-0.5">{{ __('Avg. overall score — last 6 months') }}</p>
          </div>
          <a href="{{ route('admin.performance.index') }}" class="pb-touch p-2 text-on-surface-variant hover:text-secondary" aria-label="{{ __('Open performance') }}">
            <x-icon name="analytics" class="w-5 h-5" />
          </a>
        </div>
        <div class="flex items-end justify-between gap-2 h-32 pt-2">
          @forelse ($trends as $bar)
            <div class="flex-1 flex flex-col items-center gap-2 min-w-0">
              <span class="text-[10px] font-bold text-on-surface-variant">{{ $bar['value'] > 0 ? number_format($bar['value'], 1) : '' }}</span>
              <div class="w-full max-w-[2.5rem] rounded-t-lg bg-surface-container-high flex items-end" style="height: 5rem">
                <div class="w-full rounded-t-lg bg-secondary transition-all" style="height: {{ (int) $bar['height'] }}%"></div>
              </div>
              <span class="text-[10px] uppercase text-on-surface-variant font-semibold">{{ $bar['label'] }}</span>
            </div>
          @empty
            <p class="text-sm text-on-surface-variant w-full text-center">{{ __('No performance data yet.') }}</p>
          @endforelse
        </div>
      </x-admin.card>
    </div>
  </x-admin.page-content>
</x-app-layout>

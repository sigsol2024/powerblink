@push('head')
  <style>
    .pb-analytics-canvas {
      background: linear-gradient(180deg, #f2f4f6 0%, #fafbfd 100%);
    }
    .pb-analytics-toolbar {
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(11, 28, 52, 0.08);
      box-shadow: 0 4px 24px -4px rgba(11, 28, 52, 0.08);
    }
    .pb-analytics-kpi {
      background: #fff;
      border: 1px solid rgba(11, 28, 52, 0.08);
      box-shadow: 0 4px 24px -4px rgba(11, 28, 52, 0.08);
    }
    .pb-analytics-card {
      background: #fff;
      border: 1px solid rgba(11, 28, 52, 0.08);
      box-shadow: 0 4px 24px -4px rgba(11, 28, 52, 0.06);
    }
    .pb-analytics-metric { font-variant-numeric: tabular-nums; letter-spacing: -0.02em; }
    .pb-analytics-bar-track {
      background: #e8ecf2;
      box-shadow: inset 0 1px 2px rgba(11, 28, 52, 0.06);
    }
  </style>
@endpush

@push('scripts')
  @vite(['resources/js/admin-analytics.js'])
@endpush

<x-app-layout>
  <x-admin.page-header :title="__('Analytics')" :subtitle="__('Academy site traffic and engagement')" />

  <x-admin.page-content
    class="pb-analytics-canvas text-on-surface relative w-full min-h-0 antialiased overflow-x-hidden"
    x-data="analyticsPage({
      trafficSubTemplate: @js(__('User engagement and volume over the last :count days', ['count' => '__N__'])),
      endpoint: '{{ route('admin.analytics.data') }}',
      range: {{ $rangeDays }},
      startDate: '{{ $startDate }}',
      endDate: '{{ $endDate }}',
      initial: @js([
        'rangeDays' => $rangeDays,
        'summary' => $summary,
        'kpiDeltas' => $kpiDeltas,
        'dailyTrend' => $dailyTrend,
        'trendBars' => $trendBars,
        'trendXLabels' => $trendXLabels,
        'lineChart' => $lineChart,
        'topPages' => $topPages,
        'topPrograms' => $topPrograms,
        'engagementRatio' => $engagementRatio,
        'topReferrers' => $topReferrers,
        'deviceBreakdown' => $deviceBreakdown,
      ]),
    })"
    x-init="initCharts()"
  >
    @php $diag = $trackingDiagnostic ?? []; @endphp
    <p class="text-xs text-on-surface-variant">
      {{ __('Tracking') }}:
      @if (! empty($diag['last_event_at']))
        {{ __('Last event :time (:count total)', ['time' => $diag['last_event_at'], 'count' => number_format((int) ($diag['total_events'] ?? 0))]) }}
      @else
        {{ __('No events recorded yet. Visit the public site while logged out, then click Apply.') }}
      @endif
    </p>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div></div>
      <a
        :href="`{{ route('admin.analytics.index') }}?range=${range}&export=csv&start_date=${startDate}&end_date=${endDate}`"
        class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold rounded-lg transition-all min-h-11 px-4 bg-secondary text-on-secondary hover:brightness-110 active:scale-[0.98]"
      >
        <x-icon name="download" class="w-4 h-4" />
        <span>{{ __('Export CSV') }}</span>
      </a>
    </div>

      <x-admin.card variant="toolbar" class="pb-analytics-toolbar w-full min-w-0">
        <div class="mb-3 flex items-center gap-2">
          <x-icon name="filter" class="w-4 h-4 text-primary-container" />
          <span class="text-xs font-semibold uppercase tracking-wider text-[#0b1f3a]">{{ __('Date Range') }}</span>
        </div>
        <div class="flex flex-col gap-4 lg:flex-row lg:flex-wrap lg:items-center lg:gap-x-4 lg:gap-y-3">
          <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-3">
            <label class="sr-only" for="anx-start-date">{{ __('Start date') }}</label>
            <input id="anx-start-date" type="date" x-model="startDate" class="w-full min-w-0 rounded-lg border border-[#0a1628]/12 bg-white/95 px-3 py-2 text-sm text-on-surface shadow-sm transition hover:border-primary-container/40 focus:border-primary-container focus:outline-none focus:ring-2 focus:ring-primary-container/20 sm:w-auto sm:min-w-[11rem]">
            <span class="hidden text-sm text-on-surface-variant sm:inline">{{ __('to') }}</span>
            <label class="sr-only" for="anx-end-date">{{ __('End date') }}</label>
            <input id="anx-end-date" type="date" x-model="endDate" class="w-full min-w-0 rounded-lg border border-[#0a1628]/12 bg-white/95 px-3 py-2 text-sm text-on-surface shadow-sm transition hover:border-primary-container/40 focus:border-primary-container focus:outline-none focus:ring-2 focus:ring-primary-container/20 sm:w-auto sm:min-w-[11rem]">
          </div>
          <div class="flex flex-wrap items-center gap-2 border-t border-[#0a1628]/08 pt-4 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-4">
            <span class="w-full text-[11px] font-medium uppercase tracking-wide text-on-surface-variant sm:hidden">{{ __('Presets') }}</span>
            <template x-for="opt in [7,30,90]" :key="opt">
              <button
                type="button"
                @click="applyPreset(opt)"
                class="rounded-lg px-3 py-2 text-xs font-bold tracking-wide transition"
                :class="range === opt ? 'bg-primary-container text-white shadow-md shadow-[#0b1f3a]/25' : 'border border-[#0a1628]/10 bg-white/80 text-on-surface-variant hover:border-primary-container/30 hover:bg-white'"
              >
                <span x-text="opt + 'd'"></span>
              </button>
            </template>
          </div>
          <div class="flex justify-end lg:ml-auto">
            <button type="button" @click="load()" class="w-full rounded-lg bg-primary-container px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-[#0b1f3a]/30 transition hover:brightness-110 sm:w-auto">{{ __('Apply') }}</button>
          </div>
        </div>
      </x-admin.card>

    <section class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <div class="pb-analytics-kpi flex flex-col rounded-2xl p-6 md:p-7">
        <div class="mb-5 flex items-start justify-between gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">{{ __('Total Page Views') }}</span>
          <span class="rounded-full px-2.5 py-1 text-[10px] font-bold tabular-nums" :class="kpiPillClass(state.kpiDeltas?.views)" x-text="formatKpiDelta(state.kpiDeltas?.views)"></span>
        </div>
        <div class="pb-analytics-metric text-[2rem] font-semibold leading-none tracking-tight text-[#061018] md:text-[2.125rem]" x-text="num(state.summary.total_views)"></div>
        <div class="pb-analytics-bar-track mt-4 h-1.5 w-full overflow-hidden rounded-full">
          <div class="h-full rounded-full bg-gradient-to-r from-primary-container to-[#152a45] shadow-sm transition-all duration-500" :style="`width:${kpiBarWidth(1)}%`"></div>
        </div>
      </div>
      <div class="pb-analytics-kpi flex flex-col rounded-2xl p-6 md:p-7">
        <div class="mb-5 flex items-start justify-between gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">{{ __('Unique Sessions') }}</span>
          <span class="rounded-full px-2.5 py-1 text-[10px] font-bold tabular-nums" :class="kpiPillClass(state.kpiDeltas?.sessions)" x-text="formatKpiDelta(state.kpiDeltas?.sessions)"></span>
        </div>
        <div class="pb-analytics-metric text-[2rem] font-semibold leading-none tracking-tight text-[#061018] md:text-[2.125rem]" x-text="num(state.summary.unique_sessions)"></div>
        <div class="pb-analytics-bar-track mt-4 h-1.5 w-full overflow-hidden rounded-full">
          <div class="h-full rounded-full bg-gradient-to-r from-on-tertiary-container to-[#c49a6c] shadow-sm transition-all duration-500" :style="`width:${kpiBarWidth(2)}%`"></div>
        </div>
      </div>
      <div class="pb-analytics-kpi flex flex-col rounded-2xl p-6 md:p-7">
        <div class="mb-5 flex items-start justify-between gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">{{ __('Unique Pages Visited') }}</span>
          <span class="rounded-full px-2.5 py-1 text-[10px] font-bold tabular-nums" :class="kpiPillClass(state.kpiDeltas?.pages)" x-text="formatKpiDelta(state.kpiDeltas?.pages)"></span>
        </div>
        <div class="pb-analytics-metric text-[2rem] font-semibold leading-none tracking-tight text-[#061018] md:text-[2.125rem]" x-text="num(state.summary.unique_pages)"></div>
        <div class="pb-analytics-bar-track mt-4 h-1.5 w-full overflow-hidden rounded-full">
          <div class="h-full rounded-full bg-gradient-to-r from-error to-[#d32f2f] shadow-sm transition-all duration-500" :style="`width:${kpiBarWidth(3)}%`"></div>
        </div>
      </div>
      <div class="pb-analytics-kpi flex flex-col rounded-2xl p-6 md:p-7">
        <div class="mb-5 flex items-start justify-between gap-2">
          <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">{{ __('Top Page') }}</span>
          <span class="rounded-full border border-on-tertiary-container/20 bg-gradient-to-br from-on-tertiary-container/15 to-on-tertiary-container/5 px-2.5 py-1 text-[10px] font-bold text-on-tertiary-container shadow-sm">{{ __('Hot') }}</span>
        </div>
        <div class="pb-analytics-metric truncate text-xl font-semibold tracking-tight text-[#061018]" x-text="topProgramTitle()"></div>
        <p class="mt-2 text-[11px] font-medium leading-snug text-on-surface-variant">
          <span x-show="!topProgramViews()">—</span>
          <span x-show="topProgramViews() && Number(range) === 1"><span x-text="num(topProgramViews())"></span> {{ __('views today') }}</span>
          <span x-show="topProgramViews() && Number(range) !== 1"><span x-text="num(topProgramViews())"></span> {{ __('views in this range') }}</span>
        </p>
      </div>
    </section>

    <section class="mb-10">
      <div class="pb-analytics-card-hero rounded-2xl p-6 md:p-8">
        <div class="mb-8 flex flex-col items-start justify-between gap-5 md:flex-row md:items-center">
          <div>
            <h3 class="text-lg font-bold tracking-tight text-[#061018]">{{ __('Traffic Analytics') }}</h3>
            <p class="mt-1.5 text-sm leading-relaxed text-on-surface-variant" x-text="subTraffic()"></p>
          </div>
          <div class="flex flex-wrap gap-5">
            <div class="flex items-center gap-2.5 rounded-full border border-[#0a1628]/8 bg-white/60 px-3 py-1.5 shadow-sm">
              <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-br from-primary-container to-[#152a45] shadow-sm"></span>
              <span class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ __('Views') }}</span>
            </div>
            <div class="flex items-center gap-2.5 rounded-full border border-[#0a1628]/8 bg-white/60 px-3 py-1.5 shadow-sm">
              <span class="h-2.5 w-2.5 rounded-full bg-gradient-to-br from-on-tertiary-container to-[#8a6645] shadow-sm"></span>
              <span class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ __('Sessions') }}</span>
            </div>
          </div>
        </div>
        <div x-ref="trafficChart" class="min-h-[300px] w-full"></div>
      </div>
    </section>

    <section class="mb-10 grid grid-cols-1 gap-6 lg:grid-cols-2">
      <div class="pb-analytics-card rounded-2xl p-6 md:p-8">
        <h3 class="mb-8 text-xs font-bold uppercase tracking-[0.18em] text-on-surface-variant">{{ __('Traffic Distribution') }}</h3>
        <div class="flex flex-col items-stretch gap-8 lg:flex-row lg:items-center lg:gap-10">
          <div class="mx-auto w-full max-w-[280px] min-w-0 shrink-0 lg:mx-0">
            <div x-ref="donutChart" class="min-h-[260px] w-full"></div>
          </div>
          <div class="w-full min-w-0 space-y-4 sm:space-y-5 lg:flex-1">
            <template x-for="(row, di) in (state.deviceBreakdown || [])" :key="row.label">
              <div class="flex items-center justify-between gap-3 border-b border-[#0a1628]/05 pb-4 last:border-0 last:pb-0">
                <div class="flex min-w-0 items-center gap-3">
                  <span class="h-2.5 w-2.5 shrink-0 rounded-full shadow-sm" :class="deviceDotClass(di)"></span>
                  <span class="text-sm font-medium text-[#1a1d21]" x-text="row.label"></span>
                </div>
                <span class="shrink-0 text-sm font-bold tabular-nums text-[#061018]" x-text="(row.percentage || 0) + '%'"></span>
              </div>
            </template>
          </div>
        </div>
      </div>

      <div class="pb-analytics-card rounded-2xl p-6 md:p-8">
        <h3 class="mb-8 text-xs font-bold uppercase tracking-[0.18em] text-on-surface-variant">{{ __('Referrer Performance') }}</h3>
        <div x-ref="referrerChart" class="min-h-[220px] w-full"></div>
        <p class="mt-4 text-center text-sm text-on-surface-variant" x-show="!state.topReferrers || !state.topReferrers.length" x-cloak>{{ __('No referrer data in this range yet') }}</p>
      </div>
    </section>

    <section class="pb-analytics-card mb-10 overflow-hidden rounded-2xl">
      <div class="border-b border-[#0a1628]/06 bg-gradient-to-r from-[#f8fafc] to-white px-6 py-6 md:px-8">
        <h3 class="text-lg font-bold tracking-tight text-[#061018]">{{ __('Most Visited Pages') }}</h3>
        <p class="mt-1.5 text-sm text-on-surface-variant">{{ __('Listing performance and viewer conversion') }}</p>
      </div>
      <div class="px-4 pb-6 pt-2 md:px-6">
        <ul class="lg:hidden space-y-2 rounded-xl border border-[#0a1628]/06 bg-white/50 p-3">
          <template x-for="row in (state.topPages || []).slice(0, 10)" :key="row.path">
            <li class="flex items-center justify-between gap-3 border-b border-[#0a1628]/06 py-3 last:border-0">
              <span class="min-w-0 flex-1 truncate text-sm font-medium text-[#1a1d21]" x-text="pathTitle(row)"></span>
              <span class="shrink-0 text-sm font-semibold tabular-nums text-[#061018]" x-text="num(row.views)"></span>
            </li>
          </template>
          <li class="py-4 text-center text-sm text-on-surface-variant" x-show="!(state.topPages && state.topPages.length)">{{ __('No page data in this range yet') }}</li>
        </ul>

        <div class="hidden lg:block overflow-x-auto rounded-xl border border-[#0a1628]/06 bg-white/50">
          <table class="w-full min-w-[600px] border-collapse text-left">
            <thead>
              <tr class="pb-admin-table border-b border-outline-variant/30 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                <th class="px-5 py-4 md:px-6">{{ __('Page title') }}</th>
                <th class="px-5 py-4 md:px-6">{{ __('Views') }}</th>
                <th class="px-5 py-4 md:px-6">{{ __('Avg. time') }}</th>
                <th class="px-5 py-4 md:px-6">{{ __('Bounce rate') }}</th>
                <th class="px-5 py-4 md:px-6">{{ __('Performance') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#0a1628]/06">
              <template x-for="row in (state.topPages || [])" :key="row.path">
                <tr class="transition-colors duration-150 hover:bg-surface-container-low">
                  <td class="px-5 py-4 text-sm font-medium text-[#1a1d21] md:px-6 md:py-5" x-text="pathTitle(row)"></td>
                  <td class="px-5 py-4 text-sm tabular-nums text-[#1a1d21] md:px-6 md:py-5" x-text="num(row.views)"></td>
                  <td class="px-5 py-4 text-sm text-on-surface-variant md:px-6 md:py-5">—</td>
                  <td class="px-5 py-4 text-sm tabular-nums text-[#1a1d21] md:px-6 md:py-5" x-text="bounceProxy(row) + '%'"></td>
                  <td class="px-5 py-4 md:px-6 md:py-5">
                    <div class="flex min-w-[10rem] items-center gap-3 sm:min-w-0">
                      <div class="pb-analytics-bar-track h-2 w-28 shrink-0 overflow-hidden rounded-full">
                        <div class="h-full rounded-full shadow-sm transition-all duration-500" :class="perfBarClass(performanceScore(row))" :style="`width: ${performanceScore(row)}%`"></div>
                      </div>
                      <span class="w-9 shrink-0 text-right text-xs font-bold tabular-nums" :class="perfTextClass(performanceScore(row))" x-text="performanceScore(row) + '%'"></span>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        <p class="hidden px-2 py-6 text-center text-sm text-on-surface-variant lg:block" x-show="!(state.topPages && state.topPages.length)">{{ __('No page data in this range yet') }}</p>
      </div>
    </section>

    <section class="mb-10 grid grid-cols-1 gap-6 lg:grid-cols-2">
      <div class="pb-analytics-card rounded-2xl p-6 md:p-8">
        <h3 class="mb-6 text-xs font-bold uppercase tracking-[0.18em] text-on-surface-variant">{{ __('Top Academy Pages') }}</h3>
        <div x-ref="listingsChart" class="min-h-[260px] w-full"></div>
        <p class="mt-4 text-center text-sm text-on-surface-variant" x-show="!hasProgramViews()" x-cloak>{{ __('No route views in this range yet') }}</p>
      </div>

      <div class="pb-analytics-card flex flex-col rounded-2xl p-6 md:p-8">
        <h3 class="mb-4 w-full text-left text-xs font-bold uppercase tracking-[0.18em] text-on-surface-variant">{{ __('User Engagement Ratio') }}</h3>
        <div class="flex w-full flex-col items-center justify-center overflow-hidden">
          <div x-ref="gaugeChart" class="min-h-[240px] w-full max-w-[280px]"></div>
        </div>
        <p class="mt-6 max-w-sm text-center text-xs leading-relaxed text-on-surface-variant sm:mx-auto">
          <span x-text="engagementLabel()"></span>
          <span class="font-semibold text-on-tertiary-container" x-text="engagementRatio() + '%'"></span>
          {{ __('engagement score based on bounce rate and session depth.') }}
        </p>
      </div>
    </section>

    <section class="pb-analytics-card mb-10 rounded-2xl p-6 md:p-8">
      <div class="mb-8 flex flex-col items-start justify-between gap-5 md:flex-row md:items-center">
        <h3 class="text-lg font-bold tracking-tight text-[#061018]">{{ __('Daily Activity: Views vs Sessions') }}</h3>
        <div class="flex flex-wrap gap-5">
          <div class="flex items-center gap-2 rounded-full border border-[#0a1628]/08 bg-white/70 px-3 py-1.5">
            <div class="h-2.5 w-2.5 rounded-sm bg-gradient-to-br from-primary-container to-[#152a45]"></div>
            <span class="text-xs font-medium text-on-surface-variant">{{ __('Views') }}</span>
          </div>
          <div class="flex items-center gap-2 rounded-full border border-[#0a1628]/08 bg-white/70 px-3 py-1.5">
            <div class="h-2.5 w-2.5 rounded-sm bg-gradient-to-br from-on-tertiary-container/40 to-on-tertiary-container/25"></div>
            <span class="text-xs font-medium text-on-surface-variant">{{ __('Sessions') }}</span>
          </div>
        </div>
      </div>
      <div x-ref="dailyChart" class="min-h-[280px] w-full px-1 sm:px-3"></div>
    </section>
  </x-admin.page-content>

</x-app-layout>

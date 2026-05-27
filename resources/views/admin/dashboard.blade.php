<x-app-layout>
  @php
    $traffic = $analyticsSummary ?? [];
    $audit = $auditSummary ?? [];
    $totalOrders = (int) ($stats['orders_count'] ?? 0);
    $paidOrders = (int) ($stats['paid_orders_count'] ?? 0);
    $activeProducts = (int) ($stats['approved_listings'] ?? 0);
    $revenue = (int) ($stats['revenue_paid_total'] ?? 0);
    $visitors = (int) ($stats['visitors_total'] ?? ($traffic['total_views'] ?? 0));
  @endphp

  <header class="flex justify-between items-center px-4 md:px-6 py-3 border-b border-wp-border sticky top-0 bg-white z-40 shrink-0">
    <h2 class="text-lg font-semibold text-wp-text">{{ __('Dashboard') }}</h2>
    <div class="flex items-center gap-4">
      <button type="button" class="text-wp-text-muted hover:text-wp-link transition-colors inline-flex items-center" aria-label="{{ __('Notifications') }}">
        <x-icon name="bell" class="w-5 h-5" />
      </button>
      <span class="text-xs text-wp-text-muted hidden sm:inline">{{ now()->format('M j, Y') }}</span>
    </div>
  </header>

  <div class="py-5 md:py-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 mb-6">
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[7rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total Orders') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($totalOrders) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ number_format($paidOrders) }} {{ __('paid') }}</span>
        </div>
      </div>

      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[7rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Active Products') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($activeProducts) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ __('live') }}</span>
        </div>
      </div>

      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[7rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total Revenue') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ format_currency($revenue) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ __('paid') }}</span>
        </div>
      </div>

      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[7rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Page Visitors') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($visitors) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ __('last :days days', ['days' => (int) ($traffic['range_days'] ?? 90)]) }}</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="lg:col-span-2 bg-white border border-wp-border rounded">
        <div class="p-4 border-b border-wp-border flex justify-between items-center">
          <h4 class="text-sm font-semibold text-wp-text">{{ __('Recent Admin Activity') }}</h4>
          <a href="{{ route('admin.audit.index') }}" class="text-xs text-wp-link hover:text-wp-link-hover transition-colors inline-flex items-center gap-1">
            {{ __('View all') }}
            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-wp-bg">
                <th class="px-4 py-2 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('When') }}</th>
                <th class="px-4 py-2 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Admin') }}</th>
                <th class="px-4 py-2 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Method') }}</th>
                <th class="px-4 py-2 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Path') }}</th>
                <th class="px-4 py-2 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border text-right">{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse (($audit['recent'] ?? []) as $entry)
                <tr class="hover:bg-wp-bg transition-colors">
                  <td class="px-4 py-3 border-b border-wp-border text-wp-text-muted whitespace-nowrap">{{ optional($entry->created_at)->format('M j, Y') }}</td>
                  <td class="px-4 py-3 border-b border-wp-border">{{ $entry->user?->name ?? __('Unknown') }}</td>
                  <td class="px-4 py-3 border-b border-wp-border">
                    <span class="inline-block px-2 py-0.5 bg-wp-bg text-[10px] font-semibold tracking-wide uppercase rounded">{{ $entry->method }}</span>
                  </td>
                  <td class="px-4 py-3 border-b border-wp-border max-w-xs truncate" title="{{ $entry->path }}">{{ $entry->path }}</td>
                  <td class="px-4 py-3 border-b border-wp-border text-right font-medium">{{ $entry->status_code ?? '—' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-4 py-8 text-center text-wp-text-muted">{{ __('No audit actions yet.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white border border-wp-border rounded p-4">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h4 class="text-sm font-semibold text-wp-text leading-tight">{{ __('Site Traffic') }}</h4>
            <p class="text-[10px] uppercase tracking-wide text-wp-text-muted mt-0.5">{{ __('Last :days days', ['days' => (int) ($traffic['range_days'] ?? 90)]) }}</p>
          </div>
          <a href="{{ route('admin.analytics.index') }}" class="text-wp-text-muted hover:text-wp-link inline-flex items-center" aria-label="{{ __('Open analytics') }}">
            <x-icon name="arrow-right" class="w-4 h-4" />
          </a>
        </div>
        <div class="space-y-3 text-sm">
          <div>
            <p class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total views') }}</p>
            <p class="text-lg font-semibold text-wp-text mt-0.5">{{ number_format((int) ($traffic['total_views'] ?? 0)) }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Unique sessions') }}</p>
            <p class="text-lg font-semibold text-wp-text mt-0.5">{{ number_format((int) ($traffic['unique_sessions'] ?? 0)) }}</p>
          </div>
          <div class="pt-3 border-t border-wp-border">
            <p class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Top page') }}</p>
            <p class="text-sm mt-0.5">{{ $traffic['top_page_label'] ?? __('No data yet') }}</p>
          </div>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-2">
          <a href="{{ route('admin.orders.index') }}" class="bg-black text-white border border-black hover:opacity-90 px-4 py-3 text-xs font-medium rounded text-center transition-opacity">{{ __('Manage orders') }}</a>
          <a href="{{ route('dashboard.vehicles.index') }}" class="bg-black text-white border border-black hover:opacity-90 px-4 py-3 text-xs font-medium rounded text-center transition-opacity">{{ __('Product management') }}</a>
          <a href="{{ route('shop.index') }}" target="_blank" rel="noopener" class="bg-black text-white border border-black hover:opacity-90 px-4 py-3 text-xs font-medium rounded text-center transition-opacity">{{ __('View shop') }}</a>
        </div>
      </div>
    </div>

    @include('admin.partials.luxe-footer', ['footerClass' => 'mt-8 border-t-0 opacity-60'])
  </div>
</x-app-layout>

<x-app-layout>
  @php
    $traffic = $analyticsSummary ?? [];
    $audit = $auditSummary ?? [];
    $totalOrders = (int) ($stats['orders_count'] ?? 0);
    $paidOrders = (int) ($stats['paid_orders_count'] ?? 0);
    $activeProducts = (int) ($stats['approved_listings'] ?? 0);
    $ordersPct = $totalOrders > 0 ? min(100, (int) round(($paidOrders / $totalOrders) * 100)) : 0;
    $productsPct = ($stats['total_listings'] ?? 0) > 0 ? min(100, (int) round(($activeProducts / max(1, $stats['total_listings'])) * 100)) : 0;
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

  <div class="px-margin-mobile md:px-gutter py-8 md:py-0 md:pb-gutter max-w-max-container mx-auto w-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
      <div class="bg-surface-container-lowest p-8 border border-outline-variant flex flex-col justify-between hover:scale-[1.02] transition-transform duration-300">
        <div>
          <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ __('Total Orders') }}</span>
          <div class="flex items-baseline gap-3 mt-4">
            <h3 class="font-display-lg text-[40px] leading-tight text-primary">{{ number_format($totalOrders) }}</h3>
            <span class="text-secondary font-body-md text-sm font-medium">{{ number_format($paidOrders) }} {{ __('paid') }}</span>
          </div>
        </div>
        <div class="mt-8 h-1 bg-surface-container">
          <div class="h-full bg-primary" style="width: {{ $ordersPct }}%"></div>
        </div>
      </div>

      <div class="bg-surface-container-lowest p-8 border border-outline-variant flex flex-col justify-between hover:scale-[1.02] transition-transform duration-300">
        <div>
          <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ __('Active Products') }}</span>
          <div class="flex items-baseline gap-3 mt-4">
            <h3 class="font-display-lg text-[40px] leading-tight text-primary">{{ number_format($activeProducts) }}</h3>
            <span class="text-on-surface-variant font-body-md text-sm">{{ __('live') }}</span>
          </div>
        </div>
        <div class="mt-8 h-1 bg-surface-container">
          <div class="h-full bg-primary" style="width: {{ $productsPct }}%"></div>
        </div>
      </div>

      <div class="bg-surface-container-lowest p-8 border border-outline-variant flex flex-col justify-between hover:scale-[1.02] transition-transform duration-300">
        <div>
          <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ __('Pending Review') }}</span>
          <div class="flex items-baseline gap-3 mt-4">
            <h3 class="font-display-lg text-[40px] leading-tight text-primary">{{ number_format((int) ($stats['pending_listings'] ?? 0)) }}</h3>
            <span class="text-on-surface-variant font-body-md text-sm">{{ __('products') }}</span>
          </div>
        </div>
        <div class="mt-8 h-1 bg-surface-container">
          <div class="h-full" style="width: {{ ($stats['total_listings'] ?? 0) > 0 ? min(100, (int) round((($stats['pending_listings'] ?? 0) / max(1, $stats['total_listings'])) * 100)) : 0 }}%; background-color: #C19A6B;"></div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant">
        <div class="p-8 border-b border-outline-variant flex justify-between items-center">
          <h4 class="font-headline-md text-headline-md">{{ __('Recent Admin Activity') }}</h4>
          <a href="{{ route('admin.audit.index') }}" class="text-xs text-wp-link hover:text-wp-link-hover transition-colors inline-flex items-center gap-1">
            {{ __('View all') }}
            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low">
                <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('WHEN') }}</th>
                <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('ADMIN') }}</th>
                <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('METHOD') }}</th>
                <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('PATH') }}</th>
                <th class="px-8 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant text-right">{{ __('STATUS') }}</th>
              </tr>
            </thead>
            <tbody class="font-body-md text-body-md">
              @forelse (($audit['recent'] ?? []) as $entry)
                <tr class="hover:bg-surface-container transition-colors">
                  <td class="px-8 py-6 border-b border-outline-variant text-on-surface-variant">{{ optional($entry->created_at)->format('M j, Y') }}</td>
                  <td class="px-8 py-6 border-b border-outline-variant">{{ $entry->user?->name ?? __('Unknown') }}</td>
                  <td class="px-8 py-6 border-b border-outline-variant">
                    <span class="inline-block px-2 py-1 bg-surface-container text-[10px] font-bold tracking-widest uppercase">{{ $entry->method }}</span>
                  </td>
                  <td class="px-8 py-6 border-b border-outline-variant max-w-xs truncate" title="{{ $entry->path }}">{{ $entry->path }}</td>
                  <td class="px-8 py-6 border-b border-outline-variant text-right font-medium">{{ $entry->status_code ?? '—' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-8 py-12 text-center text-on-surface-variant">{{ __('No audit actions yet.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-surface-container-lowest border border-outline-variant p-8">
        <div class="flex justify-between items-start mb-10">
          <div>
            <h4 class="font-headline-md text-headline-md leading-tight">{{ __('Site Traffic') }}</h4>
            <p class="font-label-caps text-[10px] text-on-surface-variant mt-1">{{ __('LAST :days DAYS', ['days' => $traffic['range_days'] ?? 90]) }}</p>
          </div>
          <a href="{{ route('admin.analytics.index') }}" class="text-wp-text-muted hover:text-wp-link inline-flex items-center" aria-label="{{ __('Open analytics') }}">
            <x-icon name="arrow-right" class="w-4 h-4" />
          </a>
        </div>
        <div class="space-y-6">
          <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ __('Total views') }}</p>
            <p class="font-headline-md text-primary mt-1">{{ number_format((int) ($traffic['total_views'] ?? 0)) }}</p>
          </div>
          <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ __('Unique sessions') }}</p>
            <p class="font-headline-md text-primary mt-1">{{ number_format((int) ($traffic['unique_sessions'] ?? 0)) }}</p>
          </div>
          <div class="pt-8 border-t border-outline-variant">
            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ __('Top page') }}</p>
            <p class="font-body-md text-sm mt-1">{{ $traffic['top_page_label'] ?? __('No data yet') }}</p>
          </div>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-3">
          <a href="{{ route('admin.orders.index') }}" class="border border-outline-variant px-4 py-3 font-label-caps text-[10px] tracking-widest uppercase hover:border-primary transition-colors">{{ __('Manage orders') }}</a>
          <a href="{{ route('dashboard.vehicles.index') }}" class="border border-outline-variant px-4 py-3 font-label-caps text-[10px] tracking-widest uppercase hover:border-primary transition-colors">{{ __('Product management') }}</a>
          <a href="{{ route('shop.index') }}" target="_blank" rel="noopener" class="border border-outline-variant px-4 py-3 font-label-caps text-[10px] tracking-widest uppercase hover:border-primary transition-colors">{{ __('View shop') }}</a>
        </div>
      </div>
    </div>

    @include('admin.partials.luxe-footer', ['footerClass' => 'mt-16 border-t-0 opacity-60'])
  </div>
</x-app-layout>

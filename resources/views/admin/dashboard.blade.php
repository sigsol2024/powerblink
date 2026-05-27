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

  <x-admin.page-header :title="__('Dashboard')">
    <x-slot name="actions">
      <span class="text-xs text-wp-text-muted hidden sm:inline">{{ now()->format('M j, Y') }}</span>
    </x-slot>
  </x-admin.page-header>

  <x-admin.page-content>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total Orders') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($totalOrders) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ number_format($paidOrders) }} {{ __('paid') }}</span>
        </div>
      </x-admin.card>
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Active Products') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($activeProducts) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ __('live') }}</span>
        </div>
      </x-admin.card>
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total Revenue') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ format_currency($revenue) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ __('paid') }}</span>
        </div>
      </x-admin.card>
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Page Visitors') }}</span>
        <div class="flex items-baseline gap-2">
          <h3 class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($visitors) }}</h3>
          <span class="text-xs text-wp-text-muted">{{ __('last :days days', ['days' => (int) ($traffic['range_days'] ?? 90)]) }}</span>
        </div>
      </x-admin.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <x-admin.card variant="table" class="lg:col-span-2">
        <div class="p-4 border-b border-wp-border flex justify-between items-center">
          <h4 class="text-sm font-semibold text-wp-text">{{ __('Recent orders') }}</h4>
          <a href="{{ route('admin.orders.index') }}" class="text-xs text-wp-link hover:text-wp-link-hover transition-colors inline-flex items-center gap-1">
            {{ __('View all') }}
            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
          </a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm admin-luxe-table">
            <thead>
              <tr class="bg-wp-bg">
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Date') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Order') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Product') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border text-right">{{ __('Qty') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border text-right">{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse (($recentOrders ?? []) as $order)
                @php
                  $items = $order->items ?? collect();
                  $firstItem = $items->first();
                  $primaryName = (string) ($firstItem->name ?? $firstItem?->vehicle?->title ?? __('—'));
                  $qtyTotal = (int) ($items->sum('qty') ?? 0);
                @endphp
                <tr class="hover:bg-wp-bg transition-colors">
                  <td class="px-4 py-3 border-b border-wp-border text-wp-text-muted whitespace-nowrap">{{ optional($order->created_at)->format('M j, Y') }}</td>
                  <td class="px-4 py-3 border-b border-wp-border font-medium">
                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->order_number ?? ('#'.$order->id) }}</a>
                  </td>
                  <td class="px-4 py-3 border-b border-wp-border max-w-xs truncate" title="{{ $primaryName }}">{{ $primaryName }}</td>
                  <td class="px-4 py-3 border-b border-wp-border text-right tabular-nums">{{ number_format($qtyTotal) }}</td>
                  <td class="px-4 py-3 border-b border-wp-border text-right">
                    <x-admin.status-pill :variant="(string) ($order->status ?? 'neutral')">{{ $order->status ?? '—' }}</x-admin.status-pill>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5"><x-admin.empty-state :title="__('No orders yet.')" /></td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-admin.card>

      <x-admin.card class="p-4">
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
          <x-admin.button variant="primary" :href="route('admin.orders.index')" class="w-full">{{ __('Manage orders') }}</x-admin.button>
          <x-admin.button variant="primary" :href="route('dashboard.vehicles.index')" class="w-full">{{ __('Product management') }}</x-admin.button>
          <x-admin.button variant="primary" :href="route('shop.index')" target="_blank" rel="noopener" class="w-full">{{ __('View shop') }}</x-admin.button>
        </div>
      </x-admin.card>
    </div>

    @include('admin.partials.luxe-footer', ['footerClass' => 'mt-4 border-t-0 opacity-60'])
  </x-admin.page-content>
</x-app-layout>

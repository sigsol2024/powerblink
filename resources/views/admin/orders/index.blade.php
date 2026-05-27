<x-app-layout>
  @php
    $statusTabs = ['', 'pending_payment', 'paid', 'fulfilled', 'failed', 'cancelled', 'refunded'];
    $totalShown = $orders->total();
    $statusLabels = [
      '' => __('All'),
      'pending_payment' => __('Pending payment'),
      'paid' => __('Paid'),
      'fulfilled' => __('Fulfilled'),
      'failed' => __('Failed'),
      'cancelled' => __('Cancelled'),
      'refunded' => __('Refunded'),
    ];
  @endphp

  @php
    $statusCounts = $statusCounts ?? ['all' => 0];
    $paidCount = (int) ($statusCounts['paid'] ?? 0);
    $pendingCount = (int) ($statusCounts['pending_payment'] ?? 0);
  @endphp

  <div
    class="flex flex-col"
    x-data="{
      currentStatus: @js((string) ($status ?? '')),
      q: '',
      expandedMobileId: null,
      statusCounts: @js($statusCounts),
      toggleMobile(id) { this.expandedMobileId = this.expandedMobileId === id ? null : id; },
      countFor(st) {
        if (st === '') return this.statusCounts.all ?? 0;
        return this.statusCounts[st] ?? 0;
      },
      visible(rowStatus, haystack) {
        const statusOk = (!this.currentStatus || this.currentStatus === rowStatus);
        const query = this.q.trim().toLowerCase();
        const searchOk = !query || haystack.toLowerCase().includes(query);
        return statusOk && searchOk;
      },
    }"
  >
    <x-admin.page-header :title="__('Orders')" :count="trans_choice(':count order|:count orders', $totalShown, ['count' => number_format($totalShown)])" />

    <x-admin.page-content>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <x-admin.card variant="stats">
          <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total orders') }}</span>
          <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format((int) ($statusCounts['all'] ?? 0)) }}</span>
        </x-admin.card>
        <x-admin.card variant="stats">
          <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Paid') }}</span>
          <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($paidCount) }}</span>
        </x-admin.card>
        <x-admin.card variant="stats">
          <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Pending payment') }}</span>
          <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($pendingCount) }}</span>
        </x-admin.card>
      </div>

      <x-admin.card variant="toolbar">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <div class="relative flex-1 sm:flex-initial">
            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-wp-text-muted pointer-events-none"><x-icon name="search" class="w-4 h-4" /></span>
            <input type="search" x-model.debounce.250ms="q" class="w-full sm:w-72 pl-9 pr-3 py-2 text-sm border border-wp-border rounded" placeholder="{{ __('Search orders…') }}" aria-label="{{ __('Search orders') }}" />
          </div>
        </div>
        <div class="flex flex-wrap items-center gap-1.5">
          <span class="text-[11px] text-wp-text-muted mr-1">{{ __('Filter') }}:</span>
          @foreach ($statusTabs as $st)
            <button
              type="button"
              @click="currentStatus = '{{ $st }}'; history.replaceState(null, '', currentStatus ? '?status=' + currentStatus : location.pathname);"
              :class="currentStatus === '{{ $st }}' ? 'bg-wp-link text-white border-wp-link' : 'border-wp-border bg-white hover:bg-wp-bg text-wp-text'"
              class="border px-2.5 py-1 text-xs rounded transition-colors whitespace-nowrap"
            >
              {{ $statusLabels[$st] ?? ucfirst(str_replace('_', ' ', $st)) }} (<span x-text="countFor('{{ $st }}')"></span>)
            </button>
          @endforeach
        </div>
      </x-admin.card>

      {{-- Desktop table --}}
      <div class="hidden lg:block bg-white border border-wp-border overflow-hidden rounded">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-wp-bg">
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Order ID') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Customer') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Date') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Total') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border">{{ __('Status') }}</th>
                <th class="px-4 py-2.5 text-xs uppercase tracking-wide text-wp-text-muted border-b border-wp-border text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($orders as $order)
                @php
                  $initials = collect(explode(' ', (string) $order->customer_name))->filter()->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                  $haystack = strtolower(trim($order->order_number.' '.$order->customer_name.' '.$order->customer_email));
                @endphp
                <tr
                  class="hover:bg-wp-bg/40 transition-colors group"
                  x-show="visible('{{ $order->status }}', @js($haystack))"
                >
                  <td class="px-4 py-3 border-b border-wp-border font-medium whitespace-nowrap">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-wp-link hover:text-wp-link-hover">#{{ $order->order_number }}</a>
                  </td>
                  <td class="px-4 py-3 border-b border-wp-border">
                    <div class="flex items-center gap-3 min-w-0">
                      <div class="w-8 h-8 bg-wp-bg rounded-full flex items-center justify-center font-bold text-xs shrink-0">{{ $initials ?: '?' }}</div>
                      <div class="min-w-0">
                        <span class="text-sm block truncate text-wp-text">{{ $order->customer_name }}</span>
                        <span class="text-xs text-wp-text-muted truncate block">{{ $order->customer_email }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 border-b border-wp-border text-wp-text-muted whitespace-nowrap">{{ $order->created_at?->format('M j, Y') }}</td>
                  <td class="px-4 py-3 border-b border-wp-border font-semibold whitespace-nowrap">{{ \App\Support\Money::formatKobo($order->total) }}</td>
                  <td class="px-4 py-3 border-b border-wp-border">
                    @include('admin.partials.order-status-badge', ['status' => $order->status])
                  </td>
                  <td class="px-4 py-3 border-b border-wp-border text-right">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-wp-link hover:text-wp-link-hover transition-colors inline-flex items-center gap-1 text-xs" title="{{ __('View order') }}">
                      <x-icon name="eye" class="w-4 h-4" /> {{ __('View') }}
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-4 py-16 text-center text-wp-text-muted">{{ __('No orders yet.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($orders->hasPages())
          <div class="px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-wp-border admin-luxe-pagination">
            <p class="text-xs text-wp-text-muted">
              {{ __('Showing :from to :to of :total results', [
                'from' => $orders->firstItem() ?? 0,
                'to' => $orders->lastItem() ?? 0,
                'total' => $orders->total(),
              ]) }}
            </p>
            {{ $orders->links() }}
          </div>
        @endif
      </div>

      {{-- Mobile accordion --}}
      <div class="lg:hidden space-y-2">
        @forelse ($orders as $order)
          @php
            $initials = collect(explode(' ', (string) $order->customer_name))->filter()->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
            $haystack = strtolower(trim($order->order_number.' '.$order->customer_name.' '.$order->customer_email));
          @endphp
          <div
            class="bg-white border border-wp-border rounded overflow-hidden"
            x-show="visible('{{ $order->status }}', @js($haystack))"
          >
            <button
              type="button"
              class="w-full flex items-center gap-3 p-3 text-left"
              @click="toggleMobile({{ $order->id }})"
              :aria-expanded="expandedMobileId === {{ $order->id }} ? 'true' : 'false'"
            >
              <div class="w-10 h-10 bg-wp-bg rounded-full flex items-center justify-center font-bold text-xs shrink-0">{{ $initials ?: '?' }}</div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                  <span class="font-medium text-wp-text text-sm truncate">#{{ $order->order_number }}</span>
                  <span class="font-semibold text-sm whitespace-nowrap">{{ \App\Support\Money::formatKobo($order->total) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2 mt-0.5">
                  <span class="text-xs text-wp-text-muted truncate">{{ $order->customer_name }}</span>
                  @include('admin.partials.order-status-badge', ['status' => $order->status])
                </div>
              </div>
              <svg :class="expandedMobileId === {{ $order->id }} ? 'rotate-180' : ''" class="w-4 h-4 transition-transform text-wp-text-muted shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div
              x-show="expandedMobileId === {{ $order->id }}"
              x-cloak
              x-transition.duration.150ms
              class="border-t border-wp-border bg-wp-bg/40 p-3 space-y-2 text-sm"
            >
              <div class="flex justify-between gap-3">
                <span class="text-xs text-wp-text-muted">{{ __('Email') }}</span>
                <span class="text-wp-text text-right truncate">{{ $order->customer_email }}</span>
              </div>
              <div class="flex justify-between gap-3">
                <span class="text-xs text-wp-text-muted">{{ __('Date') }}</span>
                <span class="text-wp-text text-right">{{ $order->created_at?->format('M j, Y') }}</span>
              </div>
              <div class="pt-2">
                <a href="{{ route('admin.orders.show', $order) }}" class="block w-full text-center bg-black text-white border border-black hover:opacity-90 px-3 py-2 text-xs font-medium rounded transition-opacity">
                  {{ __('View order') }}
                </a>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center text-wp-text-muted py-8 text-sm">{{ __('No orders yet.') }}</p>
        @endforelse

        @if ($orders->hasPages())
          <div class="py-2 admin-luxe-pagination">{{ $orders->links() }}</div>
        @endif
      </div>

    </x-admin.page-content>

    @include('admin.partials.luxe-footer')
  </div>
</x-app-layout>

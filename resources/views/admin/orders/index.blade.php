<x-app-layout>
  @php
    $statusTabs = ['', 'pending_payment', 'paid', 'fulfilled', 'failed', 'cancelled', 'refunded'];
    $totalShown = $orders->total();
  @endphp

  <main class="flex-1 flex flex-col h-full min-h-0 overflow-hidden relative bg-wp-bg">
    <header class="flex flex-col sm:flex-row sm:items-center justify-between px-4 md:px-6 py-3 border-b border-wp-border bg-white sticky top-0 z-40 shrink-0 gap-3">
      <div class="flex items-center gap-3 min-w-0">
        <h2 class="text-lg font-semibold text-wp-text">{{ __('Orders') }}</h2>
        <span class="text-xs text-wp-text-muted">{{ trans_choice(':count order|:count orders', $totalShown, ['count' => number_format($totalShown)]) }}</span>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <div class="relative hidden sm:block">
          <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-wp-text-muted pointer-events-none"><x-icon name="search" class="w-4 h-4" /></span>
          <input type="search" class="pl-8 pr-3 py-1.5 text-sm w-48 lg:w-64" placeholder="{{ __('Search orders…') }}" aria-label="{{ __('Search orders') }}" />
        </div>
        <button type="button" class="admin-luxe-btn-primary">
          <x-icon name="download" class="w-4 h-4" /> {{ __('Export CSV') }}
        </button>
      </div>
    </header>

    <section class="flex-1 overflow-y-auto px-4 md:px-6 py-4 md:py-5 max-w-max-container mx-auto w-full custom-scrollbar">
      <div class="flex flex-wrap items-center gap-1 mb-4 border-b border-wp-border">
        @foreach ($statusTabs as $st)
          <a
            href="{{ route('admin.orders.index', $st !== '' ? ['status' => $st] : []) }}"
            class="px-3 py-2 text-sm border-b-2 transition-colors {{ ($status ?? '') === $st ? 'border-wp-link text-wp-link font-medium' : 'border-transparent text-wp-text-muted hover:text-wp-text' }}"
          >
            {{ $st === '' ? __('All') : ucfirst(str_replace('_', ' ', $st)) }}
          </a>
        @endforeach
      </div>

      <div class="bg-white border border-wp-border overflow-hidden rounded">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-low">
                <th class="px-6 py-5 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('Order ID') }}</th>
                <th class="px-6 py-5 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('Customer Name') }}</th>
                <th class="px-6 py-5 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('Date') }}</th>
                <th class="px-6 py-5 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('Total Amount') }}</th>
                <th class="px-6 py-5 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">{{ __('Status') }}</th>
                <th class="px-6 py-5 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
              @forelse ($orders as $order)
                @php
                  $initials = collect(explode(' ', $order->customer_name))->filter()->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                @endphp
                <tr class="hover:bg-surface-container-lowest transition-colors group">
                  <td class="px-6 py-6 font-body-md text-sm font-medium">
                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:text-secondary">#{{ $order->order_number }}</a>
                  </td>
                  <td class="px-6 py-6">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 bg-surface-container-highest flex items-center justify-center font-bold text-xs shrink-0">{{ $initials ?: '?' }}</div>
                      <div class="min-w-0">
                        <span class="font-body-md text-sm block truncate">{{ $order->customer_name }}</span>
                        <span class="text-xs text-on-surface-variant truncate block">{{ $order->customer_email }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-6 font-body-md text-sm text-on-surface-variant whitespace-nowrap">{{ $order->created_at?->format('M j, Y') }}</td>
                  <td class="px-6 py-6 font-body-md text-sm font-semibold whitespace-nowrap">{{ \App\Support\Money::formatKobo($order->total) }}</td>
                  <td class="px-6 py-6">
                    @include('admin.partials.order-status-badge', ['status' => $order->status])
                  </td>
                  <td class="px-6 py-6 text-right">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-wp-link hover:text-wp-link-hover transition-colors inline-flex items-center gap-1 text-sm" title="{{ __('View order') }}">
                      <x-icon name="eye" class="w-4 h-4" /> {{ __('View') }}
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-6 py-16 text-center text-on-surface-variant font-body-md">{{ __('No orders yet.') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if ($orders->hasPages())
          <div class="px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-outline-variant admin-luxe-pagination">
            <p class="font-body-md text-xs text-on-surface-variant">
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

      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-white border border-wp-border rounded">
          <p class="text-xs text-wp-text-muted">{{ __('Total orders') }}</p>
          <h4 class="text-2xl font-semibold mt-1 text-wp-text">{{ number_format($totalShown) }}</h4>
        </div>
        <div class="p-4 bg-white border border-wp-border rounded">
          <p class="text-xs text-wp-text-muted">{{ __('Current filter') }}</p>
          <h4 class="text-base font-semibold mt-1 text-wp-text">{{ ($status ?? '') !== '' ? ucfirst(str_replace('_', ' ', $status)) : __('All statuses') }}</h4>
        </div>
        <div class="p-4 bg-white border border-wp-border rounded flex items-start gap-3">
          <span class="text-wp-link"><x-icon name="shopping-bag" class="w-5 h-5" /></span>
          <div>
            <p class="text-xs text-wp-text-muted">{{ __('Quick link') }}</p>
            <a href="{{ route('dashboard.vehicles.index') }}" class="text-sm font-medium text-wp-link hover:text-wp-link-hover">{{ __('Manage products') }}</a>
          </div>
        </div>
      </div>
    </section>

    @include('admin.partials.luxe-footer')
  </main>
</x-app-layout>

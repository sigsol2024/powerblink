<x-app-layout>
  @php
    $statusTabs = ['', 'pending_payment', 'paid', 'fulfilled', 'failed', 'cancelled', 'refunded'];
    $totalShown = $orders->total();
  @endphp

  <main class="flex-1 flex flex-col h-full min-h-0 overflow-hidden luxe-pattern-bg relative">
    <header class="h-20 flex items-center justify-between px-margin-mobile md:px-gutter border-b border-outline-variant bg-white/80 backdrop-blur-md sticky top-0 z-40 shrink-0 gap-4">
      <div class="flex items-center gap-4 min-w-0">
        <h2 class="font-headline-md text-headline-lg-mobile md:text-headline-md tracking-tight truncate">{{ __('Order Management') }}</h2>
        <div class="h-4 w-px bg-outline-variant hidden md:block shrink-0"></div>
        <p class="text-on-surface-variant text-sm font-body-md hidden md:block">{{ trans_choice(':count order processed|:count orders processed', $totalShown, ['count' => number_format($totalShown)]) }}</p>
      </div>
      <div class="flex items-center gap-3 shrink-0">
        <div class="relative hidden sm:block">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
          <input type="search" class="pl-10 pr-4 py-2 bg-surface-container-low border-none focus:ring-1 focus:ring-primary font-body-md text-sm w-48 lg:w-64 transition-all" placeholder="{{ __('Search orders...') }}" aria-label="{{ __('Search orders') }}" />
        </div>
        <button type="button" class="bg-primary text-on-primary px-4 md:px-6 py-2.5 font-button-text text-button-text uppercase tracking-widest hover:scale-105 transition-transform text-xs md:text-sm whitespace-nowrap">
          {{ __('Export CSV') }}
        </button>
      </div>
    </header>

    <section class="flex-1 overflow-y-auto px-margin-mobile md:px-gutter py-8 md:py-10 max-w-max-container mx-auto w-full custom-scrollbar">
      <div class="flex flex-wrap items-center gap-2 mb-8 border-b border-outline-variant">
        @foreach ($statusTabs as $st)
          <a
            href="{{ route('admin.orders.index', $st !== '' ? ['status' => $st] : []) }}"
            class="px-6 py-3 font-label-caps text-label-caps border-b-2 tracking-widest transition-all {{ ($status ?? '') === $st ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-primary' }}"
          >
            {{ $st === '' ? __('ALL') : strtoupper(str_replace('_', ' ', $st)) }}
          </a>
        @endforeach
      </div>

      <div class="bg-white border border-outline-variant overflow-hidden">
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
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-on-surface-variant hover:text-primary transition-colors inline-flex" title="{{ __('View order') }}">
                      <span class="material-symbols-outlined">visibility</span>
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

      <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-8 bg-surface-container-lowest border border-outline-variant flex flex-col justify-between min-h-[12rem]">
          <p class="font-label-caps text-label-caps text-on-surface-variant">{{ __('TOTAL ORDERS') }}</p>
          <h4 class="font-headline-lg text-headline-md mt-4">{{ number_format($totalShown) }}</h4>
        </div>
        <div class="p-8 bg-primary text-on-primary flex flex-col justify-between min-h-[12rem]">
          <p class="font-label-caps text-label-caps opacity-70">{{ __('FILTER') }}</p>
          <h4 class="font-headline-lg text-headline-md mt-4 uppercase">{{ ($status ?? '') !== '' ? str_replace('_', ' ', $status) : __('All statuses') }}</h4>
        </div>
        <div class="p-8 bg-surface-container-lowest border border-outline-variant flex flex-col justify-between min-h-[12rem] relative overflow-hidden">
          <p class="font-label-caps text-label-caps text-on-surface-variant z-10">{{ __('QUICK LINK') }}</p>
          <a href="{{ route('dashboard.vehicles.index') }}" class="font-headline-md text-headline-md mt-4 z-10 hover:opacity-80">{{ __('Products') }} →</a>
          <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-[120px] opacity-10">shopping_bag</span>
        </div>
      </div>
    </section>

    @include('admin.partials.luxe-footer')
  </main>
</x-app-layout>

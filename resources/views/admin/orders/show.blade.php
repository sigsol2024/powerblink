<x-app-layout>
  <header class="flex flex-wrap items-center justify-between gap-3 px-4 md:px-6 py-3 border-b border-wp-border bg-white sticky top-0 z-40">
    <div class="min-w-0">
      <p class="text-xs text-wp-text-muted">{{ __('Order details') }}</p>
      <h2 class="text-lg font-semibold text-wp-text truncate">#{{ $order->order_number }}</h2>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-wp-link hover:text-wp-link-hover flex items-center gap-1.5 shrink-0">
      <x-icon name="arrow-left" class="w-4 h-4" />
      {{ __('All orders') }}
    </a>
  </header>

  <div class="px-4 md:px-6 py-4 md:py-5">
    @if (session('status'))
      <div class="mb-8 border border-secondary/40 bg-secondary-fixed/30 px-4 py-3 text-sm text-on-surface">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-8">
        <div class="bg-surface-container-lowest border border-outline-variant">
          <div class="p-8 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md">{{ __('Line items') }}</h3>
          </div>
          <ul class="divide-y divide-outline-variant">
            @foreach ($order->items as $item)
              <li class="flex justify-between gap-6 px-8 py-6">
                <div class="min-w-0">
                  <p class="font-body-md font-bold text-primary uppercase">{{ $item->name }}</p>
                  <p class="text-on-surface-variant text-sm mt-1">× {{ $item->qty }} @if($item->sku)<span class="text-[10px] uppercase tracking-widest">· {{ $item->sku }}</span>@endif</p>
                </div>
                <p class="font-body-md font-semibold whitespace-nowrap">{{ \App\Support\Money::formatKobo($item->line_total) }}</p>
              </li>
            @endforeach
          </ul>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant p-8">
          <h3 class="font-headline-md text-headline-md mb-6">{{ __('Shipping address') }}</h3>
          <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
            {{ $order->customer_name }}<br>
            {{ $order->shipping_address_line1 }}<br>
            @if ($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
            {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif @if($order->shipping_postal_code){{ $order->shipping_postal_code }}@endif<br>
            {{ $order->shipping_country }}
          </p>
        </div>
      </div>

      <div class="space-y-8">
        <div class="bg-surface-container-lowest border border-outline-variant p-8">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-6">{{ __('ORDER SUMMARY') }}</h3>
          <dl class="space-y-4 font-body-md">
            <div class="flex justify-between border-b border-outline-variant pb-4">
              <dt class="text-on-surface-variant">{{ __('Subtotal') }}</dt>
              <dd class="font-medium">{{ \App\Support\Money::formatKobo($order->subtotal) }}</dd>
            </div>
            <div class="flex justify-between border-b border-outline-variant pb-4">
              <dt class="text-on-surface-variant">{{ __('Shipping') }}</dt>
              <dd class="font-medium">{{ \App\Support\Money::formatKobo($order->shipping) }}</dd>
            </div>
            <div class="flex justify-between pt-2">
              <dt class="font-label-caps text-label-caps">{{ __('Total') }}</dt>
              <dd class="font-headline-md text-headline-md">{{ \App\Support\Money::formatKobo($order->total) }}</dd>
            </div>
          </dl>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant p-8">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-4">{{ __('CUSTOMER') }}</h3>
          <p class="font-body-md font-semibold text-primary">{{ $order->customer_name }}</p>
          <p class="font-body-md text-on-surface-variant mt-1">{{ $order->customer_email }}</p>
          @if ($order->customer_phone)
            <p class="font-body-md text-on-surface-variant mt-1">{{ $order->customer_phone }}</p>
          @endif
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant p-8">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-4">{{ __('STATUS') }}</h3>
          <div class="mb-6">@include('admin.partials.order-status-badge', ['status' => $order->status])</div>
          <form method="post" action="{{ route('admin.orders.status', $order) }}" class="space-y-4">
            @csrf
            <select name="status" class="w-full bg-surface-container-low border-outline-variant border py-3 px-4 font-body-md text-sm focus:ring-0 focus:border-primary">
              @foreach (['pending_payment', 'paid', 'fulfilled', 'failed', 'cancelled', 'refunded'] as $st)
                <option value="{{ $st }}" @selected($order->status === $st)>{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
              @endforeach
            </select>
            <select name="delivery_status" class="w-full bg-surface-container-low border-outline-variant border py-3 px-4 font-body-md text-sm focus:ring-0 focus:border-primary">
              @foreach (['processing', 'packed', 'dispatched', 'in_transit', 'delivered', 'failed', 'returned', 'cancelled'] as $dst)
                <option value="{{ $dst }}" @selected(($order->delivery_status ?? 'processing') === $dst)>{{ ucfirst(str_replace('_', ' ', $dst)) }}</option>
              @endforeach
            </select>
            <button type="submit" class="admin-luxe-btn-primary w-full">{{ __('Save status') }}</button>
          </form>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant p-8">
          <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-4">{{ __('TRACKING') }}</h3>
          <p class="font-body-md text-body-md text-primary font-semibold">{{ $order->tracking_number ?: $order->order_number }}</p>
          <p class="mt-2 text-xs text-on-surface-variant">{{ __('This tracking number is generated automatically.') }}</p>
        </div>

        @if ($order->payment)
          <div class="bg-surface-container-low p-8 border border-outline-variant">
            <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-2">{{ __('PAYMENT') }}</h3>
            <p class="font-body-md text-sm text-on-surface-variant">{{ __('Reference') }}: {{ $order->payment->reference }}</p>
            <p class="font-body-md text-sm text-on-surface-variant mt-1">{{ __('Provider') }}: {{ $order->payment->provider }}</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>

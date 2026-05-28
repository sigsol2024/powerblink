@extends('layouts.site')

@section('content')
  @php
    /** @var \App\Models\Order|null $order */
    $delivery = strtoupper(str_replace('_', ' ', (string) ($order?->delivery_status ?? 'processing')));
    $payment = strtoupper(str_replace('_', ' ', (string) ($order?->status ?? '')));
    $hasOrder = ! empty($order?->id);
    $trackingNumber = trim((string) ($order?->tracking_number ?? '')) ?: trim((string) ($order?->order_number ?? ''));
  @endphp

  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <div class="text-center mb-10">
        <span class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase block mb-4">{{ __('Order Tracking') }}</span>
        <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-3">{{ $title ?? __('Track your order') }}</h1>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mx-auto">{{ __('Enter your order number or shipping tracking number to see payment and delivery status.') }}</p>
      </div>

      <form method="post" action="{{ route('orders.track') }}" class="max-w-xl mx-auto bg-surface-container-low border border-outline-variant p-7 md:p-9 space-y-4">
        @csrf
        <div>
          <label for="tracking_number" class="block font-label-caps text-label-caps mb-2 text-on-surface-variant">{{ __('Tracking number') }}</label>
          <input
            type="text"
            name="tracking_number"
            id="tracking_number"
            value="{{ old('tracking_number') }}"
            required
            placeholder="VD-XXXXXXXX"
            class="w-full border-0 border-b border-outline-variant bg-transparent px-0 py-3 font-body-md text-body-md placeholder:text-outline-variant focus:ring-0"
          />
          @error('tracking_number')
            <p class="text-error text-sm mt-2">{{ $message }}</p>
          @enderror
        </div>
        <button type="submit" class="bg-primary text-on-primary px-10 py-4 font-button-text text-button-text uppercase tracking-widest hover:scale-[1.02] transition-transform w-full md:w-auto">
          {{ __('Track order') }}
        </button>
      </form>

      @if ($hasOrder)
        <div class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          <div class="lg:col-span-4 space-y-8">
            <div class="bg-surface-container-lowest border border-outline-variant p-6">
              <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">{{ __('Order') }}</p>
              <p class="font-headline-md text-headline-md text-primary">#{{ $order->order_number }}</p>
              <p class="mt-2 font-body-md text-body-md text-on-surface-variant">{{ __('Placed') }}: {{ $order->created_at?->format('M j, Y') }}</p>
            </div>

            <div class="bg-surface-container-lowest border border-outline-variant p-6">
              <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">{{ __('Tracking') }}</p>
              <p class="font-body-md text-body-md text-primary font-semibold">{{ $trackingNumber }}</p>
            </div>

            <div class="bg-surface-container-lowest border border-outline-variant p-6">
              <p class="font-label-caps text-label-caps text-on-surface-variant mb-3">{{ __('Status') }}</p>
              <div class="space-y-2">
                <p class="font-body-md text-body-md">{{ __('Payment') }}: <span class="font-semibold text-primary">{{ $payment }}</span></p>
                <p class="font-body-md text-body-md">{{ __('Delivery') }}: <span class="font-semibold text-primary">{{ $delivery }}</span></p>
              </div>
            </div>

            <div class="bg-surface-container-lowest border border-outline-variant p-6">
              <p class="font-label-caps text-label-caps text-on-surface-variant mb-3">{{ __('Totals') }}</p>
              <p class="font-body-md text-body-md">{{ __('Total') }}: <span class="font-semibold text-primary">{{ \App\Support\Money::formatKobo($order->total) }}</span></p>
            </div>
          </div>

          <div class="lg:col-span-8 space-y-8">
            <div class="bg-surface-container-lowest border border-outline-variant p-6">
              <h2 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Delivery details') }}</h2>
              <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                {{ $order->customer_name }}<br>
                {{ $order->shipping_address_line1 }}<br>
                @if ($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
                {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif {{ $order->shipping_postal_code }}<br>
                {{ $order->shipping_country }}
              </p>
            </div>

            <div class="bg-white border border-outline-variant divide-y divide-outline-variant">
              <div class="p-6">
                <h2 class="font-headline-md text-headline-md text-primary">{{ __('Items') }}</h2>
              </div>
              @foreach ($order->items as $item)
                <div class="p-6 flex justify-between gap-4 text-sm">
                  <div>
                    <p class="font-bold uppercase text-primary">{{ $item->name }}</p>
                    <p class="text-on-surface-variant mt-1">× {{ $item->qty }}</p>
                  </div>
                  <p class="font-medium whitespace-nowrap">{{ \App\Support\Money::formatKobo($item->line_total) }}</p>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection


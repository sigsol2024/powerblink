@extends('layouts.site')

@section('content')
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-2">{{ __('Order details') }}</h1>
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-8 md:mb-10">#{{ $order->order_number }}</p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-surface-container-lowest border border-outline-variant p-6">
          <h2 class="font-label-caps text-label-caps text-on-surface-variant mb-4">{{ __('Shipping') }}</h2>
          <p class="text-sm leading-relaxed">
            {{ $order->customer_name }}<br>
            {{ $order->shipping_address_line1 }}<br>
            @if ($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
            {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif {{ $order->shipping_postal_code }}<br>
            {{ $order->shipping_country }}
          </p>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant p-6">
          <h2 class="font-label-caps text-label-caps text-on-surface-variant mb-4">{{ __('Payment') }}</h2>
          <p class="text-sm">{{ __('Status') }}: <span class="font-bold uppercase">{{ $order->status }}</span></p>
          <p class="text-sm mt-2">{{ __('Total') }}: <span class="font-bold">{{ \App\Support\Money::formatKobo($order->total) }}</span></p>
        </div>
      </div>

      <div class="bg-white border border-[#cfc4c5] divide-y divide-[#cfc4c5]">
        @foreach ($order->items as $item)
          <div class="p-6 flex justify-between gap-4 text-sm">
            <div>
              <p class="font-bold uppercase">{{ $item->name }}</p>
              <p class="text-[#4c4546] mt-1">× {{ $item->qty }}</p>
            </div>
            <p class="font-medium whitespace-nowrap">{{ \App\Support\Money::formatKobo($item->line_total) }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endsection

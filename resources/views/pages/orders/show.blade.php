@extends('layouts.site')

@section('content')
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-2">{{ __('Order details') }}</h1>
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-8 md:mb-10">#{{ $order->order_number }}</p>

      @if (!empty($showPaymentInstructions))
        <div class="mb-10 rounded-sm border-2 border-[#3A3C94] bg-white p-6 md:p-8 shadow-sm">
          @if ($paymentProvider === 'bank_transfer')
            <div class="flex items-start gap-4 mb-6">
              <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#3A3C94]/10 text-[#3A3C94]">
                <x-icon name="building-library" class="w-6 h-6" />
              </span>
              <div>
                <h2 class="font-headline-md text-xl md:text-2xl uppercase text-[#3A3C94] mb-1">{{ __('Bank transfer details') }}</h2>
                <p class="text-sm text-on-surface-variant">{{ __('Transfer the order total using the details below. Use your order number as the payment reference.') }}</p>
              </div>
            </div>
            <div class="text-base md:text-lg text-on-surface whitespace-pre-line leading-relaxed font-medium bg-surface-container-low rounded-sm p-5 md:p-6 border border-outline-variant">
              {{ $bankTransferDetails !== '' ? $bankTransferDetails : __('Bank transfer details will be sent to your email.') }}
            </div>
            <p class="mt-4 text-sm font-semibold text-on-surface">{{ __('Order reference') }}: <span class="text-[#3A3C94]">#{{ $order->order_number }}</span></p>
            <p class="mt-2 text-lg font-bold">{{ __('Amount due') }}: {{ \App\Support\Money::formatKobo($order->total) }}</p>
          @elseif ($paymentProvider === 'pay_on_delivery')
            <div class="flex items-start gap-4 mb-6">
              <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#3A3C94]/10 text-[#3A3C94]">
                <x-icon name="truck" class="w-6 h-6" />
              </span>
              <div>
                <h2 class="font-headline-md text-xl md:text-2xl uppercase text-[#3A3C94] mb-1">{{ __('Pay on delivery') }}</h2>
                <p class="text-sm text-on-surface-variant">{{ __('Your order is confirmed. Please review the instructions below.') }}</p>
              </div>
            </div>
            <div class="text-base md:text-lg text-on-surface leading-relaxed font-medium bg-surface-container-low rounded-sm p-5 md:p-6 border border-outline-variant">
              {{ $payOnDeliveryNote !== '' ? $payOnDeliveryNote : __('You will pay when your order is delivered.') }}
            </div>
            <p class="mt-4 text-lg font-bold">{{ __('Amount due on delivery') }}: {{ \App\Support\Money::formatKobo($order->total) }}</p>
          @endif

          @if (!empty($awaitingAck))
            <div class="mt-8 pt-6 border-t border-outline-variant">
              <a href="{{ $signedPlacedUrl }}" class="inline-flex w-full sm:w-auto items-center justify-center bg-black text-white px-10 py-4 text-xs font-bold uppercase tracking-[0.25em] hover:opacity-90 transition-opacity">
                {{ __('Done') }}
              </a>
            </div>
          @endif
        </div>
      @endif

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
          <p class="text-sm">{{ __('Status') }}: <span class="font-bold uppercase">{{ str_replace('_', ' ', $order->status) }}</span></p>
          <p class="text-sm mt-2">{{ __('Total') }}: <span class="font-bold">{{ \App\Support\Money::formatKobo($order->total) }}</span></p>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant p-6">
          <h2 class="font-label-caps text-label-caps text-on-surface-variant mb-4">{{ __('Delivery') }}</h2>
          <p class="text-sm">{{ __('Status') }}: <span class="font-bold uppercase">{{ str_replace('_', ' ', (string) ($order->delivery_status ?? 'processing')) }}</span></p>
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

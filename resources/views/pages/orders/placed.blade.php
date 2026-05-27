@extends('layouts.site')

@section('content')
  @php
    $provider = (string) ($order->payment?->provider ?? '');
  @endphp
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <div class="max-w-xl mx-auto text-center">
        <span class="text-primary mb-6 inline-flex"><x-icon name="check-circle" class="w-12 h-12" /></span>
        <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-4">{{ __('Order received') }}</h1>
        <p class="font-body-md text-on-surface-variant mb-2">{{ __('Thank you — we have received your order.') }}</p>
        <p class="font-label-caps text-label-caps text-on-surface-variant mb-6">{{ __('Order') }} #{{ $order->order_number }}</p>
        <p class="font-headline-md text-primary mb-8">{{ \App\Support\Money::formatKobo($order->total) }}</p>

        @if ($provider === 'bank_transfer')
          <div class="text-left bg-surface-container-low border border-outline-variant p-6 mb-8">
            <h2 class="font-label-caps text-label-caps text-primary uppercase tracking-widest mb-4">{{ __('Bank transfer details') }}</h2>
            <div class="text-sm text-on-surface-variant whitespace-pre-line leading-relaxed">
              {{ $bankTransferDetails !== '' ? $bankTransferDetails : __('Bank transfer details will be sent to your email.') }}
            </div>
            <p class="mt-4 text-xs text-on-surface-variant">{{ __('Please use your order number as the payment reference.') }}</p>
          </div>
        @elseif ($provider === 'pay_on_delivery')
          <div class="text-left bg-surface-container-low border border-outline-variant p-6 mb-8">
            <h2 class="font-label-caps text-label-caps text-primary uppercase tracking-widest mb-4">{{ __('Pay on delivery') }}</h2>
            <p class="text-sm text-on-surface-variant leading-relaxed">
              {{ $payOnDeliveryNote !== '' ? $payOnDeliveryNote : __('You will pay when your order is delivered.') }}
            </p>
          </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="{{ $signedShowUrl }}" class="inline-block border border-primary text-primary px-8 py-4 font-button-text text-button-text uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-colors">{{ __('View order') }}</a>
          <a href="{{ route('shop.index') }}" class="inline-block bg-primary text-on-primary px-8 py-4 font-button-text text-button-text uppercase tracking-widest hover:opacity-90">{{ __('Continue shopping') }}</a>
        </div>
      </div>
    </div>
  </div>
@endsection

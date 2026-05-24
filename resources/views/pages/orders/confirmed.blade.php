@extends('layouts.site')

@section('content')
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop text-center">
      <span class="material-symbols-outlined text-5xl text-secondary mb-6">check_circle</span>
      <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-4">{{ __('Thank you') }}</h1>
      <p class="font-body-md text-on-surface-variant mb-2">{{ __('Your order has been confirmed.') }}</p>
      <p class="font-label-caps text-label-caps text-on-surface-variant mb-10">{{ __('Order') }} #{{ $order->order_number }}</p>
      <p class="font-headline-md text-primary mb-10">{{ \App\Support\Money::formatKobo($order->total) }}</p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ $signedShowUrl }}" class="inline-block border border-primary text-primary px-8 py-4 font-button-text text-button-text uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-colors">{{ __('View order') }}</a>
        <a href="{{ route('orders.lookup.index') }}" class="inline-block border border-outline-variant text-on-background px-8 py-4 font-button-text text-button-text uppercase tracking-widest hover:border-primary">{{ __('Find your orders') }}</a>
        <a href="{{ route('shop.index') }}" class="inline-block bg-primary text-on-primary px-8 py-4 font-button-text text-button-text uppercase tracking-widest hover:opacity-90">{{ __('Continue shopping') }}</a>
      </div>
    </div>
  </div>
@endsection

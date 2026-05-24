@extends('layouts.site')

@section('content')
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-4">{{ $title }}</h1>
      <p class="font-body-md text-on-surface-variant mb-8">{{ __('Orders for') }} {{ $customerEmail }}</p>

      <ul class="space-y-4 max-w-2xl">
        @foreach($orders as $order)
          <li class="border border-outline-variant p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <p class="font-label-caps text-label-caps text-on-surface-variant">{{ __('Order') }} #{{ $order->order_number }}</p>
              <p class="font-body-md">{{ $order->created_at->format('M j, Y') }}</p>
              <p class="font-headline-md text-primary">{{ \App\Support\Money::formatKobo($order->total) }}</p>
            </div>
            <a href="{{ $orderLinks[$order->id] }}" class="inline-block border border-primary text-primary px-6 py-3 font-button-text text-button-text uppercase tracking-widest hover:bg-primary hover:text-on-primary transition-colors text-center">
              {{ __('View order') }}
            </a>
          </li>
        @endforeach
      </ul>

      <p class="mt-8">
        <a href="{{ route('orders.lookup.index') }}" class="font-body-md text-primary underline">{{ __('Search again') }}</a>
      </p>
    </div>
  </div>
@endsection

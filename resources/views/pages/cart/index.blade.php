@extends('layouts.site')

@section('content')
  @php
    $lineCount = \App\Support\Cart::count();
  @endphp
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <nav class="mb-8 md:mb-12 flex items-center gap-2 font-label-caps text-label-caps text-on-surface-variant">
        <a href="{{ route('shop.index') }}" class="hover:text-black transition-colors">{{ __('Shop') }}</a>
        <span>/</span>
        <span class="text-primary underline">{{ __('Shopping Bag') }}</span>
      </nav>

      @if (session('status'))
        <div class="mb-8 rounded border border-[#cfc4c5] bg-white px-4 py-3 text-sm">{{ session('status') }}</div>
      @endif

      @if ($lines === [])
        <div class="py-20 text-center">
          <p class="text-lg font-medium mb-6">{{ __('Your bag is empty.') }}</p>
          <a href="{{ route('shop.index') }}" class="inline-block bg-black text-white px-8 py-4 text-xs font-bold uppercase tracking-[0.2em] hover:opacity-90">{{ __('Continue shopping') }}</a>
        </div>
      @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
          <section class="lg:col-span-8 space-y-8">
            <div class="flex justify-between items-end border-b border-outline-variant pb-4">
              <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase">{{ __('Your Bag') }}</h1>
              <span class="font-label-caps text-label-caps text-on-surface-variant">{{ trans_choice(':count item|:count items', $lineCount, ['count' => $lineCount]) }}</span>
            </div>

            @foreach ($lines as $line)
              <div class="flex flex-col md:flex-row gap-8 py-8 border-b border-[#cfc4c5] group">
                <div class="w-full md:w-48 aspect-[3/4] bg-[#eeeeee] overflow-hidden shrink-0">
                  @if (!empty($line['image']))
                    <img src="{{ $line['image'] }}" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                  @endif
                </div>
                <div class="flex-grow flex flex-col justify-between min-w-0">
                  <div class="flex justify-between items-start gap-4">
                    <div>
                      <h2 class="text-xl uppercase tracking-wide font-medium">{{ $line['name'] }}</h2>
                      @if (!empty($line['variant_label']))
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#4c4546] mt-4">{{ $line['variant_label'] }}</p>
                      @endif
                      @if (!empty($line['sku']))
                        <p class="text-[10px] text-[#7e7576] mt-1">{{ __('SKU') }}: {{ $line['sku'] }}</p>
                      @endif
                    </div>
                    <p class="text-xl font-medium whitespace-nowrap">{{ format_currency($line['unit_price']) }}</p>
                  </div>
                  <div class="flex flex-wrap justify-between items-center gap-4 mt-8">
                    <form method="post" action="{{ route('cart.update') }}" class="flex items-center border border-[#7e7576] px-4 py-2">
                      @csrf
                      <input type="hidden" name="vehicle_id" value="{{ $line['vehicle_id'] }}" />
                      @if (!empty($line['vehicle_variant_id']))
                        <input type="hidden" name="vehicle_variant_id" value="{{ $line['vehicle_variant_id'] }}" />
                      @endif
                      <button type="submit" name="qty" value="{{ max(1, (int) $line['qty'] - 1) }}" class="hover:text-black inline-flex items-center" aria-label="{{ __('Decrease quantity') }}">
                        <x-icon name="minus" class="w-4 h-4" />
                      </button>
                      <span class="mx-6 min-w-[20px] text-center text-sm">{{ $line['qty'] }}</span>
                      <button type="submit" name="qty" value="{{ min(99, (int) $line['qty'] + 1) }}" class="hover:text-black inline-flex items-center" aria-label="{{ __('Increase quantity') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                      </button>
                    </form>
                    <form method="post" action="{{ route('cart.remove') }}">
                      @csrf
                      <input type="hidden" name="vehicle_id" value="{{ $line['vehicle_id'] }}" />
                      @if (!empty($line['vehicle_variant_id']))
                        <input type="hidden" name="vehicle_variant_id" value="{{ $line['vehicle_variant_id'] }}" />
                      @endif
                      <button type="submit" class="text-[10px] font-bold uppercase tracking-widest text-[#4c4546] hover:text-[#ba1a1a] flex items-center gap-1.5">
                        <x-icon name="close" class="w-3.5 h-3.5" /> {{ __('Remove') }}
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            @endforeach
          </section>

          <aside class="lg:col-span-4 lg:sticky lg:top-32 h-fit bg-white p-8 border border-[#cfc4c5]">
            <h2 class="text-xl uppercase mb-8 border-b border-[#cfc4c5] pb-4">{{ __('Order Summary') }}</h2>
            <div class="space-y-6">
              <div class="flex justify-between">
                <span class="text-[#4c4546]">{{ __('Subtotal') }}</span>
                <span class="font-bold">{{ format_currency($subtotal) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#4c4546]">{{ __('Shipping') }}</span>
                <span class="text-[#78582f]">{{ __('Calculated at checkout') }}</span>
              </div>
              <div class="border-t border-[#cfc4c5] pt-6 flex justify-between items-center">
                <span class="text-sm font-bold uppercase tracking-widest">{{ __('Total') }}</span>
                <span class="text-2xl font-medium">{{ format_currency($subtotal) }}</span>
              </div>
              <a href="{{ route('checkout.index') }}" class="block w-full bg-black text-white text-center py-5 text-xs font-bold uppercase tracking-[0.25em] hover:opacity-90 transition-opacity">
                {{ __('Proceed to checkout') }}
              </a>
              <a href="{{ route('shop.index') }}" class="block text-center text-[10px] font-bold uppercase tracking-widest text-[#4c4546] hover:text-black pt-2">
                {{ __('Continue shopping') }}
              </a>
            </div>
          </aside>
        </div>
      @endif
    </div>
  </div>
@endsection

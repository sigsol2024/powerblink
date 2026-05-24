@extends('layouts.site')

@section('content')
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <nav class="mb-8 md:mb-10 flex items-center gap-2 font-label-caps text-label-caps text-on-surface-variant">
        <a href="{{ route('shop.index') }}" class="hover:text-black">{{ __('Shop') }}</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-black">{{ __('Bag') }}</a>
        <span>/</span>
        <span class="text-black">{{ __('Checkout') }}</span>
      </nav>

      <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-8 md:mb-10">{{ __('Checkout') }}</h1>

      @if ($errors->any())
        <div class="mb-8 rounded border border-[#ba1a1a]/30 bg-[#ffdad6]/40 px-4 py-3 text-sm text-[#93000a]">
          <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="post" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        @csrf
        <div class="lg:col-span-7 space-y-10">
          <section>
            <h2 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#4c4546] mb-6">{{ __('Contact') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <input name="customer_name" value="{{ old('customer_name') }}" required class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('Full name') }}" />
              </div>
              <div>
                <input name="customer_email" type="email" value="{{ old('customer_email') }}" required class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('Email') }}" />
              </div>
              <div>
                <input name="customer_phone" value="{{ old('customer_phone') }}" class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('Phone') }}" />
              </div>
            </div>
          </section>

          <section>
            <h2 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#4c4546] mb-6">{{ __('Shipping address') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <input name="shipping_address_line1" value="{{ old('shipping_address_line1') }}" required class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('Street address') }}" />
              </div>
              <div class="md:col-span-2">
                <input name="shipping_address_line2" value="{{ old('shipping_address_line2') }}" class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('Apartment, suite, etc. (optional)') }}" />
              </div>
              <div>
                <input name="shipping_city" value="{{ old('shipping_city') }}" required class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('City') }}" />
              </div>
              <div>
                <input name="shipping_state" value="{{ old('shipping_state') }}" class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('State') }}" />
              </div>
              <div>
                <input name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black" placeholder="{{ __('Postal code') }}" />
              </div>
              <div>
                <select name="shipping_country" required class="w-full bg-transparent border-0 border-b border-[#cfc4c5] py-3 focus:ring-0 focus:border-black">
                  <option value="NG" @selected(old('shipping_country', 'NG') === 'NG')>{{ __('Nigeria') }}</option>
                  <option value="GH" @selected(old('shipping_country') === 'GH')>{{ __('Ghana') }}</option>
                  <option value="GB" @selected(old('shipping_country') === 'GB')>{{ __('United Kingdom') }}</option>
                  <option value="US" @selected(old('shipping_country') === 'US')>{{ __('United States') }}</option>
                </select>
              </div>
            </div>
          </section>

          <section class="bg-[#f3f3f3] p-8 border border-[#cfc4c5]">
            <h2 class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#4c4546] mb-4">{{ __('Payment') }}</h2>
            <p class="text-sm text-[#4c4546]">{{ __('You will be redirected to Paystack to pay securely by card, bank transfer, or USSD.') }}</p>
          </section>
        </div>

        <aside class="lg:col-span-5">
          <div class="sticky top-32 bg-white p-8 border border-[#cfc4c5] space-y-6">
            <h2 class="text-xl uppercase border-b border-[#cfc4c5] pb-4">{{ __('Order summary') }}</h2>
            <div class="space-y-4 max-h-80 overflow-y-auto">
              @foreach ($lines as $line)
                <div class="flex gap-4 text-sm">
                  @if (!empty($line['image']))
                    <img src="{{ $line['image'] }}" alt="" class="w-16 h-20 object-cover bg-[#eee]" />
                  @endif
                  <div class="min-w-0 flex-1">
                    <p class="font-bold uppercase text-xs truncate">{{ $line['name'] }}</p>
                    <p class="text-[#4c4546] text-xs mt-1">× {{ $line['qty'] }}</p>
                  </div>
                  <p class="font-medium whitespace-nowrap">{{ format_currency($line['unit_price'] * $line['qty']) }}</p>
                </div>
              @endforeach
            </div>
            <div class="border-t border-[#cfc4c5] pt-4 flex justify-between font-bold">
              <span>{{ __('Total') }}</span>
              <span>{{ format_currency($subtotal) }}</span>
            </div>
            <button type="submit" class="w-full bg-black text-white py-5 text-xs font-bold uppercase tracking-[0.25em] hover:opacity-90">
              {{ __('Pay with Paystack') }}
            </button>
          </div>
        </aside>
      </form>
    </div>
  </div>
@endsection

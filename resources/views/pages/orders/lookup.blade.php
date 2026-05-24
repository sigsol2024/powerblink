@extends('layouts.site')

@section('content')
  <div class="luxe-store bg-background text-on-background min-h-screen luxe-geometric-bg font-body-md">
    <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter pt-24 md:pt-28 pb-section-py-mobile md:pb-section-py-desktop">
      <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg uppercase mb-4">{{ $title }}</h1>
      <p class="font-body-md text-on-surface-variant mb-8 max-w-xl">{{ __('Enter the email you used at checkout to view your past orders.') }}</p>

      <form method="post" action="{{ route('orders.lookup') }}" class="max-w-md space-y-4">
        @csrf
        <div>
          <label for="customer_email" class="block font-label-caps text-label-caps mb-2">{{ __('Email') }}</label>
          <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" required
            class="w-full border border-outline-variant bg-surface px-4 py-3 font-body-md">
          @error('customer_email')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div>
          <label for="order_number" class="block font-label-caps text-label-caps mb-2">{{ __('Order number') }} <span class="text-on-surface-variant">({{ __('optional') }})</span></label>
          <input type="text" name="order_number" id="order_number" value="{{ old('order_number') }}"
            class="w-full border border-outline-variant bg-surface px-4 py-3 font-body-md" placeholder="VD-XXXXXXXX">
        </div>
        <button type="submit" class="bg-primary text-on-primary px-8 py-4 font-button-text text-button-text uppercase tracking-widest hover:opacity-90">
          {{ __('Find orders') }}
        </button>
      </form>
    </div>
  </div>
@endsection

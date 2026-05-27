@php
  $hasVariants = $productVariants->isNotEmpty();
  $inStock = $vehicle->status === 'approved' && (! is_null($price) && $price > 0);
  $compositionCare = trim((string) ($vehicle->composition_care ?? ''));
  $shippingReturns = trim((string) ($vehicle->shipping_returns ?? ''));
  $featuresList = is_array($vehicle->features) ? array_filter($vehicle->features) : [];
@endphp

<div class="mb-8">
  <div class="flex items-center justify-between mb-2 gap-4">
    <span class="font-label-caps text-label-caps text-custom-accent tracking-[0.3em] uppercase">
      {{ $inStock ? __('In Stock') : __('Unavailable') }}
    </span>
    @if ($vehicle->vin)
      <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ __('SKU') }}: {{ $vehicle->vin }}</span>
    @endif
  </div>
  <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-2">{{ $vehicle->title }}</h1>
  <p class="font-body-lg text-body-lg text-primary font-semibold">
    @if (! is_null($price)){{ format_currency($price) }}@else {{ __('Price on request') }}@endif
  </p>
</div>

@if ($overview)
  <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-10">{{ \Illuminate\Support\Str::limit(strip_tags($overview), 320) }}</p>
@endif

@if (session('status'))
  <p class="mb-4 text-sm text-custom-accent">{{ session('status') }}</p>
@endif
@if ($errors->any())
  <ul class="mb-4 text-sm text-error list-disc pl-4">
    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
  </ul>
@endif

@if ($inStock)
  <form id="add-to-cart-form" method="post" action="{{ route('cart.add') }}" class="space-y-6 mb-6" data-cart-add-form>
    @csrf
    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}" />

    @if ($hasVariants)
      <div class="border-t border-outline-variant pt-6">
        <div class="flex justify-between items-center mb-4">
          <span class="font-label-caps text-label-caps text-primary uppercase">{{ __('Select variant') }}</span>
        </div>
        <div class="flex flex-wrap gap-3">
          @foreach ($productVariants as $variant)
            <label class="cursor-pointer">
              <input type="radio" name="vehicle_variant_id" value="{{ $variant->id }}" class="peer sr-only" @checked($loop->first && (int) $variant->stock > 0) @disabled((int) $variant->stock <= 0) required />
              <span class="w-14 h-12 inline-flex items-center justify-center border font-label-caps text-label-caps transition-all peer-checked:border-primary peer-checked:bg-primary peer-checked:text-on-primary border-outline-variant hover:border-primary peer-disabled:opacity-40">
                {{ $variant->displayLabel() }}
              </span>
            </label>
          @endforeach
        </div>
      </div>
    @endif

    <div class="flex items-center gap-4">
      <label class="font-label-caps text-label-caps text-primary uppercase" for="qty">{{ __('Qty') }}</label>
      <input id="qty" name="qty" type="number" min="1" max="99" value="1" class="w-20 border-b border-outline-variant bg-transparent py-2 font-body-md focus:border-primary focus:outline-none" />
    </div>
  </form>

  <div class="grid grid-cols-2 gap-3 mb-6">
    <button type="submit" form="add-to-cart-form" class="bg-primary text-on-primary py-4 px-3 font-button-text text-button-text uppercase tracking-[0.2em] hover:scale-[1.01] active:scale-[0.99] transition-all duration-300 text-center">
      {{ __('Add to bag') }}
    </button>
    @auth
      <form method="post" action="{{ route('favorites.toggle', ['vehicle' => $vehicle->id]) }}" class="contents">
        @csrf
        <button type="submit" class="w-full border border-primary text-primary py-4 px-3 font-button-text text-button-text uppercase tracking-[0.2em] hover:bg-primary hover:text-on-primary transition-all duration-300 text-center">
          {{ $isFavorited ? __('Saved') : __('Wishlist') }}
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="flex items-center justify-center border border-primary text-primary py-4 px-3 font-button-text text-button-text uppercase tracking-[0.2em] hover:bg-primary hover:text-on-primary transition-all duration-300 text-center">
        {{ __('Wishlist') }}
      </a>
    @endauth
  </div>
@endif

@if ($compositionCare !== '' || $shippingReturns !== '' || $featuresList !== [])
  <div class="space-y-4 mt-10">
    @if ($compositionCare !== '')
      <details class="group border-b border-outline-variant pb-4" open>
        <summary class="flex justify-between items-center cursor-pointer list-none font-label-caps text-label-caps text-primary uppercase tracking-widest">
          {{ __('Composition & Care') }}
          <x-icon name="chevron-down" class="w-4 h-4 group-open:rotate-180 transition-transform" />
        </summary>
        <div class="pt-4 text-on-surface-variant font-body-md text-sm leading-relaxed whitespace-pre-line">
          {{ $compositionCare }}
        </div>
      </details>
    @endif
    @if ($shippingReturns !== '')
      <details class="group border-b border-outline-variant pb-4">
        <summary class="flex justify-between items-center cursor-pointer list-none font-label-caps text-label-caps text-primary uppercase tracking-widest">
          {{ __('Shipping & Returns') }}
          <x-icon name="chevron-down" class="w-4 h-4 group-open:rotate-180 transition-transform" />
        </summary>
        <div class="pt-4 text-on-surface-variant font-body-md text-sm leading-relaxed whitespace-pre-line">
          {{ $shippingReturns }}
        </div>
      </details>
    @endif
    @if ($featuresList !== [])
      <details class="group border-b border-outline-variant pb-4">
        <summary class="flex justify-between items-center cursor-pointer list-none font-label-caps text-label-caps text-primary uppercase tracking-widest">
          {{ __('Features') }}
          <x-icon name="chevron-down" class="w-4 h-4 group-open:rotate-180 transition-transform" />
        </summary>
        <ul class="pt-4 space-y-2 text-sm text-on-surface-variant">
          @foreach ($featuresList as $feature)
            <li class="flex gap-2"><span class="text-custom-accent">•</span> {{ $feature }}</li>
          @endforeach
        </ul>
      </details>
    @endif
  </div>
@endif

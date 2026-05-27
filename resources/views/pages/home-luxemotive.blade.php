@extends('layouts.site')

@push('head')
  @include('partials.luxe-home-styles')
@endpush

@php
  $s = $sections ?? [];
  $heroTitle = $s['hero_title'] ?? __('Artisanship Redefined');
  $heroBg = \App\Support\PlaceholderMedia::url($s['hero_image'] ?? 'asset/images/media/home-hero-main.jpg');
  $heroCtaHref = $s['hero_cta_href'] ?? '/shop';
  $heroCtaUrl = \Illuminate\Support\Str::startsWith($heroCtaHref, ['http://', 'https://']) ? $heroCtaHref : url($heroCtaHref);
  $promoBg = \App\Support\PlaceholderMedia::url($s['dealer_cta_bg'] ?? 'asset/images/media/home-cta-left.jpg');
  $recentTitle = trim((string) ($s['recent_title'] ?? __('New Arrivals')));
  $bestsellers = ($recentVehicles ?? collect())->where('is_special', true)->take(4);
  if ($bestsellers->isEmpty()) {
    $bestsellers = ($recentVehicles ?? collect())->take(4);
  }
  $arrivals = ($recentVehicles ?? collect())->take(6);
  $categories = [
    [
      'label' => $s['category_1_label'] ?? __('LIMITED'),
      'title' => $s['category_1_title'] ?? __('Bridal'),
      'image' => \App\Support\PlaceholderMedia::url($s['category_1_image'] ?? 'asset/images/media/home-cta-left.jpg'),
      'url' => route('shop.index'),
    ],
    [
      'label' => $s['category_2_label'] ?? __('ESSENTIALS'),
      'title' => $s['category_2_title'] ?? __('Pret-a-Porter'),
      'image' => \App\Support\PlaceholderMedia::url($s['category_2_image'] ?? 'asset/images/media/home-recent-fallback.jpg'),
      'url' => route('shop.index'),
    ],
    [
      'label' => $s['category_3_label'] ?? __('HERITAGE'),
      'title' => $s['category_3_title'] ?? __('Textiles'),
      'image' => \App\Support\PlaceholderMedia::url($s['category_3_image'] ?? $promoBg),
      'url' => route('shop.index'),
    ],
  ];
@endphp

@section('content')
  <main class="luxe-store mt-[80px]">
    {{-- Hero --}}
    @php
      $heroSlides = ($recentVehicles ?? collect())->take(5);
    @endphp
    <section class="relative w-full overflow-hidden">
      <div class="absolute inset-0">
        <div class="absolute inset-0 luxe-african-pattern opacity-40"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-surface-container-lowest via-surface-container-low/60 to-surface-container-lowest"></div>
      </div>
      <div class="relative max-w-max-container mx-auto px-margin-mobile md:px-gutter py-10 md:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 items-center">
          <div class="min-w-0">
            <p class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase mb-3">{{ __('New season') }}</p>
            <h2 class="font-display-md md:font-display-lg text-primary uppercase mb-5 tracking-tighter">{{ $heroTitle }}</h2>
            @if (! empty($s['hero_subtitle']))
              <p class="font-body-lg text-on-surface-variant mb-7 max-w-xl">{{ $s['hero_subtitle'] }}</p>
            @endif
            <div class="flex flex-wrap gap-3">
              <a href="{{ $heroCtaUrl }}" class="inline-block bg-primary text-on-primary font-button-text px-8 md:px-10 py-4 uppercase tracking-widest luxe-scale-hover luxe-transition-standard">
                {{ $s['hero_cta_text'] ?? __('Explore Collection') }}
              </a>
              <a href="{{ route('shop.index') }}" class="inline-block border border-outline-variant text-primary font-button-text px-8 md:px-10 py-4 uppercase tracking-widest hover:bg-surface-container-high luxe-transition-standard">
                {{ __('Shop now') }}
              </a>
            </div>
          </div>

          <div
            class="min-w-0"
            x-data="{
              index: 0,
              timer: null,
              slideCount: {{ (int) $heroSlides->count() }},
              prefersReduced: (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches),
              goTo(i) {
                if (!this.slideCount) return;
                this.index = ((i % this.slideCount) + this.slideCount) % this.slideCount;
              },
              start() {
                if (this.prefersReduced || this.slideCount <= 1) return;
                this.stop();
                this.timer = window.setInterval(() => this.goTo(this.index + 1), 4500);
              },
              stop() {
                if (this.timer) { window.clearInterval(this.timer); this.timer = null; }
              },
              init() { this.start(); },
              touchStartX: 0,
              touchStartY: 0,
              touching: false,
              onTouchStart(e) {
                if (!e.touches || e.touches.length !== 1) return;
                this.touching = true;
                this.touchStartX = e.touches[0].clientX;
                this.touchStartY = e.touches[0].clientY;
                this.stop();
              },
              onTouchEnd(e) {
                if (!this.touching) return;
                this.touching = false;
                const t = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0] : null;
                if (!t) { this.start(); return; }
                const dx = t.clientX - this.touchStartX;
                const dy = t.clientY - this.touchStartY;
                if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
                  this.goTo(this.index + (dx < 0 ? 1 : -1));
                }
                this.start();
              },
            }"
            x-init="init()"
            @mouseenter="stop()"
            @mouseleave="start()"
            @touchstart.passive="onTouchStart($event)"
            @touchend.passive="onTouchEnd($event)"
          >
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-sm overflow-hidden">
              <div class="relative aspect-[4/5] bg-surface-container-low">
                @if ($heroSlides->isNotEmpty())
                  <div
                    class="absolute inset-0 flex transition-transform duration-500 ease-in-out"
                    :style="`transform: translateX(-${index * 100}%);`"
                  >
                    @foreach ($heroSlides as $vehicle)
                      @php
                        $cover = $vehicle->images->first();
                        $img = $cover ? \App\Support\VehicleImageUrl::url($cover->path) : $heroBg;
                      @endphp
                      <a href="{{ route('product.show', ['slug' => $vehicle->slug]) }}" class="w-full shrink-0 relative block">
                        <img src="{{ $img }}" alt="{{ $vehicle->title }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/60 via-black/15 to-transparent">
                          <p class="font-label-caps text-label-caps text-white/90 tracking-widest">{{ $vehicle->categoryOption?->value ?: __('Collection') }}</p>
                          <h3 class="mt-2 font-headline-md text-headline-md text-white">{{ $vehicle->title }}</h3>
                        </div>
                      </a>
                    @endforeach
                  </div>
                @else
                  <img src="{{ $heroBg }}" alt="" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
                @endif
              </div>
            </div>

            @if ($heroSlides->count() > 1)
              <div class="mt-4 flex justify-center gap-2">
                @foreach ($heroSlides as $vehicle)
                  <button
                    type="button"
                    class="h-1.5 w-7 rounded-full transition"
                    :class="index === {{ $loop->index }} ? 'bg-primary' : 'bg-outline-variant hover:bg-primary/40'"
                    @click="goTo({{ $loop->index }}); stop(); start();"
                    aria-label="{{ __('Go to slide :n', ['n' => $loop->iteration]) }}"
                  ></button>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    {{-- Categories --}}
    <section class="py-section-py-mobile md:py-section-py-desktop max-w-max-container mx-auto px-margin-mobile md:px-gutter">
      <h2 class="font-headline-md text-headline-md text-center mb-8 md:mb-10 uppercase">{{ __('Shop Categories') }}</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        @foreach ($categories as $cat)
          <a href="{{ $cat['url'] }}" class="group relative aspect-[4/5] overflow-hidden cursor-pointer block">
            <img src="{{ $cat['image'] }}" alt="" class="w-full h-full object-cover luxe-transition-standard group-hover:scale-105" loading="lazy" />
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 luxe-transition-standard"></div>
            <div class="absolute bottom-4 md:bottom-6 left-4 md:left-6 text-white">
              <p class="font-label-caps text-label-caps tracking-widest mb-1">{{ $cat['label'] }}</p>
              <h3 class="font-headline-sm text-headline-sm">{{ $cat['title'] }}</h3>
            </div>
          </a>
        @endforeach
      </div>
    </section>

    {{-- New arrivals scroll --}}
    @if ($arrivals->isNotEmpty())
      <section class="bg-surface-container-low py-section-py-mobile md:py-section-py-desktop overflow-hidden">
        <div class="max-w-max-container mx-auto px-margin-mobile md:px-gutter mb-8 md:mb-12 flex justify-between items-end">
          <div>
            <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">{{ __('SEASONAL DROP') }}</p>
            <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg">{{ $recentTitle }}</h2>
          </div>
          <a href="{{ route('shop.index') }}" class="font-button-text text-button-text border-b border-primary pb-1 hidden md:block">{{ __('VIEW ALL') }}</a>
        </div>
        <div class="flex gap-6 overflow-x-auto luxe-hide-scrollbar px-margin-mobile md:px-[calc((100vw-1200px)/2+1.5rem)]">
          @foreach ($arrivals as $vehicle)
            @php
              $cover = $vehicle->images->first();
              $img = $cover ? \App\Support\VehicleImageUrl::url($cover->path) : \App\Support\PlaceholderMedia::url('asset/images/media/home-recent-fallback.jpg');
              $subtitle = $vehicle->categoryOption?->value ?: __('Collection');
            @endphp
            <a href="{{ route('product.show', ['slug' => $vehicle->slug]) }}" class="min-w-[280px] md:min-w-[400px] flex-shrink-0 group cursor-pointer block">
              <div class="aspect-[4/5] overflow-hidden bg-surface-container-lowest mb-4 md:mb-6 relative">
                <img src="{{ $img }}" alt="{{ $vehicle->title }}" class="w-full h-full object-cover luxe-transition-standard group-hover:scale-105" loading="lazy" />
                @if ($vehicle->is_special)
                  <span class="absolute top-4 left-4 bg-surface-container-lowest px-3 py-1 font-label-caps text-[10px] tracking-tighter">{{ __('LIMITED') }}</span>
                @endif
              </div>
              <div class="flex justify-between items-start gap-4">
                <div class="min-w-0">
                  <h4 class="font-body-lg text-body-lg text-primary uppercase truncate">{{ $vehicle->title }}</h4>
                  <p class="font-body-md text-body-md text-on-surface-variant">{{ $subtitle }}</p>
                </div>
                <p class="font-body-lg text-body-lg font-bold text-secondary-fixed-dim shrink-0">
                  @if (! is_null($vehicle->price)){{ format_currency($vehicle->price) }}@else {{ __('Ask') }}@endif
                </p>
              </div>
            </a>
          @endforeach
        </div>
        <div class="mt-8 text-center md:hidden px-margin-mobile">
          <a href="{{ route('shop.index') }}" class="font-button-text text-button-text border-b border-primary pb-1">{{ __('VIEW ALL') }}</a>
        </div>
      </section>
    @endif

    {{-- Bestsellers grid --}}
    @if ($bestsellers->isNotEmpty())
      <section class="py-section-py-mobile md:py-section-py-desktop max-w-max-container mx-auto px-margin-mobile md:px-gutter">
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-center mb-10 md:mb-16 uppercase">{{ __('The Bestsellers') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 md:gap-x-6 gap-y-8 md:gap-y-12">
          @foreach ($bestsellers as $vehicle)
            @php
              $cover = $vehicle->images->first();
              $img = $cover ? \App\Support\VehicleImageUrl::url($cover->path) : \App\Support\PlaceholderMedia::url('asset/images/media/home-recent-fallback.jpg');
            @endphp
            <a href="{{ route('product.show', ['slug' => $vehicle->slug]) }}" class="group cursor-pointer block">
              <div class="aspect-[3/4] overflow-hidden border border-outline-variant mb-4 md:mb-6 relative">
                <img src="{{ $img }}" alt="{{ $vehicle->title }}" class="w-full h-full object-cover luxe-transition-standard group-hover:scale-105" loading="lazy" />
                @if ($vehicle->is_special)
                  <span class="absolute top-4 left-4 bg-surface-container-lowest px-3 py-1 font-label-caps text-[10px] tracking-tighter">{{ __('BESTSELLER') }}</span>
                @endif
              </div>
              <h4 class="font-body-md text-body-md text-primary uppercase mb-1 line-clamp-2">{{ $vehicle->title }}</h4>
              <p class="font-body-md text-body-md text-secondary-fixed-dim">
                @if (! is_null($vehicle->price)){{ format_currency($vehicle->price) }}@else {{ __('Ask') }}@endif
              </p>
            </a>
          @endforeach
        </div>
      </section>
    @endif

    {{-- Promo banner --}}
    <section class="max-w-max-container mx-auto px-margin-mobile md:px-gutter mb-section-py-mobile md:mb-section-py-desktop">
      <div class="relative h-[320px] md:h-[500px] overflow-hidden flex items-center justify-center text-center">
        <img src="{{ $promoBg }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/40 backdrop-blur-[2px]"></div>
        <div class="relative z-10 px-margin-mobile md:px-gutter">
          <p class="font-label-caps text-label-caps text-white mb-4 tracking-[0.3em]">{{ $s['promo_eyebrow'] ?? __('LIMITED CAPSULE') }}</p>
          <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-white mb-6 md:mb-8">{{ $s['promo_title'] ?? __('The Diaspora Series') }}</h2>
          <a href="{{ $heroCtaUrl }}" class="inline-block border border-white text-white font-button-text px-8 md:px-10 py-4 uppercase tracking-widest hover:bg-white hover:text-primary luxe-transition-standard luxe-scale-hover">
            {{ $s['promo_cta'] ?? __('Explore Series') }}
          </a>
        </div>
      </div>
    </section>

    {{-- Heritage / about --}}
    <section class="py-section-py-mobile md:py-section-py-desktop border-t border-outline-variant">
      <div class="max-w-[800px] mx-auto px-margin-mobile md:px-gutter text-center">
        <p class="font-label-caps text-label-caps text-secondary-fixed-dim mb-6">{{ $s['welcome_eyebrow'] ?? __('OUR HERITAGE') }}</p>
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-6 md:mb-8 italic">{{ $s['welcome_title'] ?? __('Crafting a New Legacy') }}</h2>
        <div class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed space-y-6">
          <p>{{ $s['welcome_body'] ?? __('We collaborate with master artisans to bring exceptional pieces to a global audience. Each listing reflects quality, story, and craft.') }}</p>
          @if (! empty($s['welcome_body_2']))
            <p>{{ $s['welcome_body_2'] }}</p>
          @endif
        </div>
        <div class="mt-12">
          <span class="text-primary inline-flex"><x-icon name="sparkles" class="w-10 h-10" /></span>
        </div>
      </div>
    </section>

    {{-- Newsletter --}}
    <section class="bg-surface-container py-section-py-mobile md:py-section-py-desktop">
      <div class="max-w-[600px] mx-auto px-margin-mobile md:px-gutter text-center">
        <h2 class="font-headline-md text-headline-md mb-4 uppercase tracking-widest">{{ __('Join the Circle') }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mb-8 md:mb-10">{{ __('Receive exclusive access to new collections and artisanal stories.') }}</p>
        <form class="flex flex-col md:flex-row gap-4" method="get" action="{{ route('contact') }}">
          <input type="email" name="email" class="flex-1 bg-transparent border-b border-primary py-3 px-2 font-label-caps text-label-caps focus:outline-none focus:border-secondary-fixed-dim luxe-transition-standard" placeholder="{{ __('EMAIL ADDRESS') }}" />
          <button type="submit" class="bg-primary text-on-primary font-button-text px-8 md:px-10 py-4 uppercase tracking-widest luxe-scale-hover luxe-transition-standard">
            {{ __('SUBSCRIBE') }}
          </button>
        </form>
      </div>
    </section>
  </main>
@endsection

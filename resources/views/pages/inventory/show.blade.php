@extends('layouts.site')

@push('head')
  @include('partials.luxe-home-styles')
@endpush

@section('content')
@php
  $images = $vehicle->images ?? collect();
  $cover = $images->first();
  $galleryUrls = $images->map(fn ($img) => \App\Support\VehicleImageUrl::url($img->path))->values();
  $youtubeId = null;
  $videoUrl = trim((string) ($vehicle->video_url ?? ''));
  if ($videoUrl !== '') {
      $try = $videoUrl;
      if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $try) === 1) {
          $youtubeId = $try;
      } else {
          $parts = parse_url($try) ?: [];
          $host = strtolower((string) ($parts['host'] ?? ''));
          $path = (string) ($parts['path'] ?? '');
          parse_str((string) ($parts['query'] ?? ''), $qs);
          if (str_contains($host, 'youtu.be')) {
              $cand = trim($path, '/');
              if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $cand) === 1) {
                  $youtubeId = $cand;
              }
          } elseif (str_contains($host, 'youtube.com')) {
              if (! empty($qs['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', (string) $qs['v']) === 1) {
                  $youtubeId = (string) $qs['v'];
              } elseif (preg_match('#/embed/([a-zA-Z0-9_-]{11})#', $path, $m) === 1) {
                  $youtubeId = (string) ($m[1] ?? '');
              }
          }
      }
  }
  $galleryItems = $galleryUrls->map(fn ($u) => ['type' => 'image', 'src' => $u])->values()->all();
  if ($youtubeId) {
      $galleryItems[] = [
          'type' => 'video',
          'provider' => 'youtube',
          'embedUrl' => 'https://www.youtube.com/embed/'.$youtubeId.'?autoplay=1&rel=0',
          'thumbUrl' => 'https://img.youtube.com/vi/'.$youtubeId.'/hqdefault.jpg',
          'externalUrl' => 'https://www.youtube.com/watch?v='.$youtubeId,
      ];
  } elseif ($videoUrl !== '') {
      $galleryItems[] = [
          'type' => 'video',
          'provider' => 'external',
          'embedUrl' => null,
          'thumbUrl' => null,
          'externalUrl' => $videoUrl,
      ];
  }
  $price = $vehicle->price;
  $overview = $vehicle->overview ?: $vehicle->description;
  $productVariants = $productVariants ?? collect();
  $displayColors = $productVariants->isNotEmpty()
    ? $productVariants->map(fn ($v) => $v->colorOption?->value)->filter()->unique()->values()
    : collect(array_filter([$vehicle->exterior_color]));
  if ($displayColors->isEmpty()) {
    $displayColors = collect([__('Onyx'), __('Ivory')]);
  }
  $categoryCrumb = $vehicle->makeOption?->value ?: __('Collections');
@endphp

<main class="luxe-store pt-24 max-w-max-container mx-auto px-margin-mobile md:px-gutter pb-section-py-mobile md:pb-section-py-desktop">
  <nav class="py-6 md:py-8" aria-label="{{ __('Breadcrumb') }}">
    <ul class="flex flex-wrap items-center gap-2 font-label-caps text-label-caps text-on-surface-variant tracking-widest uppercase">
      <li><a class="hover:text-primary transition-colors" href="{{ route('home') }}">{{ __('Home') }}</a></li>
      <li>/</li>
      <li><a class="hover:text-primary transition-colors" href="{{ route('shop.index') }}">{{ $categoryCrumb }}</a></li>
      <li>/</li>
      <li class="text-primary">{{ $vehicle->title }}</li>
    </ul>
  </nav>

  <div class="flex flex-col md:flex-row gap-10 lg:gap-24 mb-section-py-mobile md:mb-section-py-desktop">
    <div class="w-full md:w-1/2 space-y-4">
      @include('pages.inventory.partials.product-luxe-gallery', [
        'vehicle' => $vehicle,
        'galleryUrls' => $galleryUrls,
        'galleryItems' => $galleryItems,
        'youtubeId' => $youtubeId,
      ])
    </div>

    <div class="w-full md:w-1/2 flex flex-col pt-0 md:pt-4">
      @include('pages.inventory.partials.product-luxe-purchase', [
        'vehicle' => $vehicle,
        'price' => $price,
        'overview' => $overview,
        'productVariants' => $productVariants,
        'displayColors' => $displayColors,
        'isFavorited' => $isFavorited ?? false,
      ])
    </div>
  </div>

  @if ($similarVehicles->isNotEmpty())
    <section class="py-section-py-mobile md:py-section-py-desktop relative overflow-hidden">
      <div class="luxe-product-pattern absolute inset-0 pointer-events-none" aria-hidden="true"></div>
      <div class="flex justify-between items-end mb-8 md:mb-12 relative z-10">
        <h2 class="font-headline-md text-headline-md text-primary uppercase tracking-widest">{{ __('You May Also Like') }}</h2>
        <a href="{{ route('shop.index') }}" class="font-label-caps text-label-caps text-on-surface-variant border-b border-outline hover:text-primary transition-colors pb-1">{{ __('VIEW ALL') }}</a>
      </div>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 relative z-10">
        @foreach ($similarVehicles->take(4) as $item)
          @php $itemCover = $item->images->first(); @endphp
          <a href="{{ route('product.show', ['slug' => $item->slug]) }}" class="group block">
            <div class="aspect-[3/4] overflow-hidden bg-surface-container mb-4 relative">
              @if ($item->is_special)
                <span class="absolute top-4 left-4 bg-primary text-on-primary font-label-caps text-[10px] px-2 py-1 uppercase tracking-widest z-10">{{ __('Limited') }}</span>
              @endif
              @if ($itemCover)
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="{{ \App\Support\VehicleImageUrl::url($itemCover->path) }}" alt="{{ $item->title }}" loading="lazy" />
              @endif
            </div>
            <div class="space-y-1">
              <h3 class="font-body-md text-body-md text-primary uppercase tracking-tight line-clamp-2">{{ $item->title }}</h3>
              <p class="font-body-md text-sm text-on-surface-variant">
                @if (! is_null($item->price)){{ format_currency($item->price) }}@else {{ __('Ask') }}@endif
              </p>
            </div>
          </a>
        @endforeach
      </div>
    </section>
  @endif

  @if ($vehicle->status === 'approved')
    <section class="border-t border-outline-variant pt-10 md:pt-12 mt-8">
      <h2 class="font-headline-md text-headline-md text-primary uppercase mb-6">{{ __('Contact') }}</h2>
      <form method="post" action="{{ route('inventory.inquiry', ['slug' => $vehicle->slug]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">
        @csrf
        <div>
          <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-2">{{ __('Your name') }}*</label>
          <input class="w-full bg-transparent border-b border-outline-variant py-3 font-body-md focus:border-primary focus:outline-none" type="text" name="sender_name" value="{{ old('sender_name', auth()->user()?->name) }}" required />
        </div>
        <div>
          <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-2">{{ __('Email') }}*</label>
          <input class="w-full bg-transparent border-b border-outline-variant py-3 font-body-md focus:border-primary focus:outline-none" type="email" name="sender_email" value="{{ old('sender_email', auth()->user()?->email) }}" required />
        </div>
        <div class="md:col-span-2">
          <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-2">{{ __('Message') }}</label>
          <textarea class="w-full bg-transparent border-b border-outline-variant py-3 font-body-md focus:border-primary focus:outline-none resize-none" rows="4" name="message" required>{{ old('message') }}</textarea>
        </div>
        <div class="md:col-span-2">
          <button type="submit" class="bg-primary text-on-primary px-10 py-4 font-button-text uppercase tracking-widest hover:opacity-90 transition-opacity">{{ __('Send message') }}</button>
        </div>
      </form>
    </section>
  @endif
</main>
@endsection

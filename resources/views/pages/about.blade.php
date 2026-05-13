@extends('layouts.site')

@section('content')
@php
    $heroImg = \App\Support\VehicleImageUrl::url($sections['hero_image'] ?? 'asset/images/media/about-hero-bg.jpg');
    $valGrid1 = \App\Support\VehicleImageUrl::url($sections['values_grid_1'] ?? 'asset/images/media/about-values-1.jpg');
    $valGrid2 = \App\Support\VehicleImageUrl::url($sections['values_grid_2'] ?? 'asset/images/media/about-values-2.jpg');
    $valGrid3 = \App\Support\VehicleImageUrl::url($sections['values_grid_3'] ?? 'asset/images/media/about-values-3.jpg');
    $valGrid4 = \App\Support\VehicleImageUrl::url($sections['values_grid_4'] ?? 'asset/images/media/about-values-4.jpg');

    $rawGallery = trim((string) ($sections['gallery'] ?? '[]'));
    $galleryPaths = [];
    try {
        $galleryPaths = json_decode($rawGallery, true);
        if (!is_array($galleryPaths)) {
            $galleryPaths = [];
        }
    } catch (\Exception $e) {
        $galleryPaths = [];
    }

    // Fallback to defaults if empty
    if (empty($galleryPaths)) {
        $galleryPaths = [
            'asset/images/media/about-gallery-1.jpg',
            'asset/images/media/about-gallery-2.jpg',
            'asset/images/media/about-gallery-3.jpg',
            'asset/images/media/about-gallery-4.jpg',
        ];
    }

    $gallery = collect($galleryPaths)
      ->filter(fn ($p) => !empty(trim((string) $p)))
      ->map(fn ($p) => \App\Support\VehicleImageUrl::url($p))
      ->values();

@endphp

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>

<!-- Hero Section -->
<section class="relative min-h-[80vh] flex items-start overflow-hidden pt-24 md:pt-32">
    <div class="flex flex-col md:flex-row w-full h-full">
        <div class="w-full md:w-1/2 relative h-[500px] md:h-screen">
            <img alt="Hero Image" class="w-full h-full object-cover" src="{{ $heroImg }}"/>
            <div class="absolute bottom-12 right-0 bg-primary text-slate-900 px-8 py-6 flex flex-col items-center justify-center transform translate-x-1/4 shadow-2xl">
                <span class="text-4xl font-black font-headline">{{ $sections['hero_stat_value'] ?? '25+' }}</span>
                <span class="text-xs font-bold font-label tracking-tighter uppercase">{{ $sections['hero_stat_label'] ?? 'Years of Excellence' }}</span>
            </div>
        </div>
        <div class="w-full md:w-1/2 flex items-center px-12 md:px-24 py-20 bg-white">
            <div class="max-w-xl">
                <h2 class="text-sm font-label font-bold text-primary tracking-[0.3em] uppercase mb-4">Established {{ $sections['established_year'] ?? '1999' }}</h2>
                <h1 class="text-5xl md:text-6xl font-black font-headline text-on_surface leading-[0.9] mb-8 uppercase">
                    @php
                        $heading = $sections['heading'] ?? 'WELCOME TO THE MOTORS';
                        if (str_contains($heading, 'THE MOTORS')) {
                            $parts = explode('THE MOTORS', $heading);
                            $firstPart = trim($parts[0]);
                            $lastPart = 'THE MOTORS';
                        } else {
                            $words = explode(' ', $heading);
                            $lastPart = array_pop($words);
                            $firstPart = implode(' ', $words);
                        }
                    @endphp
                    {!! nl2br(e($firstPart)) !!} <br/>
                    <span class="text-primary">{{ $lastPart }}</span>
                </h1>
                <div class="text-base font-body text-slate-600 leading-relaxed mb-10 [&_p]:mb-3 [&_p:last-child]:mb-0 [&_ul]:my-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:my-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_a]:text-primary [&_a]:underline">
                    @if (trim((string) ($sections['intro'] ?? '')) !== '')
                        {!! $sections['intro'] !!}
                    @else
                        <p>{{ __('Experience the pinnacle of automotive engineering and white-glove service.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Values -->
<section class="py-32 bg-page_bg overflow-hidden">
    <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-2 gap-20 items-center">
        <div>
            <h2 class="text-5xl font-black font-headline text-on_surface mb-12 tracking-tight uppercase">
                @php
                    $vTitle = $sections['values_title'] ?? 'CORE VALUES';
                    $vWords = explode(' ', $vTitle);
                    $vLast = array_pop($vWords);
                    $vFirst = implode(' ', $vWords);
                @endphp
                {{ $vFirst }} <span class="text-primary">{{ $vLast }}</span>
            </h2>
            <div class="space-y-10">
                @foreach([1, 2, 3] as $i)
                    @php
                        $vT = $sections['val_'.$i.'_title'] ?? '';
                        $vB = $sections['val_'.$i.'_body'] ?? '';
                    @endphp
                    @if($vT)
                    <div class="flex items-start gap-6 group">
                        <div class="mt-1 flex-shrink-0 w-8 h-8 flex items-center justify-center bg-primary text-slate-900">
                            <span class="material-symbols-outlined font-bold">check</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold font-headline mb-2 uppercase tracking-wide">{{ $vT }}</h3>
                            <p class="text-slate-600 font-body">{{ $vB }}</p>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 h-[600px]">
            <div class="space-y-4 pt-12">
                <div class="h-1/2 overflow-hidden bg-slate-200">
                    <img alt="Value 1" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700" src="{{ $valGrid1 }}"/>
                </div>
                <div class="h-1/2 overflow-hidden bg-slate-200">
                    <img alt="Value 2" class="w-full h-full object-cover" src="{{ $valGrid2 }}"/>
                </div>
            </div>
            <div class="space-y-4">
                <div class="h-1/2 overflow-hidden bg-slate-200">
                    <img alt="Value 3" class="w-full h-full object-cover" src="{{ $valGrid3 }}"/>
                </div>
                <div class="h-1/2 overflow-hidden bg-slate-200 relative">
                    <div class="absolute inset-0 bg-primary/20 mix-blend-multiply"></div>
                    <img alt="Value 4" class="w-full h-full object-cover" src="{{ $valGrid4 }}"/>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Media Gallery (Motors reference palette) -->
@if($gallery->isNotEmpty())
<section class="bg-[#232628] py-24 px-6 md:px-12 lg:px-24">
  <div class="max-w-7xl mx-auto w-full">
    <div class="text-center mb-16">
      <h2 class="text-white font-headline text-3xl md:text-4xl font-extrabold tracking-tight uppercase">
        {{ strtoupper((string) ($sections['gallery_title'] ?? 'Media Gallery')) }}
      </h2>
      <div class="motors-colored-separator">
        <div class="first-long"></div>
        <div class="last-short"></div>
      </div>
    </div>

    <div class="motors-carousel motors-carousel--gallery" data-simple-carousel data-carousel-type="gallery" data-carousel-loop="1">
      <div class="motors-carousel-viewport overflow-hidden" data-carousel-viewport>
        <div class="motors-carousel-track flex gap-1" data-carousel-track>
          @foreach ($gallery as $img)
            <div class="w-full sm:w-1/3 lg:w-1/4 shrink-0 px-0.5" data-carousel-slide>
              <a href="{{ $img }}" target="_blank" rel="noopener noreferrer" class="aspect-[4/3] overflow-hidden group cursor-pointer block bg-black/10">
                <img src="{{ $img }}" alt="{{ __('Gallery image') }}" data-gallery-image class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" decoding="async" />
              </a>
            </div>
          @endforeach
        </div>
      </div>

      <div class="flex items-center justify-center mt-12 gap-8">
        <button type="button" class="motors-gallery-chevron" data-carousel-prev aria-label="{{ __('Previous') }}">
          <span class="material-symbols-outlined text-3xl">chevron_left</span>
        </button>
        <div class="motors-gallery-dots flex items-center gap-3" data-carousel-dots></div>
        <button type="button" class="motors-gallery-chevron" data-carousel-next aria-label="{{ __('Next') }}">
          <span class="material-symbols-outlined text-3xl">chevron_right</span>
        </button>
      </div>
    </div>
  </div>
</section>
@endif

<!-- Quick Links -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach([1, 2, 3] as $i)
            @php
                $qT = $sections['adv_'.$i.'_title'] ?? '';
                $qB = $sections['adv_'.$i.'_body'] ?? '';
                $qI = $sections['adv_'.$i.'_icon'] ?? 'directions_car';
                $qH = $sections['adv_'.$i.'_href'] ?? '#';
            @endphp
            @if($qT)
            <div class="bg-page_bg p-10 flex flex-col items-center text-center group hover:bg-white transition-all shadow-xl shadow-transparent hover:shadow-slate-200 @if($i == 2) border-b-4 border-primary @endif">
                <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center text-primary mb-6 transition-colors group-hover:bg-primary group-hover:text-slate-900">
                    <span class="material-symbols-outlined text-3xl">{{ $qI }}</span>
                </div>
                <h3 class="text-xl font-black font-headline mb-4 uppercase text-on_surface">{{ $qT }}</h3>
                <p class="text-slate-600 font-body mb-8">{{ $qB }}</p>
                <a class="font-label font-bold text-sm uppercase tracking-widest text-primary hover:underline flex items-center gap-2" href="{{ $qH }}">
                    {{ str_contains($qT, 'sell') ? 'Appraise Now' : (str_contains($qT, 'new car') ? 'Browse Inventory' : 'Book Appointment') }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            @endif
        @endforeach
    </div>
</section>

@endsection
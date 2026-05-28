@extends('layouts.site')

@php
  $s = $sections ?? [];
  $site = $site ?? [];
  $heroImg = \App\Support\VehicleImageUrl::url($s['hero_image'] ?? 'asset/images/media/about-hero-bg.jpg');
  $artisanImg = \App\Support\VehicleImageUrl::url($s['artisan_image'] ?? 'asset/images/media/about-values-1.jpg');
  $storyCtaHref = trim($s['story_cta_href'] ?? '/shop');
  if ($storyCtaHref !== '' && ! str_starts_with($storyCtaHref, 'http')) {
      $storyCtaHref = url($storyCtaHref);
  }
@endphp

@push('head')
<style>
  .luxe-natural-pattern {
    background-image: url("https://www.transparenttextures.com/patterns/natural-paper.png");
    opacity: 0.03;
    pointer-events: none;
  }

  /* Hero / editorial images: center crop on all viewports (not Tailwind-dependent). */
  .about-page-hero__image,
  .about-page-media__image {
    object-fit: cover;
    object-position: center center;
  }
</style>
@endpush

@section('content')
  <div class="relative overflow-x-hidden">
    <div class="absolute inset-0 luxe-natural-pattern"></div>

    <section class="relative w-full h-[55vh] min-h-[18rem] max-h-[26rem] sm:max-h-[28rem] md:h-[75vh] md:min-h-0 md:max-h-none lg:h-[80vh] overflow-hidden">
      <img
        alt=""
        class="about-page-hero__image absolute inset-0 w-full h-full"
        src="{{ $heroImg }}"
      />
      <div class="absolute inset-0 z-[1] bg-black/20 flex flex-col justify-end pb-10 sm:pb-12 md:pb-24">
        <div class="max-w-max-container mx-auto px-gutter w-full relative z-10">
          <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-surface max-w-3xl leading-none">
            {{ $s['hero_title'] ?? __('The Hands Behind the Heritage') }}
          </h1>
        </div>
      </div>
    </section>

    <section class="py-section-py-mobile md:py-section-py-desktop max-w-max-container mx-auto px-gutter relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-24 items-start">
        <div class="md:col-span-5">
          <span class="font-label-caps text-label-caps text-secondary tracking-widest uppercase mb-4 block">{{ $s['philosophy_kicker'] ?? __('Our Philosophy') }}</span>
          <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-8">{{ $s['philosophy_title'] ?? __('Modern Heritage') }}</h2>
          @if(trim($s['philosophy_quote'] ?? '') !== '')
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed italic">{{ $s['philosophy_quote'] }}</p>
          @endif
        </div>
        <div class="md:col-span-7 space-y-6">
          @if(trim($s['story_paragraph_1'] ?? '') !== '')
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $s['story_paragraph_1'] }}</p>
          @endif
          @if(trim($s['story_paragraph_2'] ?? '') !== '')
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $s['story_paragraph_2'] }}</p>
          @endif
          @if(trim($s['story_cta_text'] ?? '') !== '')
            <div class="pt-8">
              <a href="{{ $storyCtaHref }}" class="inline-block bg-primary text-on-primary font-button-text text-button-text px-10 py-4 uppercase tracking-widest transition-transform duration-300 hover:scale-[1.02]">
                {{ $s['story_cta_text'] }}
              </a>
            </div>
          @endif
        </div>
      </div>
    </section>

    <section class="bg-surface-container py-section-py-mobile md:py-section-py-desktop relative z-10">
      <div class="max-w-max-container mx-auto px-gutter">
        <div class="flex flex-col md:flex-row gap-12 items-center">
          <div class="w-full md:w-1/2 aspect-[4/5] overflow-hidden">
            <img alt="" class="about-page-media__image w-full h-full" src="{{ $artisanImg }}" />
          </div>
          <div class="w-full md:w-1/2 md:pl-8 lg:pl-16">
            <span class="font-label-caps text-label-caps text-secondary tracking-widest uppercase mb-4 block">{{ $s['artisan_kicker'] ?? '' }}</span>
            <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-6">{{ $s['artisan_title'] ?? '' }}</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mb-8 leading-relaxed">{{ $s['artisan_body'] ?? '' }}</p>
            @if(trim($s['artisan_location_label'] ?? '') !== '' || trim($s['artisan_location_detail'] ?? '') !== '')
              <div class="border-l-2 border-primary pl-6 py-2">
                @if(trim($s['artisan_location_label'] ?? '') !== '')
                  <p class="font-label-caps text-label-caps text-primary uppercase">{{ $s['artisan_location_label'] }}</p>
                @endif
                @if(trim($s['artisan_location_detail'] ?? '') !== '')
                  <p class="font-body-md text-body-md text-on-surface-variant">{{ $s['artisan_location_detail'] }}</p>
                @endif
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>

    <section class="py-section-py-mobile md:py-section-py-desktop max-w-max-container mx-auto px-gutter relative z-10">
      <div class="text-center mb-16">
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary">{{ $s['values_title'] ?? __('Core Values') }}</h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-0.5 bg-outline-variant border border-outline-variant">
        @foreach([
          ['icon' => 'sparkles', 'title' => $s['val_1_title'] ?? '', 'body' => $s['val_1_body'] ?? ''],
          ['icon' => 'heart', 'title' => $s['val_2_title'] ?? '', 'body' => $s['val_2_body'] ?? ''],
          ['icon' => 'document', 'title' => $s['val_3_title'] ?? '', 'body' => $s['val_3_body'] ?? ''],
        ] as $value)
          <div class="bg-surface p-12 text-center group transition-colors duration-500 hover:bg-primary-container">
            <x-icon :name="$value['icon']" class="w-10 h-10 text-secondary mb-6 mx-auto block group-hover:text-surface transition-colors" />
            <h3 class="font-headline-md text-headline-md text-primary mb-4 group-hover:text-surface transition-colors">{{ $value['title'] }}</h3>
            <p class="font-body-md text-body-md text-on-surface-variant group-hover:text-inverse-on-surface transition-colors">{{ $value['body'] }}</p>
          </div>
        @endforeach
      </div>
    </section>

    <section class="bg-surface-container-high py-24 relative z-10">
      <div class="max-w-[600px] mx-auto px-gutter text-center">
        <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-4 uppercase tracking-tighter">{{ $s['newsletter_title'] ?? __('Join the Circle') }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mb-10">{{ $s['newsletter_body'] ?? '' }}</p>
        @if (((string) ($site['newsletter_enabled'] ?? '0')) === '1')
          @php $note = trim((string) ($site['newsletter_note'] ?? '')); @endphp
          @if ($note !== '')
            <p class="font-body-md text-body-md text-on-surface-variant mb-6">{{ $note }}</p>
          @endif
          <form class="flex flex-col md:flex-row gap-0" method="post" action="{{ route('newsletter.subscribe') }}">
            @csrf
            <input class="flex-grow bg-transparent border-b border-primary px-4 py-4 focus:ring-0 focus:outline-none placeholder:text-outline font-label-caps text-label-caps uppercase" name="email" type="email" placeholder="{{ __('Your email address') }}" required />
            <button class="bg-primary text-on-primary px-8 py-4 uppercase font-button-text text-button-text tracking-widest hover:bg-on-surface hover:text-surface transition-colors" type="submit">{{ __('Subscribe') }}</button>
          </form>
        @endif
      </div>
    </section>
  </div>
@endsection

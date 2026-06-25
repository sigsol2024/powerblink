@extends('layouts.site')

@php
  $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/coaching-team-powerblink-fc-024.jpg');
  $featured = $coaches->first();
@endphp

@section('content')
  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImage,
    'eyebrow' => __('Our Staff'),
    'title' => $page->title,
    'subtitle' => $page->meta_description ?? __('Licensed coaches dedicated to holistic player development — technical, tactical, and character growth.'),
    'primaryCta' => ['href' => route('registration.wizard'), 'label' => __('Join the Academy')],
  ])

  @if ($featured)
    <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
      <div class="bg-primary-container rounded-[2rem] overflow-hidden grid grid-cols-1 lg:grid-cols-2 fade-up">
        <div class="p-10 lg:p-16 flex flex-col justify-center">
          <span class="text-secondary-fixed font-bold uppercase tracking-widest text-label-caps mb-4">{{ __('Head of Coaching') }}</span>
          <h2 class="font-headline-lg text-on-primary mb-3">{{ $featured->name }}</h2>
          @if ($featured->title)
            <p class="text-on-primary-container mb-6">{{ $featured->title }}</p>
          @endif
          @if ($featured->bio)
            <p class="font-body-lg text-on-primary-container/90 leading-relaxed line-clamp-5">{{ $featured->bio }}</p>
          @endif
        </div>
        <div class="min-h-[320px] lg:min-h-0">
          @if ($featured->photo)
            <img src="{{ \App\Support\MediaImageUrl::url($featured->photo->file_path) }}" alt="{{ $featured->name }}" class="w-full h-full object-cover object-top" />
          @endif
        </div>
      </div>
    </section>
  @endif

  <section class="py-section-gap bg-surface-container">
    <div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
      <div class="text-center mb-12 fade-up">
        <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ __('The Coaching Team') }}</span>
        <h2 class="font-headline-lg text-primary mt-2">{{ __('Meet Our Licensed Staff') }}</h2>
      </div>

      @if ($coaches->isEmpty())
        <p class="text-on-surface-variant text-center">{{ __('Coaching profiles coming soon.') }}</p>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter">
          @foreach ($coaches as $coach)
            <article class="bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/30 card-hover fade-up">
              @if ($coach->photo)
                <img src="{{ \App\Support\MediaImageUrl::url($coach->photo->file_path) }}" alt="{{ $coach->name }}" class="w-full h-64 object-cover object-top" />
              @else
                <div class="w-full h-64 bg-primary-container flex items-center justify-center">
                  <span class="material-symbols-outlined text-6xl text-on-primary-container">person</span>
                </div>
              @endif
              <div class="p-6">
                <h3 class="font-headline-md text-primary">{{ $coach->name }}</h3>
                @if ($coach->title)
                  <p class="text-secondary text-sm font-semibold mt-1">{{ $coach->title }}</p>
                @endif
                @if ($coach->specialization)
                  <p class="text-xs text-on-surface-variant mt-2">{{ $coach->specialization }}</p>
                @endif
                @if ($coach->license_level)
                  <span class="inline-block mt-3 text-xs bg-surface-container px-2 py-1 rounded font-label-caps uppercase">{{ $coach->license_level }}</span>
                @endif
                @if ($coach->bio)
                  <p class="text-sm text-on-surface-variant mt-4 line-clamp-4">{{ $coach->bio }}</p>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  </section>
@endsection

@extends('layouts.site')

@php
  $s = $sections ?? [];
  $heroImage = \App\Support\PlaceholderMedia::url($s['hero_image'] ?? 'asset/images/powerblink/about-us-powerblink-fc-001.jpg');
  $storyImage = \App\Support\PlaceholderMedia::url($s['artisan_image'] ?? 'asset/images/powerblink/about-us-powerblink-fc-009.jpg');
  $timeline = [
    ['year' => '2025', 'title' => __('Academy Launch'), 'body' => $s['story_paragraph_1'] ?? __('Official opening of our state-of-the-art Ibeju Lekki facility, setting a new benchmark for youth training infrastructure in Nigeria.'), 'image' => 'about-us-powerblink-fc-002.jpg'],
    ['year' => '2025', 'title' => __('Independence Day Cup'), 'body' => $s['story_paragraph_2'] ?? __('Over 600 young athletes competed in our flagship tournament, marking our arrival as a major force in national youth development.'), 'image' => 'about-us-powerblink-fc-003.jpg'],
    ['year' => __('2026 & Beyond'), 'title' => __('Global Expansion'), 'body' => __('Solidifying international partnerships with European clubs and launching academic scholarship pathways for elite player-students.'), 'image' => 'about-us-powerblink-fc-004.jpg'],
  ];
  $values = [
    ['icon' => 'target', 'title' => $s['val_1_title'] ?? __('Mission'), 'body' => $s['val_1_body'] ?? __('To discover, develop, mentor, and showcase young football talent through structured coaching.')],
    ['icon' => 'visibility', 'title' => $s['val_2_title'] ?? __('Vision'), 'body' => $s['val_2_body'] ?? __('To be Nigeria\'s most professional youth football academy, recognized globally for producing elite athletes.')],
    ['icon' => 'diamond', 'title' => $s['val_3_title'] ?? __('Core Values'), 'body' => $s['val_3_body'] ?? __('Discipline, development, and community united around shared goals on and off the pitch.')],
  ];
@endphp

@section('content')
  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImage,
    'eyebrow' => __('About the Academy'),
    'title' => $s['hero_title'] ?? __('Our Story'),
    'subtitle' => $s['philosophy_quote'] ?? __('Rooted in the heart of Ibeju Lekki, Powerblink FC is a high-performance ecosystem dedicated to transforming raw potential into world-class athletic excellence.'),
    'primaryCta' => ['href' => route('programs'), 'label' => __('Explore Programs')],
    'secondaryCta' => ['href' => route('registration.wizard'), 'label' => __('Apply for Trials')],
  ])

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
      @foreach ($values as $i => $card)
        <div class="bg-surface-container-lowest p-10 rounded-xl shadow-sm border border-outline-variant/20 card-hover fade-up" @if($i) style="transition-delay: {{ $i * 0.1 }}s" @endif>
          <div class="w-16 h-16 bg-primary-container rounded-lg flex items-center justify-center mb-8">
            <x-icon name="{{ $card['icon'] }}" class="w-10 h-10 text-secondary-fixed" />
          </div>
          <h3 class="font-headline-md text-primary mb-4">{{ $card['title'] }}</h3>
          <p class="font-body-md text-on-surface-variant leading-relaxed">{{ $card['body'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  <section class="py-section-gap bg-surface-container-low">
    <div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <div class="fade-up">
        <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ $s['philosophy_kicker'] ?? __('Our Philosophy') }}</span>
        <h2 class="font-headline-lg text-primary mt-2 mb-6">{{ $s['philosophy_title'] ?? __('Excellence On and Off the Pitch') }}</h2>
        <p class="font-body-lg text-on-surface-variant leading-relaxed mb-6">{{ $s['story_paragraph_1'] ?? '' }}</p>
        <p class="font-body-md text-on-surface-variant leading-relaxed italic border-l-4 border-secondary pl-6">{{ $s['philosophy_quote'] ?? '' }}</p>
      </div>
      <div class="rounded-2xl overflow-hidden shadow-xl fade-up">
        <img src="{{ $storyImage }}" alt="" class="w-full aspect-video object-cover" />
      </div>
    </div>
  </section>

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto overflow-hidden">
    <div class="text-center mb-16 fade-up">
      <h2 class="font-headline-lg text-primary mb-4">{{ __('Our Journey') }}</h2>
      <div class="w-24 h-1 bg-secondary mx-auto rounded-full"></div>
    </div>
    <div class="space-y-16 md:space-y-24">
      @foreach ($timeline as $i => $event)
        <div class="relative grid grid-cols-1 md:grid-cols-2 gap-8 items-center fade-up" @if($i) style="transition-delay: 0.15s" @endif>
          <div class="{{ $i % 2 === 1 ? 'md:order-2' : '' }}">
            <span class="font-stat-md text-stat-md text-secondary block mb-2">{{ $event['year'] }}</span>
            <h3 class="font-headline-md text-primary mb-3">{{ $event['title'] }}</h3>
            <p class="font-body-md text-on-surface-variant">{{ $event['body'] }}</p>
          </div>
          <div class="rounded-xl overflow-hidden shadow-xl aspect-video {{ $i % 2 === 1 ? 'md:order-1' : '' }}">
            <img src="{{ \App\Support\PlaceholderMedia::url('asset/images/powerblink/'.$event['image']) }}" alt="" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
          </div>
        </div>
      @endforeach
    </div>
  </section>

  @if (($leadership ?? collect())->isNotEmpty())
    <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
      <div class="mb-12 text-center fade-up">
        <h2 class="font-headline-lg text-primary">{{ __('Board & Leadership') }}</h2>
        <p class="text-on-surface-variant max-w-xl mx-auto mt-4">{{ __('The visionary minds steering Powerblink FC toward international football excellence.') }}</p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
        @foreach ($leadership as $member)
          <div class="group fade-up">
            <div class="aspect-[3/4] bg-surface-container rounded-xl overflow-hidden relative mb-6">
              @if ($member->photo)
                <img src="{{ \App\Support\MediaImageUrl::url($member->photo->file_path) }}" alt="{{ $member->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              @endif
              <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-primary-container to-transparent opacity-90"></div>
              <div class="absolute bottom-6 left-6">
                <span class="block text-white font-bold text-lg">{{ $member->name }}</span>
                <span class="block text-secondary-fixed text-sm uppercase font-bold tracking-widest">{{ $member->title }}</span>
              </div>
            </div>
            @if ($member->bio)
              <p class="text-on-surface-variant text-sm line-clamp-3">{{ $member->bio }}</p>
            @endif
          </div>
        @endforeach
      </div>
    </section>
  @endif

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop">
    <div class="max-w-container-max mx-auto bg-primary-container rounded-3xl overflow-hidden flex flex-col md:flex-row items-center fade-up">
      <div class="p-10 md:p-16 md:w-3/5">
        <h2 class="font-headline-lg text-on-primary mb-6">{{ __('Ready to Join the Family?') }}</h2>
        <p class="font-body-lg text-on-primary-container mb-8">{{ __('Trials are now open for our elite performance squads. Secure your spot and start your journey toward professional football excellence.') }}</p>
        <a href="{{ route('registration.wizard') }}" class="inline-flex items-center bg-secondary text-on-secondary px-8 py-4 rounded-xl font-bold hover:opacity-90 transition-opacity">
          {{ __('Start Application') }}
        </a>
      </div>
    </div>
  </section>
@endsection

@extends('layouts.site')

@php
  $s = $sections ?? [];
  $heroTitle = $s['hero_title'] ?? __('Developing Tomorrow\'s Football Stars Today');
  $heroSubtitle = $s['hero_subtitle'] ?? __('Structured football development for players aged 7–15 through elite coaching, competitive tournaments, and professional mentorship in Ibeju Lekki.');
  $heroBg = \App\Support\PlaceholderMedia::url($s['hero_image'] ?? 'asset/images/powerblink/home-powerblink-fc-044.jpg');
  $heroCtaHref = $s['hero_cta_href'] ?? '/register';
  $heroCtaUrl = \Illuminate\Support\Str::startsWith($heroCtaHref, ['http://', 'https://']) ? $heroCtaHref : url($heroCtaHref);
  $programsTitle = $s['shop_categories_title'] ?? __('Development Programs');
  $aboutImage = \App\Support\PlaceholderMedia::url($s['about_preview_image'] ?? 'asset/images/powerblink/home-powerblink-fc-045.jpg');
  $coach = $featuredCoach ?? null;
  $coachImage = $coach?->photo
    ? \App\Support\MediaImageUrl::url($coach->photo->file_path)
    : \App\Support\PlaceholderMedia::url('asset/images/powerblink/coaching-team-powerblink-fc-024.jpg');
  $bento = [
    ['icon' => 'verified_user', 'title' => __('Certified Coaches'), 'body' => __('Learn from experts with FCAAN and international certifications dedicated to youth development.')],
    ['icon' => 'account_tree', 'title' => __('Structured Development'), 'body' => __('A curriculum-based approach tailored to different age groups and skill levels.')],
    ['icon' => 'trophy', 'title' => __('Tournament Exposure'), 'body' => __('Regular participation in high-stakes local and regional competitive football tournaments.')],
    ['icon' => 'psychology', 'title' => __('Character Building'), 'body' => __('Mentorship focusing on discipline, leadership, and emotional intelligence on and off the pitch.')],
    ['icon' => 'monitoring', 'title' => __('Player Monitoring'), 'body' => __('Data-driven performance tracking to ensure continuous improvement and growth.')],
    ['icon' => 'rocket_launch', 'title' => __('Pathway Opportunities'), 'body' => __('Direct links to professional clubs and scouting networks for elite performing talents.')],
  ];
@endphp

@section('content')
  {{-- Hero --}}
  <section class="relative h-[90vh] min-h-[560px] md:min-h-[700px] flex items-center overflow-hidden -mt-20 pt-20">
    <div class="absolute inset-0 pb-hero-bg" style="background-image: url('{{ $heroBg }}')"></div>
    <div class="absolute inset-0 cinematic-overlay" aria-hidden="true"></div>
    <div class="relative z-10 w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop">
      <div class="max-w-4xl">
        <span class="inline-block bg-secondary-container text-on-secondary-fixed px-4 py-1 rounded-full text-label-caps mb-6 uppercase tracking-widest">{{ $s['hero_eyebrow'] ?? __('Elite Academy Trials Open') }}</span>
        <h1 class="font-display-hero text-headline-lg-mobile md:text-display-hero text-on-primary mb-6">{{ $heroTitle }}</h1>
        @if ($heroSubtitle !== '')
          <p class="font-body-lg text-on-primary-container max-w-2xl mb-10 leading-relaxed">{{ $heroSubtitle }}</p>
        @endif
        <div class="flex flex-wrap gap-4 mb-12">
          <a href="{{ $heroCtaUrl }}" class="inline-flex items-center bg-secondary-container text-on-secondary-fixed px-8 py-4 rounded-xl font-headline-md text-sm hover:bg-secondary-fixed hover:shadow-[0_0_20px_rgba(100,255,146,0.4)] transition-all">
            {{ $s['hero_cta_text'] ?? __('Register Now') }}
          </a>
          <a href="{{ route('programs') }}" class="inline-flex items-center border-2 border-on-primary text-on-primary px-8 py-4 rounded-xl font-headline-md text-sm hover:bg-on-primary hover:text-primary transition-all">
            {{ __('Explore Programs') }}
          </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-8 border-t border-white/20 pt-8 sm:pt-10">
          <div>
            <div class="font-stat-xl text-stat-xl text-secondary-fixed">{{ number_format((int) ($stats['active_players'] ?? 0)) }}+</div>
            <div class="font-label-caps text-on-primary-container uppercase">{{ __('Players Reached') }}</div>
          </div>
          <div>
            <div class="font-stat-xl text-stat-xl text-secondary-fixed">{{ number_format((int) ($stats['programs_count'] ?? 0)) }}</div>
            <div class="font-label-caps text-on-primary-container uppercase">{{ __('Age Categories') }}</div>
          </div>
          <div>
            <div class="font-stat-xl text-stat-xl text-secondary-fixed">{{ number_format((int) ($stats['coaches_count'] ?? 0)) }}+</div>
            <div class="font-label-caps text-on-primary-container uppercase">{{ __('Coaches') }}</div>
          </div>
          <div>
            <div class="font-stat-xl text-stat-xl text-secondary-fixed">{{ number_format((int) ($stats['seasons_count'] ?? 1)) }}</div>
            <div class="font-label-caps text-on-primary-container uppercase">{{ __('Mission') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Why Powerblink bento --}}
  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto overflow-hidden">
    <div class="text-center mb-16 fade-up">
      <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ __('The Powerblink Edge') }}</span>
      <h2 class="font-headline-lg text-primary mt-2">{{ __('Why Our Academy Stands Out') }}</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
      @foreach ($bento as $i => $card)
        <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border border-outline-variant/30 card-hover fade-up" @if($i > 0) style="transition-delay: {{ $i * 0.1 }}s" @endif>
          <div class="w-14 h-14 bg-primary-container rounded-lg flex items-center justify-center mb-6 text-secondary-fixed">
            <x-icon name="{{ $card['icon'] }}" class="w-8 h-8" />
          </div>
          <h3 class="font-headline-md text-primary mb-3">{{ $card['title'] }}</h3>
          <p class="text-on-surface-variant text-sm leading-relaxed">{{ $card['body'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  {{-- About preview --}}
  <section class="bg-primary-container py-section-gap relative overflow-hidden">
    <div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none hidden md:block" aria-hidden="true">
      <img src="{{ asset('asset/images/powerblink/powerblink_logo.png') }}" alt="" class="w-full h-auto object-contain" />
    </div>
    <div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center relative z-10">
      <div class="fade-up">
        <h2 class="font-headline-lg text-on-primary mb-6">{{ $s['welcome_title'] ?? __('Elite Excellence in Ibeju Lekki') }}</h2>
        <p class="font-body-lg text-on-primary-container mb-8 leading-relaxed">{{ $s['welcome_body'] ?? __('Powerblink Football Club Limited is a launchpad for dreams — a safe, world-class environment where young athletes transform raw passion into professional competence.') }}</p>
        <a href="{{ route('about') }}" class="inline-flex items-center gap-2 text-secondary-fixed font-headline-md group">
          {{ __('Read Our Story') }}
          <x-icon name="arrow_forward" class="w-5 h-5 group-hover:translate-x-2 transition-transform" />
        </a>
      </div>
      <div class="relative fade-up">
        <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl border-4 border-white/10">
          <img src="{{ $aboutImage }}" alt="" class="w-full h-full object-cover" />
        </div>
      </div>
    </div>
  </section>

  {{-- Programs overview --}}
  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="flex justify-between items-end mb-12 fade-up">
      <div>
        <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ __('Player Pathways') }}</span>
        <h2 class="font-headline-lg text-primary mt-2">{{ $programsTitle }}</h2>
      </div>
      <a href="{{ route('programs') }}" class="hidden md:block text-primary font-bold border-b-2 border-primary pb-1 hover:text-secondary hover:border-secondary transition-colors">{{ __('View All Programs') }}</a>
    </div>

    @if ($programs->isEmpty())
      <p class="text-on-surface-variant">{{ __('Programs will be published soon.') }}</p>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-gutter">
        @foreach ($programs as $i => $program)
          <article class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant/30 hover:border-secondary transition-all flex flex-col h-full fade-up" @if($i > 0) style="transition-delay: {{ min($i * 0.1, 0.3) }}s" @endif>
            @if ($program->heroImage)
              <div class="h-48 overflow-hidden">
                <img src="{{ \App\Support\MediaImageUrl::url($program->heroImage->file_path) }}" alt="{{ $program->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              </div>
            @endif
            <div class="p-6 flex-grow flex flex-col">
              <div class="flex justify-between items-start gap-2 mb-4">
                <h3 class="font-headline-md text-primary">{{ $program->name }}</h3>
                @if ($program->age_group)
                  <span class="bg-surface-container-high px-3 py-1 rounded text-[10px] font-bold uppercase shrink-0">{{ $program->age_group }}</span>
                @endif
              </div>
              @if ($program->description)
                <p class="text-on-surface-variant text-sm mb-6 flex-grow line-clamp-3">{{ $program->description }}</p>
              @endif
              <a href="{{ route('registration.wizard') }}" class="block w-full py-3 bg-surface-container text-center rounded-lg font-bold text-primary border border-outline-variant hover:bg-primary hover:text-on-primary transition-all">{{ __('Apply Now') }}</a>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>

  {{-- Coach spotlight --}}
  @if ($coach)
    <section class="bg-surface-container py-section-gap">
      <div class="px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
        <div class="bg-primary-container rounded-[2rem] overflow-hidden grid grid-cols-1 lg:grid-cols-2 fade-up">
          <div class="p-10 lg:p-16 flex flex-col justify-center">
            <span class="text-secondary-fixed font-bold uppercase tracking-widest text-label-caps mb-6">{{ __('Masterclass Coaching') }}</span>
            <h2 class="font-headline-lg text-on-primary mb-4">{{ $coach->name }}</h2>
            @if ($coach->title || $coach->license_level)
              <div class="flex flex-wrap items-center gap-3 mb-8">
                @if ($coach->title)
                  <span class="bg-white/10 text-on-primary-container px-3 py-1 rounded-md text-sm border border-white/20">{{ $coach->title }}</span>
                @endif
                @if ($coach->license_level)
                  <span class="bg-white/10 text-on-primary-container px-3 py-1 rounded-md text-sm border border-white/20">{{ $coach->license_level }}</span>
                @endif
              </div>
            @endif
            @if ($coach->bio)
              <blockquote class="relative mb-8">
                <x-icon name="format_quote" class="w-12 h-12 absolute -top-4 -left-2 text-secondary-fixed opacity-30" />
                <p class="font-body-lg italic text-on-primary leading-relaxed pl-6 line-clamp-4">{{ $coach->bio }}</p>
              </blockquote>
            @endif
            <a href="{{ route('coaching') }}" class="inline-flex items-center gap-2 text-secondary-fixed font-bold group">
              {{ __('Meet the Team') }}
              <x-icon name="arrow_forward" class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </a>
          </div>
          <div class="h-72 lg:h-auto min-h-[320px]">
            <img src="{{ $coachImage }}" alt="{{ $coach->name }}" class="w-full h-full object-cover object-top" />
          </div>
        </div>
      </div>
    </section>
  @endif

  {{-- Tournament highlight --}}
  @php $featuredTournament = $featuredTournament ?? null; @endphp
  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
      <div class="lg:col-span-5 order-2 lg:order-1 fade-up">
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
          @foreach (['home-powerblink-fc-046.jpg', 'home-powerblink-fc-047.jpg', 'home-powerblink-fc-048.jpg', 'home-powerblink-fc-049.jpg'] as $img)
            <div class="aspect-square rounded-xl overflow-hidden bg-surface-container {{ $loop->iteration % 2 === 0 ? 'mt-6 sm:mt-8' : '' }} {{ $loop->iteration === 3 ? '-mt-6 sm:-mt-8' : '' }}">
              <img src="{{ \App\Support\PlaceholderMedia::url('asset/images/powerblink/'.$img) }}" alt="" class="w-full h-full object-cover" loading="lazy" />
            </div>
          @endforeach
        </div>
      </div>
      <div class="lg:col-span-7 lg:pl-8 order-1 lg:order-2 fade-up">
        <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ __('Major Event') }}</span>
        <h2 class="font-headline-lg text-primary mt-2 mb-4 sm:mb-6">{{ $featuredTournament?->title ?? __('Powerblink Independence Day Tournament') }}</h2>
        <p class="font-body-lg text-on-surface-variant mb-6 sm:mb-8 leading-relaxed">{{ $featuredTournament?->description ?? __('Our flagship annual event brings together the finest youth talents from across the region — a celebration of football culture and a stage for players to showcase growth in front of scouts and the community.') }}</p>
        <a href="{{ route('tournaments') }}" class="inline-flex items-center min-h-[44px] bg-primary text-on-primary px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-headline-md hover:shadow-xl transition-shadow">
          {{ __('Explore Tournaments') }}
        </a>
      </div>
    </div>
  </section>

  {{-- Final CTA --}}
  <section class="py-section-gap px-margin-mobile md:px-margin-desktop">
    <div class="max-w-container-max mx-auto bg-secondary text-on-secondary rounded-[2.5rem] p-10 md:p-24 text-center relative overflow-hidden fade-up">
      <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
        <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
          <path d="M0 100 C 20 0 50 0 100 100" fill="none" stroke="white" stroke-width="0.5"></path>
          <path d="M0 80 C 30 20 60 20 100 80" fill="none" stroke="white" stroke-width="0.5"></path>
        </svg>
      </div>
      <div class="relative z-10">
        <h2 class="font-headline-lg text-on-secondary mb-6">{{ $s['promo_title'] ?? __('Ready To Begin Your Football Journey?') }}</h2>
        <p class="font-body-lg text-on-secondary/80 max-w-2xl mx-auto mb-10">{{ $s['welcome_body'] ?? __('Join the elite ranks of PowerBlink FC and take your first step toward professional excellence today.') }}</p>
        <a href="{{ route('registration.wizard') }}" class="inline-flex items-center bg-white text-secondary px-10 py-5 rounded-full font-headline-md text-lg hover:scale-105 active:scale-95 transition-all shadow-2xl">
          {{ $s['promo_cta'] ?? __('Register Today') }}
        </a>
      </div>
    </div>
  </section>
@endsection

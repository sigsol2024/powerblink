@extends('layouts.site')

@section('content')
  @php
    $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/home-powerblink-fc-054.jpg');
  @endphp

  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImage,
    'eyebrow' => __('Competition'),
    'title' => $page->title,
    'subtitle' => $page->meta_description ?? __('Regional youth showcases and academy tournament fixtures.'),
    'primaryCta' => ['href' => route('registration.wizard'), 'label' => __('Register for Trials')],
  ])

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    @if ($tournaments->isEmpty())
      <div class="rounded-2xl bg-surface-container-lowest border border-outline-variant/30 p-8 text-center">
        <p class="text-on-surface-variant">{{ __('No tournaments scheduled yet.') }}</p>
      </div>
    @else
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter mb-10">
        <div class="lg:col-span-5 order-2 lg:order-1">
          <div class="grid grid-cols-2 gap-3 sm:gap-4">
            @foreach (['home-powerblink-fc-046.jpg', 'home-powerblink-fc-047.jpg', 'home-powerblink-fc-048.jpg', 'home-powerblink-fc-049.jpg'] as $img)
              <div class="aspect-square rounded-xl overflow-hidden bg-surface-container {{ $loop->iteration % 2 === 0 ? 'mt-6 sm:mt-8' : '' }} {{ $loop->iteration === 3 ? '-mt-6 sm:-mt-8' : '' }}">
                <img src="{{ \App\Support\PlaceholderMedia::url('asset/images/powerblink/'.$img) }}" alt="" class="w-full h-full object-cover" loading="lazy" />
              </div>
            @endforeach
          </div>
        </div>
        <div class="lg:col-span-7 order-1 lg:order-2 flex flex-col justify-center">
          <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ __('Academy Calendar') }}</span>
          <h2 class="font-headline-lg text-primary mt-2 mb-4">{{ __('Tournament Pathway') }}</h2>
          <p class="text-on-surface-variant leading-relaxed">{{ __('Powerblink FC competes in regional youth showcases designed to test technical growth, tactical discipline, and team character under match conditions.') }}</p>
        </div>
      </div>

      <div class="space-y-4 sm:space-y-6">
        @foreach ($tournaments as $tournament)
          <article class="flex flex-col md:flex-row gap-4 sm:gap-6 rounded-2xl bg-surface-container-lowest border border-outline-variant/30 p-4 sm:p-6 card-hover overflow-hidden">
            @if ($tournament->featuredImage)
              <img src="{{ \App\Support\MediaImageUrl::url($tournament->featuredImage->file_path) }}" alt="" class="w-full md:w-52 h-40 sm:h-44 object-cover rounded-xl shrink-0" loading="lazy" />
            @endif
            <div class="flex-1 min-w-0">
              <div class="flex flex-wrap items-center gap-2 mb-2">
                <h3 class="font-headline-md text-primary-container">{{ $tournament->title }}</h3>
                @if ($tournament->status)
                  <span class="text-xs uppercase font-bold px-2 py-1 rounded-full bg-secondary/10 text-secondary">{{ $tournament->status }}</span>
                @endif
              </div>
              <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm text-on-surface-variant">
                @if ($tournament->start_date)
                  <div>
                    <dt class="text-label-caps uppercase text-xs">{{ __('Dates') }}</dt>
                    <dd class="font-medium text-on-surface">{{ $tournament->start_date->format('M j, Y') }}@if($tournament->end_date) – {{ $tournament->end_date->format('M j, Y') }}@endif</dd>
                  </div>
                @endif
                @if ($tournament->location)
                  <div>
                    <dt class="text-label-caps uppercase text-xs">{{ __('Location') }}</dt>
                    <dd class="font-medium text-on-surface">{{ $tournament->location }}</dd>
                  </div>
                @endif
                @if ($tournament->category)
                  <div>
                    <dt class="text-label-caps uppercase text-xs">{{ __('Category') }}</dt>
                    <dd class="font-medium text-on-surface">{{ $tournament->category }}</dd>
                  </div>
                @endif
              </dl>
              @if ($tournament->description)
                <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{{ $tournament->description }}</p>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>
@endsection

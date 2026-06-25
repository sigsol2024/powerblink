@extends('layouts.site')

@php
  $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/programs-powerblink-fc-074.jpg');
@endphp

@section('content')
  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImage,
    'eyebrow' => __('Academy Pathways'),
    'title' => $page->title,
    'subtitle' => $page->meta_description ?? __('Age-group pathways and structured development programs for players aged 7–15.'),
    'primaryCta' => ['href' => route('registration.wizard'), 'label' => __('Apply Now')],
    'secondaryCta' => ['href' => route('contact'), 'label' => __('Ask a Question')],
  ])

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    @if ($programs->isEmpty())
      <p class="text-on-surface-variant text-center fade-up">{{ __('Programs will be published soon.') }}</p>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
        @foreach ($programs as $i => $program)
          <article class="group bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-outline-variant/30 hover:border-secondary transition-all flex flex-col md:flex-row fade-up" @if($i) style="transition-delay: {{ min($i * 0.08, 0.24) }}s" @endif>
            <div class="md:w-2/5 h-52 md:h-auto shrink-0 overflow-hidden bg-surface-container">
              @if ($program->heroImage)
                <img src="{{ \App\Support\MediaImageUrl::url($program->heroImage->file_path) }}" alt="{{ $program->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              @else
                <div class="w-full h-full flex items-center justify-center bg-primary-container">
                  <span class="material-symbols-outlined text-5xl text-on-primary-container">sports_soccer</span>
                </div>
              @endif
            </div>
            <div class="p-6 md:p-8 flex flex-col flex-grow">
              <div class="flex items-start justify-between gap-3 mb-3">
                <h2 class="font-headline-md text-primary">{{ $program->name }}</h2>
                @if ($program->age_group)
                  <span class="bg-secondary/10 text-secondary text-[10px] font-bold uppercase px-3 py-1 rounded-full shrink-0">{{ $program->age_group }}</span>
                @endif
              </div>
              @if ($program->season)
                <p class="text-xs text-on-surface-variant mb-3">{{ $program->season->name }}</p>
              @endif
              @if ($program->description)
                <p class="text-sm text-on-surface-variant mb-6 flex-grow line-clamp-4">{{ $program->description }}</p>
              @endif
              <dl class="grid grid-cols-2 gap-4 text-sm mb-6">
                @if ($program->sessions_per_week)
                  <div>
                    <dt class="text-on-surface-variant text-xs uppercase font-label-caps">{{ __('Sessions/week') }}</dt>
                    <dd class="font-stat-md text-primary">{{ $program->sessions_per_week }}</dd>
                  </div>
                @endif
                @if ($program->registration_fee)
                  <div>
                    <dt class="text-on-surface-variant text-xs uppercase font-label-caps">{{ __('Registration') }}</dt>
                    <dd class="font-semibold text-primary">{{ format_currency($program->registration_fee) }}</dd>
                  </div>
                @endif
              </dl>
              <a href="{{ route('registration.wizard') }}" class="inline-flex items-center justify-center w-full py-3 bg-primary text-on-primary rounded-lg font-bold hover:bg-secondary transition-colors">
                {{ __('Apply Now') }}
              </a>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </section>

  <section class="py-section-gap bg-primary-container">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop text-center fade-up">
      <h2 class="font-headline-lg text-on-primary mb-4">{{ __('Not Sure Which Program Fits?') }}</h2>
      <p class="text-on-primary-container max-w-xl mx-auto mb-8">{{ __('Our coaching staff will help place your player in the right age group and development pathway.') }}</p>
      <a href="{{ route('contact') }}" class="inline-flex items-center bg-secondary text-on-secondary px-8 py-4 rounded-xl font-bold hover:opacity-90 transition-opacity">
        {{ __('Contact the Academy') }}
      </a>
    </div>
  </section>
@endsection

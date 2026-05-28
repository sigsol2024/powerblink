@extends('layouts.site')

@section('content')
  @php
    $sections = $sections ?? [];
    $heading = trim((string) ($sections['heading'] ?? $page?->title ?? ''));
    $body = trim((string) ($sections['body'] ?? ''));
    $html = trim((string) ($page?->content_html ?? ''));
  @endphp

  <section class="py-section-py-mobile md:py-section-py-desktop relative overflow-hidden border-b border-outline-variant">
    <div class="absolute inset-0 luxe-geometric-bg opacity-[0.25] pointer-events-none"></div>
    <div class="max-w-max-container mx-auto px-gutter relative z-10 text-center">
      <span class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase block mb-6">
        {{ request()->routeIs('privacy-policy') ? __('Privacy') : (request()->routeIs('terms') ? __('Terms') : __('Legal')) }}
      </span>
      <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-6">{{ $heading }}</h1>
      @if (!empty($page?->meta_description))
        <p class="max-w-2xl mx-auto font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $page->meta_description }}</p>
      @endif
    </div>
  </section>

  <section class="py-section-py-mobile md:py-section-py-desktop">
    <div class="max-w-max-container mx-auto px-gutter">
      <div class="bg-surface-container-low border border-outline-variant p-7 md:p-10">
        <article class="max-w-3xl mx-auto font-body-md text-body-md text-on-surface-variant leading-relaxed space-y-6 [&_h2]:font-headline-md [&_h2]:text-headline-md [&_h2]:text-primary [&_h2]:mt-10 [&_h3]:font-headline-md [&_h3]:text-primary [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_a]:text-secondary [&_a]:underline">
          @if ($html !== '')
            {!! $html !!}
          @elseif ($body !== '')
            @foreach(preg_split("/\n\s*\n/", $body) ?: [] as $paragraph)
              @if(trim($paragraph) !== '')
                <p>{{ trim($paragraph) }}</p>
              @endif
            @endforeach
          @else
            <p>{{ __('Content coming soon.') }}</p>
          @endif
        </article>

        <div class="mt-10 pt-6 border-t border-outline-variant flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
          <p class="font-body-md text-body-md text-on-surface-variant">
            {{ __('Need help or have a question?') }}
          </p>
          <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 font-label-caps text-label-caps uppercase border-b border-primary pb-1 hover:text-secondary hover:border-secondary transition-all">
            {{ __('Contact us') }}
            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection

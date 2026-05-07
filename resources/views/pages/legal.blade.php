@extends('layouts.site')

@section('content')
  @php
    $sections = $sections ?? [];
    $heading = trim((string) ($sections['heading'] ?? $page?->title ?? ''));
    $body = trim((string) ($sections['body'] ?? ''));
    $html = trim((string) ($page?->content_html ?? ''));
  @endphp

  <section class="bg-[#111316] pt-28 md:pt-32">
    <div class="mx-auto max-w-5xl px-6 pb-12 text-center">
      <h1 class="font-headline text-3xl font-black tracking-tight text-white md:text-5xl">{{ $heading }}</h1>
      @if (!empty($page?->meta_description))
        <p class="mx-auto mt-5 max-w-2xl text-sm leading-relaxed text-white/70 md:text-base">{{ $page->meta_description }}</p>
      @endif
    </div>
  </section>

  <section class="bg-white py-14 md:py-20">
    <div class="mx-auto max-w-5xl px-6">
      <article class="prose prose-zinc max-w-none">
        @if ($html !== '')
          {!! $html !!}
        @elseif ($body !== '')
          {!! nl2br(e($body)) !!}
        @else
          <p>{{ __('Content coming soon.') }}</p>
        @endif
      </article>
    </div>
  </section>
@endsection


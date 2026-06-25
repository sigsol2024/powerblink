@extends('layouts.site')

@php
  $heroImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/gallery-powerblink-fc-037.jpg');
  $categories = $items->pluck('category')->filter()->unique()->values();
@endphp

@section('content')
  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImage,
    'minHeight' => 'min-h-[50vh]',
    'eyebrow' => __('Academy Life'),
    'title' => $page->title,
    'subtitle' => $page->meta_description ?? __('Match highlights, training sessions, and community moments from Powerblink FC.'),
  ])

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto" x-data="{ filter: 'all' }">
    @if ($categories->isNotEmpty())
      <div class="flex flex-wrap gap-3 justify-center mb-10 fade-up">
        <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors">{{ __('All') }}</button>
        @foreach ($categories as $category)
          <button type="button" @click="filter = '{{ $category }}'" :class="filter === '{{ $category }}' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant'" class="px-5 py-2 rounded-full text-sm font-bold transition-colors">{{ $category }}</button>
        @endforeach
      </div>
    @endif

    @if ($items->isEmpty())
      <p class="text-on-surface-variant text-center fade-up">{{ __('Gallery items will appear here soon.') }}</p>
    @else
      <div class="masonry-grid fade-up">
        @foreach ($items as $item)
          <figure
            class="group relative rounded-xl overflow-hidden bg-surface-container shadow-sm"
            x-show="filter === 'all' || filter === '{{ $item->category ?? '' }}'"
            x-cloak
          >
            @if ($item->media)
              <img src="{{ \App\Support\MediaImageUrl::url($item->media->file_path) }}" alt="{{ $item->title }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-500" />
            @endif
            @if ($item->title || $item->category)
              <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-primary-container/95 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                @if ($item->title)
                  <span class="text-on-primary text-sm font-semibold block">{{ $item->title }}</span>
                @endif
                @if ($item->category)
                  <span class="text-on-primary-container text-xs">{{ $item->category }}</span>
                @endif
              </figcaption>
            @endif
          </figure>
        @endforeach
      </div>
    @endif
  </section>
@endsection

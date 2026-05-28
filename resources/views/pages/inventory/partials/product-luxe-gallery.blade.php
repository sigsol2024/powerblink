@php
  $galleryUrls = $galleryUrls ?? collect();
  $galleryItems = $galleryItems ?? [];
  $youtubeId = $youtubeId ?? null;
  $videoUrl = trim((string) ($vehicle->video_url ?? ''));
@endphp
@if ($galleryUrls->isNotEmpty())
  <div
    class="outline-none"
    data-vehicle-detail-gallery
    data-gallery-version="v2"
    data-gallery-urls="{{ e($galleryUrls->values()->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) }}"
    data-gallery-items="{{ e(collect($galleryItems)->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) }}"
    tabindex="0"
    aria-label="{{ __('Image gallery') }}"
  >
    <div class="aspect-[4/5] overflow-hidden bg-surface-container relative group luxe-image-zoom cursor-crosshair mb-4" data-vehicle-detail-viewport>
      @if ($vehicle->is_special)
        <span class="absolute top-4 left-4 z-10 bg-surface-container-lowest px-3 py-1 font-label-caps text-[10px] tracking-widest uppercase">{{ __('Limited') }}</span>
      @endif
      <img
        src="{{ $galleryUrls->first() }}"
        alt="{{ $vehicle->title }}"
        class="w-full h-full object-cover transition-transform duration-700 ease-out"
        data-vehicle-detail-main
        data-vehicle-detail-main-img
        decoding="async"
        fetchpriority="high"
        draggable="false"
      />
      <div class="absolute inset-0 hidden" data-vehicle-detail-main-video>
        <button type="button" class="absolute inset-0 z-[2] flex items-center justify-center" data-vehicle-detail-video-start aria-label="{{ __('Play video') }}">
          @if ($youtubeId)
            <img src="https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg" alt="" class="absolute inset-0 h-full w-full object-cover opacity-90" loading="lazy" />
          @endif
          <span class="absolute inset-0 bg-black/35"></span>
          <span class="relative flex h-20 w-20 items-center justify-center rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/20">
            <span class="text-white inline-flex"><x-icon name="play" class="w-10 h-10" /></span>
          </span>
        </button>
        <div class="absolute inset-0 z-[3] hidden items-center justify-center bg-black/60" data-vehicle-detail-video-loading>
          <div class="h-10 w-10 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-4 gap-3 md:gap-4" data-vehicle-detail-thumbs-scroll>
      @foreach ($galleryUrls as $index => $url)
        <button
          type="button"
          class="aspect-square bg-surface-container overflow-hidden border transition-all vehicle-detail-thumb-btn {{ $index === 0 ? 'is-active border-primary opacity-100' : 'border-transparent opacity-60 hover:opacity-100' }}"
          data-vehicle-detail-thumb
          data-index="{{ $index }}"
          data-full="{{ $url }}"
        >
          <img src="{{ $url }}" alt="" class="w-full h-full object-cover" draggable="false" loading="lazy" decoding="async" />
        </button>
      @endforeach
      @if ($videoUrl !== '')
        @php $videoIndex = (int) $galleryUrls->count(); @endphp
        <button
          type="button"
          class="aspect-square bg-surface-container overflow-hidden border border-transparent opacity-60 hover:opacity-100 transition-all relative"
          data-vehicle-detail-thumb
          data-index="{{ $videoIndex }}"
          data-full=""
          aria-label="{{ __('Video') }}"
        >
          @if ($youtubeId)
            <img src="https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg" alt="" class="w-full h-full object-cover" loading="lazy" />
          @else
            <span class="absolute inset-0 flex items-center justify-center font-label-caps text-[10px] text-on-surface-variant">{{ __('Video') }}</span>
          @endif
          <span class="absolute inset-0 flex items-center justify-center">
            <span class="text-on-primary bg-primary/80 rounded-full p-1 inline-flex"><x-icon name="play" class="w-3.5 h-3.5" /></span>
          </span>
        </button>
      @endif
    </div>
  </div>
@endif

@extends('layouts.site')

@push('head')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
@php
  $images = $vehicle->images ?? collect();
  $cover = $images->first();
  $galleryUrls = $images->map(fn ($img) => \App\Support\VehicleImageUrl::url($img->path))->values();
  $youtubeId = null;
  $videoUrl = trim((string) ($vehicle->video_url ?? ''));
  if ($videoUrl !== '') {
      $try = $videoUrl;
      if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $try) === 1) {
          $youtubeId = $try;
      } else {
          $parts = parse_url($try) ?: [];
          $host = strtolower((string) ($parts['host'] ?? ''));
          $path = (string) ($parts['path'] ?? '');
          parse_str((string) ($parts['query'] ?? ''), $qs);
          if (str_contains($host, 'youtu.be')) {
              $cand = trim($path, '/');
              if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $cand) === 1) $youtubeId = $cand;
          } elseif (str_contains($host, 'youtube.com')) {
              if (!empty($qs['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', (string) $qs['v']) === 1) {
                  $youtubeId = (string) $qs['v'];
              } elseif (preg_match('#/embed/([a-zA-Z0-9_-]{11})#', $path, $m) === 1) {
                  $youtubeId = (string) ($m[1] ?? '');
              }
          }
      }
  }
  $galleryItems = $galleryUrls->map(fn ($u) => ['type' => 'image', 'src' => $u])->values()->all();
  if ($youtubeId) {
      $galleryItems[] = [
          'type' => 'video',
          'provider' => 'youtube',
          'embedUrl' => 'https://www.youtube.com/embed/'.$youtubeId.'?autoplay=1&rel=0',
          'thumbUrl' => 'https://img.youtube.com/vi/'.$youtubeId.'/hqdefault.jpg',
          'externalUrl' => 'https://www.youtube.com/watch?v='.$youtubeId,
      ];
  } elseif ($videoUrl !== '') {
      $galleryItems[] = [
          'type' => 'video',
          'provider' => 'external',
          'embedUrl' => null,
          'thumbUrl' => null,
          'externalUrl' => $videoUrl,
      ];
  }
  $price = $vehicle->price;
  $overview = $vehicle->overview ?: $vehicle->description;
  $techSpecs = is_array($vehicle->tech_specs) ? $vehicle->tech_specs : [];
  $siteContact = $siteContact ?? ['address' => '', 'phone' => '', 'email' => ''];
@endphp

<div class="vehicle-detail-page bg-black text-white font-['Open_Sans']">
  <main class="max-w-7xl mx-auto px-6 py-10">
    <section class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6 border-b border-white/10 pb-8">
      <div>
        <div class="text-[#ffb129] text-sm font-bold uppercase tracking-widest mb-1">{{ $vehicle->bodyTypeOption?->value ?: 'Vehicle' }}</div>
        <h1 class="text-4xl md:text-5xl font-black uppercase font-['Montserrat']">{{ $vehicle->year ?: '' }}</h1>
      </div>
      <div class="flex flex-wrap md:flex-nowrap items-stretch shadow-xl w-full md:w-auto">
        <div class="bg-[#3b5998] p-4 flex flex-col justify-center min-w-[140px] text-center border-r border-white/10">
          <div class="text-[10px] uppercase font-bold opacity-70">Buy for</div>
          <div class="text-2xl font-black">@if(!is_null($price)){{ format_currency($price) }}@else ASK @endif</div>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-10">
      <div class="lg:col-span-2">
        <div class="relative">
          <div class="absolute top-4 left-0 flex flex-col gap-2 z-10">
            @if (!empty($vehicle->video_url))
              <span class="bg-white/20 backdrop-blur-md text-[10px] font-bold px-3 py-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">videocam</span> 1 VIDEO</span>
            @endif
            @if ($vehicle->is_special)
              <span class="bg-[#ffb129] text-[#191c1e] text-[10px] font-black px-6 py-1 italic uppercase tracking-tighter w-fit transform -skew-x-12 -ml-2">SPECIAL</span>
            @endif
          </div>
          <div class="absolute top-4 right-4 flex gap-2 z-10">
            <button class="bg-black/50 p-2 rounded-sm" type="button" onclick="window.print()"><span class="material-symbols-outlined text-sm">print</span></button>
            <form method="post" action="{{ route('compare.add', ['vehicle' => $vehicle->id]) }}">
              @csrf
              <button class="bg-black/50 p-2 rounded-sm" type="submit" title="{{ __('Add to compare') }}"><span class="material-symbols-outlined text-sm">compare_arrows</span></button>
            </form>
            @auth
              <form method="post" action="{{ route('favorites.toggle', ['vehicle' => $vehicle->id]) }}" data-favorite-toggle>
                @csrf
                <button class="bg-black/50 p-2 rounded-sm" type="submit"><span class="material-symbols-outlined text-sm">{{ $isFavorited ? 'favorite' : 'favorite_border' }}</span></button>
              </form>
            @endauth
          </div>
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
              <div class="relative aspect-[16/9] w-full overflow-hidden rounded-sm mb-4 bg-[#111]" data-vehicle-detail-viewport>
                <img
                  src="{{ $galleryUrls->first() }}"
                  alt="{{ $vehicle->title }}"
                  class="h-full w-full object-cover transition-[opacity,transform] duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                  style="opacity:1"
                  data-vehicle-detail-main
                  data-vehicle-detail-main-img
                  draggable="false"
                />
                <div class="absolute inset-0 hidden" data-vehicle-detail-main-video>
                  <button type="button" class="absolute inset-0 z-[2] flex items-center justify-center" data-vehicle-detail-video-start aria-label="{{ __('Play video') }}">
                    <img src="{{ $youtubeId ? ('https://img.youtube.com/vi/'.$youtubeId.'/hqdefault.jpg') : '' }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-90" loading="lazy" />
                    <span class="absolute inset-0 bg-black/35"></span>
                    <span class="relative flex h-20 w-20 items-center justify-center rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/20 transition hover:bg-white/25">
                      <span class="material-symbols-outlined ml-1 text-5xl text-white" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                    </span>
                  </button>
                  <div class="absolute inset-0 z-[3] hidden items-center justify-center bg-black/60" data-vehicle-detail-video-loading>
                    <div class="h-10 w-10 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
                  </div>
                </div>
              </div>
              <div class="flex gap-2 overflow-x-auto pb-2 scroll-smooth" data-vehicle-detail-thumbs-scroll>
                @foreach ($galleryUrls as $index => $url)
                  <button
                    type="button"
                    class="vehicle-detail-thumb-btn shrink-0 w-[calc((100%-2.5rem)/6))] min-w-[4.5rem] max-w-[6.25rem] border-2 {{ $index === 0 ? 'border-[#ffb129] is-active opacity-100' : 'border-transparent opacity-70 hover:opacity-100' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-[#ffb129]"
                    data-vehicle-detail-thumb
                    data-index="{{ $index }}"
                    data-full="{{ $url }}"
                  >
                    <span class="block aspect-video w-full overflow-hidden rounded-sm bg-[#232628]">
                      <img src="{{ $url }}" alt="" class="h-full w-full object-cover" draggable="false" />
                    </span>
                  </button>
                @endforeach
                @if ($videoUrl !== '')
                  @php $videoIndex = (int) $galleryUrls->count(); @endphp
                  <button
                    type="button"
                    class="vehicle-detail-thumb-btn shrink-0 w-[calc((100%-2.5rem)/6))] min-w-[4.5rem] max-w-[6.25rem] border-2 border-transparent opacity-70 hover:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#ffb129]"
                    data-vehicle-detail-thumb
                    data-index="{{ $videoIndex }}"
                    data-full=""
                    aria-label="{{ __('Video') }}"
                  >
                    <span class="block aspect-video w-full overflow-hidden rounded-sm bg-[#232628] relative">
                      @if ($youtubeId)
                        <img src="https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg" alt="" class="h-full w-full object-cover" loading="lazy" draggable="false" />
                      @else
                        <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold uppercase tracking-widest text-white/80">{{ __('Video') }}</span>
                      @endif
                      <span class="absolute inset-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white/90">play_circle</span>
                      </span>
                    </span>
                  </button>
                @endif
              </div>
            </div>
          @endif
        </div>

        <div class="mt-12">
          <h2 class="text-2xl font-black uppercase mb-6 font-['Montserrat']">Vehicle overview</h2>
          <div class="text-gray-400 text-sm leading-relaxed space-y-6">
            <p class="whitespace-pre-line">{{ $overview ?: 'No overview available for this vehicle.' }}</p>
          </div>
        </div>

        <div class="mt-12">
          <h3 class="text-xs font-bold uppercase tracking-widest text-[#ffb129] mb-4">Extra features</h3>
          <h4 class="text-lg font-black uppercase mb-6 font-['Montserrat']">Extra Features</h4>
          <div class="grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse (($vehicle->features ?? []) as $feature)
              <div class="flex min-w-0 max-w-full items-start gap-3 text-xs">
                <span class="material-symbols-outlined shrink-0 text-[#ffb129] text-[16px]">check_circle</span>
                <span class="min-w-0 flex-1 break-words pr-1 text-left leading-relaxed [overflow-wrap:anywhere] [hyphens:auto]">{{ $feature }}</span>
              </div>
            @empty
              <div class="text-xs text-gray-400">No extra features provided.</div>
            @endforelse
          </div>
        </div>
      </div>

      <aside class="space-y-8">
        <div class="bg-[#191c1e] border border-white/5 p-8 rounded-sm">
          <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-6">Dealer info</h3>
          <div class="flex items-center gap-4 mb-6">
            @php
              $sellerPhoto = trim((string) ($sellerProfile['photo_url'] ?? ''));
              $fallbackLogoPath = trim((string) ($sellerProfile['fallback_logo_path'] ?? ''));
              $fallbackLogoUrl = trim((string) ($sellerProfile['fallback_logo_url'] ?? ''));
            @endphp
            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-[#ffb129] bg-[#30353a]">
              @if ($sellerPhoto !== '')
                <img src="{{ \Illuminate\Support\Str::startsWith($sellerPhoto, ['http://', 'https://']) ? $sellerPhoto : \App\Support\VehicleImageUrl::url($sellerPhoto) }}" alt="" class="h-full w-full object-cover" />
              @elseif ($fallbackLogoPath !== '')
                <img src="{{ \App\Support\VehicleImageUrl::url($fallbackLogoPath) }}" alt="" class="h-full w-full object-cover" />
              @elseif ($fallbackLogoUrl !== '')
                <img src="{{ $fallbackLogoUrl }}" alt="" class="h-full w-full object-cover" />
              @else
                <span class="material-symbols-outlined text-[28px] text-white/45" aria-hidden="true">person</span>
              @endif
            </div>
            <div>
              <div class="font-bold text-sm">{{ $sellerProfile['name'] ?? 'Dealer' }}</div>
              <div class="text-[10px] text-gray-500 uppercase font-bold">{{ $vehicle->isStaffListing() ? 'Dealer' : 'Private Seller' }}</div>
            </div>
          </div>
          @php
            $rawPhone = trim((string) ($sellerProfile['phone'] ?? ''));
          @endphp
          <div class="w-full bg-[#1e2124] py-3 px-4 text-sm font-bold">
            <span class="text-white">{{ $rawPhone !== '' ? $rawPhone : '—' }}</span>
          </div>
        </div>

        <div class="bg-[#1e2124] overflow-hidden rounded-sm">
          <table class="w-full text-xs">
            <tbody>
              <tr class="border-b border-white/5"><td class="p-4 text-gray-500 font-bold uppercase">Body</td><td class="p-4 font-bold text-right">{{ $vehicle->bodyTypeOption?->value ?: 'N/A' }}</td></tr>
              <tr class="border-b border-white/5"><td class="p-4 text-gray-500 font-bold uppercase">Mileage</td><td class="p-4 font-bold text-right">{{ $vehicle->mileage ? number_format((int) $vehicle->mileage).'mi' : 'N/A' }}</td></tr>
              <tr class="border-b border-white/5"><td class="p-4 text-gray-500 font-bold uppercase">Transmission</td><td class="p-4 font-bold text-right text-[#ffb129]">{{ $vehicle->transmissionOption?->value ?: 'N/A' }}</td></tr>
              <tr class="border-b border-white/5"><td class="p-4 text-gray-500 font-bold uppercase">Fuel Type</td><td class="p-4 font-bold text-right">{{ $vehicle->fuelTypeOption?->value ?: 'N/A' }}</td></tr>
              <tr class="border-b border-white/5"><td class="p-4 text-gray-500 font-bold uppercase">Engine</td><td class="p-4 font-bold text-right">{{ $vehicle->engine_size ?: 'N/A' }}</td></tr>
              <tr><td class="p-4 text-gray-500 font-bold uppercase">Year</td><td class="p-4 font-bold text-right">{{ $vehicle->year ?: 'N/A' }}</td></tr>
            </tbody>
          </table>
        </div>

        {{-- Financing calculator removed (plan requirement). --}}
      </aside>
    </section>

    <section class="mt-20 space-y-12">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div>
          <h3 class="flex items-center gap-2 text-sm font-black uppercase mb-6"><span class="material-symbols-outlined text-[#ffb129]">settings</span> Engine</h3>
          <div class="space-y-4 text-xs">
            <div class="flex justify-between border-b border-white/5 pb-2"><span class="text-gray-500 uppercase font-bold">Engine volume</span><span class="font-bold">{{ $techSpecs['engine_volume'] ?? $vehicle->engine_size ?: 'N/A' }}</span></div>
            <div class="flex justify-between border-b border-white/5 pb-2"><span class="text-gray-500 uppercase font-bold">Type of drive</span><span class="font-bold">{{ $techSpecs['drive_type'] ?? $vehicle->driveOption?->value ?: 'N/A' }}</span></div>
          </div>
        </div>
        <div>
          <h3 class="flex items-center gap-2 text-sm font-black uppercase mb-6"><span class="material-symbols-outlined text-[#ffb129]">settings_input_component</span> Transmission</h3>
          <div class="space-y-4 text-xs">
            <div class="flex justify-between border-b border-white/5 pb-2"><span class="text-gray-500 uppercase font-bold">Type</span><span class="font-bold">{{ $vehicle->transmissionOption?->value ?: 'N/A' }}</span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="mt-24">
      <h2 class="text-3xl font-black uppercase mb-8 font-['Montserrat']">{{ __('Contact') }}</h2>
      <div class="bg-[#191c1e] p-8 md:p-12 border border-white/5">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
          <div class="space-y-8">
            <h3 class="text-2xl font-black uppercase font-['Montserrat']">Contact Information</h3>
            <p class="text-gray-400 text-sm">{{ __('Use the details below to reach us about this vehicle. Your message is delivered to our team.') }}</p>
            <div class="space-y-4">
              <div class="flex items-center gap-4"><div class="bg-[#ffb129] p-2 rounded-full"><span class="material-symbols-outlined text-[#191c1e] text-lg">location_on</span></div><span class="text-sm font-bold">{{ ($siteContact['address'] ?? '') !== '' ? $siteContact['address'] : 'N/A' }}</span></div>
              <div class="flex items-center gap-4"><div class="bg-[#ffb129] p-2 rounded-full"><span class="material-symbols-outlined text-[#191c1e] text-lg">phone</span></div><span class="text-sm font-bold">{{ ($siteContact['phone'] ?? '') !== '' ? $siteContact['phone'] : 'N/A' }}</span></div>
              <div class="flex items-center gap-4"><div class="bg-[#ffb129] p-2 rounded-full"><span class="material-symbols-outlined text-[#191c1e] text-lg">mail</span></div><span class="text-sm font-bold">{{ ($siteContact['email'] ?? '') !== '' ? $siteContact['email'] : 'N/A' }}</span></div>
            </div>
          </div>
          @if ($vehicle->status === 'approved')
            <div class="space-y-6">
              <h3 class="text-xl font-black uppercase flex items-center gap-2 font-['Montserrat']"><span class="material-symbols-outlined text-[#ffb129]">send</span> {{ __('Message to dealer') }}</h3>
              <form method="post" action="{{ route('inventory.inquiry', ['slug' => $vehicle->slug]) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-1"><label class="text-[10px] font-bold uppercase text-gray-500">Your name*</label><input class="w-full bg-white text-[#191c1e] py-3 px-4 rounded-sm border-none" type="text" name="sender_name" value="{{ old('sender_name', auth()->user()?->name) }}" required></div>
                  <div class="space-y-1"><label class="text-[10px] font-bold uppercase text-gray-500">Your telephone number*</label><input class="w-full bg-white text-[#191c1e] py-3 px-4 rounded-sm border-none" type="text" name="sender_phone" value="{{ old('sender_phone') }}"></div>
                </div>
                <div class="space-y-1"><label class="text-[10px] font-bold uppercase text-gray-500">Email*</label><input class="w-full bg-white text-[#191c1e] py-3 px-4 rounded-sm border-none" type="email" name="sender_email" value="{{ old('sender_email', auth()->user()?->email) }}" required></div>
                <div class="space-y-1"><label class="text-[10px] font-bold uppercase text-gray-500">Your message</label><textarea class="w-full bg-white text-[#191c1e] py-3 px-4 rounded-sm border-none" rows="4" name="message" required>{{ old('message') }}</textarea></div>
                <button class="bg-[#3b5998] px-10 py-3 text-xs font-black uppercase tracking-tighter hover:bg-[#4b71be] transition-colors" type="submit">Submit</button>
              </form>
            </div>
          @endif
        </div>
      </div>
    </section>

    @if ($similarVehicles->isNotEmpty())
      <section class="mt-24 pt-12 border-t border-white/10">
        <div data-simple-carousel data-carousel-type="similar-cars">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-10">
            <a class="text-[10px] font-bold uppercase tracking-widest flex items-center gap-2 hover:text-[#ffb129]" href="{{ route('inventory.index') }}"><span class="material-symbols-outlined text-[14px]">arrow_back</span> Search results</a>
            <div class="flex gap-2 shrink-0">
              <button class="w-10 h-10 border border-white/20 flex items-center justify-center hover:border-[#ffb129]" type="button" data-carousel-prev aria-label="{{ __('Previous') }}"><span class="material-symbols-outlined">chevron_left</span></button>
              <button class="w-10 h-10 border border-white/20 flex items-center justify-center hover:border-[#ffb129]" type="button" data-carousel-next aria-label="{{ __('Next') }}"><span class="material-symbols-outlined">chevron_right</span></button>
            </div>
          </div>
          <div class="overflow-hidden" data-carousel-viewport>
            <div class="flex gap-6 transition-transform duration-300" data-carousel-track>
              @foreach ($similarVehicles as $item)
                @php $itemCover = $item->images->first(); @endphp
                <article class="bg-[#191c1e] group min-w-full md:min-w-[calc(50%-12px)] lg:min-w-[calc(25%-18px)]" data-carousel-slide>
                  <a href="{{ route('inventory.show', ['slug' => $item->slug]) }}">
                    <div class="relative overflow-hidden aspect-[4/3]">
                      @if ($item->is_special)
                        <div class="absolute top-2 left-0 bg-[#3b5998] text-white text-[10px] font-bold px-4 py-1 italic uppercase transform -skew-x-12 -ml-1 z-10">SPECIAL</div>
                      @endif
                      @if ($itemCover)
                        <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ \App\Support\VehicleImageUrl::url($itemCover->path) }}" alt="{{ $item->title }}">
                      @endif
                      <div class="absolute bottom-0 left-0 right-0 bg-[#3b5998]/90 p-2 text-center">
                        <span class="text-[10px] font-bold">Our price @if(!is_null($item->price)){{ format_currency($item->price) }}@else ASK @endif</span>
                      </div>
                    </div>
                    <div class="p-4"><h4 class="text-xs font-bold uppercase">{{ trim(($item->makeOption?->value ?: '').' '.($item->modelOption?->value ?: '').' '.($item->year ?: '')) }}</h4></div>
                  </a>
                </article>
              @endforeach
            </div>
          </div>
        </div>
      </section>
    @endif
  </main>
</div>
@endsection
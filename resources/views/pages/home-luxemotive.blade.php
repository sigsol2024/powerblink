@extends('layouts.site')

@php
  $s = $sections ?? [];
  $heroTitle = $s['hero_title'] ?? 'Lorem ipsum dolor sit amet';
  $heroSubtitle = $s['hero_subtitle'] ?? 'Consectetur adipiscing elit';
  $heroBg = \App\Support\PlaceholderMedia::url($s['hero_image'] ?? 'asset/images/media/home-hero-main.jpg');
  $dealerCtaBg = \App\Support\PlaceholderMedia::url($s['dealer_cta_bg'] ?? 'asset/images/media/home-cta-left.jpg');
  // testimonial + statistics blocks removed (see plan)
  $heroCtaHref = $s['hero_cta_href'] ?? '/inventory';
  $heroCtaUrl = \Illuminate\Support\Str::startsWith($heroCtaHref, ['http://', 'https://']) ? $heroCtaHref : url($heroCtaHref);
  $ctaLeftHref = $s['cta_left_button_href'] ?? '/inventory';
  $ctaLeftUrl = \Illuminate\Support\Str::startsWith($ctaLeftHref, ['http://', 'https://']) ? $ctaLeftHref : url($ctaLeftHref);
  $ctaRightHref = $s['cta_right_button_href'] ?? (auth()->check() ? '/dashboard/vehicles/create' : '/register');
  $ctaRightUrl = \Illuminate\Support\Str::startsWith($ctaRightHref, ['http://', 'https://']) ? $ctaRightHref : url($ctaRightHref);
  $leftCtaIcon = preg_replace('/[^a-z0-9_]/', '', strtolower((string) ($s['dealer_cta_left_icon'] ?? 'directions_car'))) ?: 'directions_car';
  $rightCtaIcon = preg_replace('/[^a-z0-9_]/', '', strtolower((string) ($s['dealer_cta_right_icon'] ?? 'sell'))) ?: 'sell';
  $recentTitleRaw = trim((string) ($s['recent_title'] ?? 'RECENT CARS'));
  $recentTitleParts = preg_split('/\s+/', $recentTitleRaw) ?: ['RECENT', 'CARS'];
  $recentLastWord = array_pop($recentTitleParts) ?: 'CARS';
  $recentFirstWords = trim(implode(' ', $recentTitleParts));
  $welcomeVideoRaw = trim((string) ($s['welcome_video_url'] ?? ''));
  $welcomeYoutubeWatch = null;
  $welcomeYoutubeEmbedId = null;
  if ($welcomeVideoRaw !== '') {
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $welcomeVideoRaw, $m)) {
      $welcomeYoutubeEmbedId = $m[1];
      $welcomeYoutubeWatch = 'https://www.youtube.com/watch?v=' . $m[1];
    } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $welcomeVideoRaw)) {
      $welcomeYoutubeEmbedId = $welcomeVideoRaw;
      $welcomeYoutubeWatch = 'https://www.youtube.com/watch?v=' . $welcomeVideoRaw;
    }
  }
  $prefooterTitle = $s['prefooter_title'] ?? 'Lorem ipsum — questions?';
  $prefooterButtonText = $s['prefooter_button_text'] ?? 'Contact';
  $prefooterButtonHref = trim((string) ($s['prefooter_button_href'] ?? '/contact'));
  $prefooterButtonUrl = \Illuminate\Support\Str::startsWith($prefooterButtonHref, ['http://', 'https://']) ? $prefooterButtonHref : url($prefooterButtonHref);
@endphp

@section('content')
  {{-- Homepage does not render legacy WordPress/Elementor HTML here. Use Admin → Page Editors → Home for section copy and optional Content HTML on other pages. --}}

  <section class="relative flex min-h-[94vh] items-start overflow-hidden pt-32 md:min-h-[100vh] md:pt-40">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ e($heroBg) }}');"></div>
    <div class="absolute inset-0 hero-gradient"></div>
    <div class="relative z-10 container mx-auto px-8 py-[65px] text-center">
      <h1 class="text-white font-headline font-black text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-tight tracking-tight">{{ $heroTitle }}</h1>
      <p class="text-primary font-bold tracking-widest mt-6 text-lg sm:text-xl md:text-2xl">{{ $heroSubtitle }}</p>
      @if (! empty($s['hero_description']))
        <p class="mx-auto mt-4 max-w-3xl text-sm font-medium leading-relaxed text-white/90 sm:text-[15px]">
          {{ $s['hero_description'] }}
        </p>
      @endif
      <a href="{{ $heroCtaUrl }}" class="mt-10 inline-block bg-primary text-on_surface px-10 py-4 font-bold text-xs tracking-widest uppercase rounded shadow-lg hover:bg-yellow-400 transition-colors">
        {{ $s['hero_cta_text'] ?? 'Lorem CTA' }}
      </a>
    </div>
  </section>

  <section class="container mx-auto max-w-5xl px-4 sm:px-6 md:px-8 -mt-16 relative z-20">
    <div class="rounded-lg bg-[#232628] p-6 shadow-2xl ring-1 ring-black/20 md:p-8">
      @php
        $homeMatrix = collect($filterOptions['model_matrix'] ?? []);
        $homeConditions = collect($filterOptions['conditions'] ?? []);
        $homeMakes = collect($filterOptions['makes'] ?? []);
      @endphp
      <form id="home-inventory-search" method="get" action="{{ route('inventory.index') }}" class="space-y-4" data-initial-model="{{ (int) ($filters['model_listing_option_id'] ?? 0) }}">
        <div class="flex items-center gap-2.5 text-white">
          <span class="material-symbols-outlined text-[28px] text-primary">search_insights</span>
          <span class="font-headline text-[20px] font-black uppercase tracking-tight">{{ $s['home_search_label'] ?? 'Search inventory' }}</span>
        </div>
        <div class="flex flex-col gap-4 md:flex-row md:items-center">
          <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-3">
            <select name="condition_listing_option_id" class="appearance-none rounded border-none bg-white px-4 py-3 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-primary">
              <option value="">Condition</option>
              @foreach ($homeConditions as $row)
                <option value="{{ $row->id }}" @selected((int) ($filters['condition_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>
              @endforeach
            </select>
            <select id="home-search-make" name="make_listing_option_id" class="appearance-none rounded border-none bg-white px-4 py-3 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-primary">
              <option value="">Make</option>
              @foreach ($homeMakes as $row)
                <option value="{{ $row->id }}" @selected((int) ($filters['make_listing_option_id'] ?? 0) === (int) $row->id)>{{ $row->value }}</option>
              @endforeach
            </select>
            <select id="home-search-model" name="model_listing_option_id" class="appearance-none rounded border-none bg-white px-4 py-3 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-primary">
              <option value="">Model</option>
            </select>
          </div>
          <div class="flex gap-2 w-full md:w-auto">
            <button class="w-full rounded bg-primary px-8 py-3 text-sm font-bold uppercase tracking-widest text-on_surface transition-colors hover:bg-yellow-400 md:w-auto flex items-center justify-center" type="submit">
              <span class="material-symbols-outlined mr-2 text-xl">search</span> Search
            </button>
            <a href="{{ route('inventory.index') }}" class="bg-[#3a3f43] text-white px-4 py-3 rounded hover:bg-slate-700 transition-colors">
              <span class="material-symbols-outlined text-xl">restart_alt</span>
            </a>
          </div>
        </div>
      </form>
      @if ($homeMatrix->isNotEmpty())
        <script type="application/json" id="homeModelMatrixJson">@json($homeMatrix->values()->all())</script>
        <script>
          (() => {
            const matrixSource = document.getElementById('homeModelMatrixJson');
            const matrix = matrixSource ? JSON.parse(matrixSource.textContent || '[]') : [];
            const makeEl = document.getElementById('home-search-make');
            const modelEl = document.getElementById('home-search-model');
            const form = document.getElementById('home-inventory-search');
            if (!makeEl || !modelEl || !form) return;
            const initialModel = parseInt(form.getAttribute('data-initial-model') || '0', 10) || 0;
            function rebuild() {
              const mk = parseInt(makeEl.value || '0', 10) || 0;
              modelEl.innerHTML = '<option value=\"\">Model</option>';
              if (!mk) return;
              matrix.forEach((r) => {
                if (!r || !r.model_id) return;
                if ((parseInt(r.make_id, 10) || 0) !== mk) return;
                const o = document.createElement('option');
                o.value = String(r.model_id);
                o.textContent = r.model || '';
                if ((parseInt(r.model_id, 10) || 0) === initialModel) o.selected = true;
                modelEl.appendChild(o);
              });
            }
            makeEl.addEventListener('change', rebuild);
            rebuild();
          })();
        </script>
      @endif
    </div>
  </section>

  <section class="bg-[#f4f5f7] py-16 md:py-20">
    <div class="container mx-auto max-w-[1240px] px-6 md:px-8">
      <div class="mb-10 md:mb-12 text-center">
        <h2 class="section-line font-headline font-black text-4xl tracking-tight text-on_surface uppercase">
          @if($recentFirstWords !== '')
            <span class="text-on_surface">{{ $recentFirstWords }}</span>
          @endif
          <span class="text-primary">{{ $recentLastWord }}</span>
        </h2>
        <p class="mt-6 max-w-xl mx-auto text-sm md:text-base text-slate-500">{{ $s['recent_subtitle'] ?? 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.' }}</p>
      </div>
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($recentVehicles as $vehicle)
          <a href="{{ route('inventory.show', ['slug' => $vehicle->slug]) }}" class="group block overflow-hidden rounded-sm border border-slate-500/50 bg-[#232628] shadow-md transition hover:shadow-xl">
            <div class="relative aspect-[16/9] overflow-hidden">
              @include('partials.vehicle-hover-gallery', [
                'vehicle' => $vehicle,
                'fallback' => \App\Support\PlaceholderMedia::url('asset/images/media/home-recent-fallback.jpg'),
                'imgClass' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105',
              ])
              @if ($vehicle->is_special)
                <div class="pointer-events-none absolute -right-8 top-3 rotate-45 bg-[#3b63d6] px-10 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-md">{{ __('Special') }}</div>
              @endif
            </div>
            <div class="border-t-2 border-[#3b63d6]/90 bg-[#31363c] px-4 pb-3.5 pt-3">
              <div class="flex items-start justify-between gap-2">
                <h3 class="line-clamp-1 min-w-0 flex-1 pr-1 font-headline text-[18px] md:text-[20px] font-black leading-tight text-white uppercase tracking-tight">{{ $vehicle->title }}</h3>
                <div class="shrink-0 rounded-sm bg-[#3b63d6] px-2 py-1 text-right text-white shadow-sm">
                  <div class="text-[8px] font-bold uppercase leading-none tracking-wide opacity-85">Buy online</div>
                  <div class="mt-0.5 text-[18px] font-black leading-none"><span data-currency-amount="{{ (float) $vehicle->price }}" data-currency-decimals="0">${{ number_format((float) $vehicle->price, 0, '.', ',') }}</span></div>
                </div>
              </div>
              <div class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-slate-500/40 pt-2 text-[10px] md:text-[11px] font-semibold text-slate-300/95">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">speed</span> {{ number_format((int) ($vehicle->mileage ?? 0)) }} mi</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">settings_input_component</span> {{ strtoupper((string) ($vehicle->transmissionOption?->value ?: 'AUTO')) }}</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $vehicle->year ?? '—' }}</span>
              </div>
            </div>
          </a>
        @empty
          <p class="col-span-full text-center text-slate-500">Lorem ipsum: no listings yet. Seed or approve inventory to populate this grid.</p>
        @endforelse
      </div>
    </div>
  </section>

  <section
    class="relative bg-cover bg-center py-14 md:py-[4.5rem]"
    style="background-image: linear-gradient(rgba(255,255,255,0.12), rgba(255,255,255,0.12)), url('{{ e($dealerCtaBg) }}');"
  >
    <div class="mx-auto grid max-w-[1020px] grid-cols-1 gap-[18px] px-5 md:grid-cols-2">
      <div class="flex min-h-[260px] flex-col justify-start bg-white px-8 py-9 text-left shadow-[0_10px_25px_rgba(0,0,0,0.08)] md:min-h-[305px] md:px-10 md:py-10">
        <span class="material-symbols-outlined mb-7 text-5xl text-[#222]">{{ $leftCtaIcon }}</span>
        <h3 class="font-headline text-xl font-extrabold uppercase leading-snug tracking-tight text-[#101010] md:text-[22px]">{{ $s['cta_left_title'] ?? 'Looking for a car?' }}</h3>
        <p class="mt-4 max-w-[26rem] text-[15px] leading-[1.8] text-[#5c6670]">{{ $s['cta_left_body'] ?? '' }}</p>
        <a href="{{ $ctaLeftUrl }}" class="mt-8 inline-flex self-start items-center justify-center bg-[#4b6ff7] px-8 py-3.5 text-[13px] font-bold uppercase tracking-wide text-white transition-colors hover:bg-[#3457e7]">{{ $s['cta_left_button_text'] ?? 'Inventory' }}</a>
      </div>
      <div class="flex min-h-[260px] flex-col justify-start bg-[#efb12c] px-8 py-9 text-left shadow-[0_10px_25px_rgba(0,0,0,0.08)] md:min-h-[305px] md:px-10 md:py-10">
        <span class="material-symbols-outlined mb-7 text-5xl text-[#222]">{{ $rightCtaIcon }}</span>
        <h3 class="font-headline text-xl font-extrabold uppercase leading-snug tracking-tight text-[#101010] md:text-[22px]">{{ $s['cta_right_title'] ?? 'Want to sell a car?' }}</h3>
        <p class="mt-4 max-w-[26rem] text-[15px] leading-[1.8] text-[#fff4d6]">{{ $s['cta_right_body'] ?? '' }}</p>
        <a href="{{ $ctaRightUrl }}" class="mt-8 inline-flex self-start items-center justify-center bg-[#4b6ff7] px-8 py-3.5 text-[13px] font-bold uppercase tracking-wide text-white transition-colors hover:bg-[#3457e7]">{{ $s['cta_right_button_text'] ?? 'Sell your car' }}</a>
      </div>
    </div>
  </section>

  <section class="py-24 bg-surface-container-low border-b border-slate-200">
    <div class="container mx-auto px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="bg-white p-10 rounded shadow-sm border border-slate-100 flex flex-col items-center text-center">
        <div class="bg-slate-50 p-4 rounded-full mb-6"><span class="material-symbols-outlined text-2xl text-slate-900">stars</span></div>
        <h4 class="font-headline font-bold text-lg mb-4 uppercase tracking-tight">{{ $s['feat1_title'] ?? 'Lorem ipsum' }}</h4>
        <p class="text-slate-500 text-sm leading-relaxed">{{ $s['feat1_body'] ?? 'Dolor sit amet, consectetur adipiscing elit.' }}</p>
      </div>
      <div class="bg-white p-10 rounded shadow-sm border border-slate-100 flex flex-col items-center text-center">
        <div class="bg-slate-50 p-4 rounded-full mb-6"><span class="material-symbols-outlined text-2xl text-slate-900">groups</span></div>
        <h4 class="font-headline font-bold text-lg mb-4 uppercase tracking-tight">{{ $s['feat2_title'] ?? 'Dolor sit amet' }}</h4>
        <p class="text-slate-500 text-sm leading-relaxed">{{ $s['feat2_body'] ?? 'Sed cursus ante dapibus diam. Sed nisi.' }}</p>
      </div>
      <div class="bg-white p-10 rounded shadow-sm border border-slate-100 flex flex-col items-center text-center">
        <div class="bg-slate-50 p-4 rounded-full mb-6"><span class="material-symbols-outlined text-2xl text-slate-900">build</span></div>
        <h4 class="font-headline font-bold text-lg mb-4 uppercase tracking-tight">{{ $s['feat3_title'] ?? 'Consectetur elit' }}</h4>
        <p class="text-slate-500 text-sm leading-relaxed">{{ $s['feat3_body'] ?? 'Fusce nec tellus sed augue semper porta.' }}</p>
      </div>
    </div>
  </section>

  {{-- Testimonials + statistics sections removed (plan requirement). --}}

  <section class="bg-white py-32">
    <div class="container mx-auto flex flex-col items-center px-8 text-center">
      <h2 class="mb-8 font-headline text-4xl font-black uppercase tracking-tight">{{ $s['welcome_title'] ?? 'Lorem ipsum welcome block' }}</h2>
      <p class="mb-12 max-w-3xl font-body text-lg leading-relaxed text-slate-500">{{ $s['welcome_body'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sagittis ipsum. Praesent mauris.' }}</p>
      @if ($welcomeYoutubeEmbedId)
        <button type="button" id="homeWelcomeVideoOpen" class="flex h-20 w-20 items-center justify-center rounded-full bg-[#4a69e2] shadow-lg transition-transform hover:scale-110" aria-label="{{ __('Watch video') }}" aria-haspopup="dialog">
          <span class="material-symbols-outlined ml-1 text-4xl text-white" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
        </button>
        <div id="homeWelcomeVideoModal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/75 p-4" role="dialog" aria-modal="true" aria-labelledby="homeWelcomeVideoTitle">
          <div class="relative w-full max-w-4xl rounded-xl bg-black p-2 shadow-2xl ring-1 ring-white/10 sm:p-4">
            <button type="button" id="homeWelcomeVideoClose" class="absolute -right-1 -top-1 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-zinc-900 shadow hover:bg-white sm:right-2 sm:top-2" aria-label="{{ __('Close') }}">&times;</button>
            <p id="homeWelcomeVideoTitle" class="sr-only">{{ __('Video') }}</p>
            <div class="relative aspect-video w-full overflow-hidden rounded-lg bg-black">
              <button type="button" id="homeWelcomeVideoStart" class="absolute inset-0 z-[2] flex items-center justify-center" aria-label="{{ __('Play video') }}">
                <img src="https://img.youtube.com/vi/{{ $welcomeYoutubeEmbedId }}/hqdefault.jpg" alt="" class="absolute inset-0 h-full w-full object-cover opacity-90" loading="lazy" />
                <span class="absolute inset-0 bg-black/35"></span>
                <span class="relative flex h-20 w-20 items-center justify-center rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/20 transition hover:bg-white/25">
                  <span class="material-symbols-outlined ml-1 text-5xl text-white" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                </span>
              </button>
              <div id="homeWelcomeVideoLoading" class="absolute inset-0 z-[3] hidden items-center justify-center bg-black/60">
                <div class="h-10 w-10 animate-spin rounded-full border-2 border-white/30 border-t-white"></div>
              </div>
              <iframe id="homeWelcomeVideoFrame" title="{{ __('Welcome video') }}" class="relative z-[1] h-full w-full" width="560" height="315" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
            </div>
          </div>
        </div>
      @else
        <div class="flex h-20 w-20 cursor-not-allowed items-center justify-center rounded-full bg-slate-300 shadow-lg opacity-60" title="{{ __('Add a YouTube URL in Admin → Pages → Home') }}">
          <span class="material-symbols-outlined ml-1 text-4xl text-white" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
        </div>
      @endif
    </div>
  </section>

  <section class="bg-primary py-8 md:py-10">
    <div class="container mx-auto flex flex-col items-stretch justify-between gap-5 px-4 sm:px-6 md:flex-row md:items-center md:gap-6 md:px-8">
      <div class="flex items-center gap-3 text-on_surface md:gap-4">
        <span class="material-symbols-outlined text-3xl">help</span>
        <h3 class="font-headline text-lg font-bold tracking-tight uppercase md:text-xl">{{ $prefooterTitle }}</h3>
      </div>
      <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between md:w-auto md:gap-6">
        <div class="flex items-center gap-2.5"><span class="material-symbols-outlined text-slate-800">call</span><p class="font-headline text-xl font-black text-slate-900 sm:text-2xl">{{ $dealerPhone ?? '' }}</p></div>
        <a href="{{ $prefooterButtonUrl }}" class="inline-flex items-center justify-center rounded border border-slate-900/10 bg-white/20 px-6 py-3 text-xs font-bold uppercase tracking-widest text-slate-900 transition-all hover:bg-white/40 sm:px-8"><span class="material-symbols-outlined mr-1 text-sm align-middle">mail</span> {{ $prefooterButtonText }}</a>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @if (! empty($welcomeYoutubeEmbedId))
    <script>
      (function () {
        var openBtn = document.getElementById('homeWelcomeVideoOpen');
        var modal = document.getElementById('homeWelcomeVideoModal');
        var closeBtn = document.getElementById('homeWelcomeVideoClose');
        var frame = document.getElementById('homeWelcomeVideoFrame');
        var startBtn = document.getElementById('homeWelcomeVideoStart');
        var loading = document.getElementById('homeWelcomeVideoLoading');
        if (!openBtn || !modal || !closeBtn || !frame || !startBtn) return;
        var embedBase = 'https://www.youtube.com/embed/{{ $welcomeYoutubeEmbedId }}?autoplay=1&rel=0';
        var playing = false;
        function openModal() {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          document.body.style.overflow = 'hidden';
        }
        function startPlayback() {
          if (playing) return;
          playing = true;
          if (loading) {
            loading.classList.remove('hidden');
            loading.classList.add('flex');
          }
          startBtn.classList.add('hidden');
          frame.src = embedBase;
        }
        function closeModal() {
          playing = false;
          frame.src = '';
          startBtn.classList.remove('hidden');
          if (loading) {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
          }
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          document.body.style.overflow = '';
        }
        frame.addEventListener('load', function () {
          if (loading) {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
          }
        });
        openBtn.addEventListener('click', openModal);
        startBtn.addEventListener('click', startPlayback);
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) {
          if (e.target === modal) closeModal();
        });
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
      })();
    </script>
  @endif
@endpush

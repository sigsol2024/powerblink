@php
  $site = $site ?? [];
  $splitHours = static fn (string $raw): array => preg_split('/\r\n|\r|\n/', $raw) ?: [];
  $copyrightName = ! empty(trim((string) ($site['copyright_line'] ?? ''))) ? trim((string) $site['copyright_line']) : config('app.name', 'REV AUTO GROUP');
  $footerTagline = trim((string) ($site['footer_tagline'] ?? ''));
  $socialFacebook = trim((string) ($site['social_facebook'] ?? ''));
  $socialInstagram = trim((string) ($site['social_instagram'] ?? ''));
  $socialLinkedin = trim((string) ($site['social_linkedin'] ?? ''));
  $socialYoutube = trim((string) ($site['social_youtube'] ?? ''));
  $salesHoursLines = $splitHours((string) ($site['dealer_sales_hours'] ?? ''));
  $serviceHoursLines = $splitHours((string) ($site['dealer_service_hours'] ?? ''));
  $partsHoursLines = $splitHours((string) ($site['dealer_parts_hours'] ?? ''));

  // Footer “Top makes” column (public inventory only: approved listings).
  $topMakes = collect();
  try {
      $makeCatId = \App\Models\ListingOptionCategory::query()->where('slug', 'make')->value('id');
      if ($makeCatId) {
          $rows = \App\Models\Vehicle::query()
              ->where('status', 'approved')
              ->whereNotNull('make_listing_option_id')
              ->selectRaw('make_listing_option_id, COUNT(*) as listing_count')
              ->groupBy('make_listing_option_id')
              ->orderByDesc('listing_count')
              ->limit(5)
              ->get();

          $ids = $rows->pluck('make_listing_option_id')->filter()->values()->all();
          $optById = \App\Models\ListingOption::query()
              ->where('category_id', $makeCatId)
              ->whereIn('id', $ids)
              ->get(['id', 'value'])
              ->keyBy('id');

          $topMakes = $rows->map(function ($r) use ($optById) {
              $opt = $optById->get((int) $r->make_listing_option_id);
              if (! $opt) return null;
              return (object) [
                  'id' => (int) $opt->id,
                  'label' => (string) $opt->value,
                  'count' => (int) $r->listing_count,
              ];
          })->filter()->values();
      }
  } catch (\Throwable) {
      $topMakes = collect();
  }

  $aboutGalleryStr = \App\Models\PageSection::query()->where('page', 'about')->where('section_key', 'gallery')->value('content') ?? '[]';
  $aboutGallery = json_decode($aboutGalleryStr, true) ?? [];
  $footerGallery = array_slice($aboutGallery, 0, 4);
  $fallbacks = ['asset/images/media/footer-1.jpg', 'asset/images/media/footer-2.jpg', 'asset/images/media/footer-3.jpg', 'asset/images/media/footer-4.jpg'];

  $newsletterEnabled = ($site['newsletter_enabled'] ?? '0') === '1';
  $newsletterNote = trim((string) ($site['newsletter_note'] ?? ''));
  $privacyUrl = trim((string) ($site['footer_privacy_url'] ?? ''));
  $termsUrl = trim((string) ($site['footer_terms_url'] ?? ''));
  if (\Illuminate\Support\Facades\Route::has('privacy-policy')) {
      $privacyUrl = route('privacy-policy');
  }
  if (\Illuminate\Support\Facades\Route::has('terms')) {
      $termsUrl = route('terms');
  }
  if ($privacyUrl === '') {
      $privacyUrl = '#';
  }
  if ($termsUrl === '') {
      $termsUrl = '#';
  }
@endphp

<footer class="bg-[#1e2229] text-white pt-20 pb-10">
  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
    <div class="space-y-6">
      <h4 class="text-brand_blue font-bold text-xs uppercase tracking-widest">{{ $copyrightName }}</h4>
      @if ($footerTagline !== '')
        <p class="text-slate-400 text-[13px] leading-relaxed">{{ $footerTagline }}</p>
      @endif
    </div>
    <div class="space-y-6">
      <h4 class="text-white font-bold text-xs uppercase tracking-widest">{{ __('Photo Gallery') }}</h4>
      <div class="grid grid-cols-4 gap-2">
        @for ($i = 0; $i < 4; $i++)
          @php
            $imgSrc = isset($footerGallery[$i]) ? \App\Support\VehicleImageUrl::url($footerGallery[$i]) : \App\Support\PlaceholderMedia::url($fallbacks[$i]);
          @endphp
          <img src="{{ $imgSrc }}" alt="Gallery Image {{ $i + 1 }}" class="w-full h-12 object-cover rounded-sm bg-slate-700" loading="lazy" />
        @endfor
      </div>
    </div>
    <div class="space-y-6">
      <h4 class="text-white font-bold text-xs uppercase tracking-widest">{{ __('Top 5 car makes') }}</h4>
      @if ($topMakes->isNotEmpty())
        <div class="space-y-3">
          @foreach ($topMakes as $mk)
            <a
              href="{{ route('inventory.index', ['make_listing_option_id' => $mk->id]) }}"
              class="flex items-center justify-between gap-4 text-slate-300 text-[13px] font-medium hover:text-white"
            >
              <span class="truncate">{{ $mk->label }}</span>
              <span class="shrink-0 rounded-full bg-white/10 px-2 py-0.5 text-[11px] font-bold text-white/80">{{ number_format((int) $mk->count) }}</span>
            </a>
          @endforeach
        </div>
      @else
        <p class="text-slate-400 text-[13px]">{{ __('Top makes will appear once listings are available.') }}</p>
      @endif
    </div>
    <div class="space-y-6">
      <h4 class="text-white font-bold text-xs uppercase tracking-widest">{{ __('Social Network') }}</h4>
      <div class="flex flex-wrap gap-4">
        @foreach (['facebook' => $socialFacebook, 'instagram' => $socialInstagram, 'linkedin' => $socialLinkedin, 'youtube' => $socialYoutube] as $net => $url)
          @if ($url !== '' && $url !== '#')
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-slate-700 rounded flex items-center justify-center hover:bg-brand_blue transition-colors" aria-label="{{ ucfirst($net) }}">
              <span class="material-symbols-outlined text-sm">
                @if ($net === 'facebook')
                  share
                @elseif ($net === 'instagram')
                  camera_alt
                @elseif ($net === 'linkedin')
                  group
                @else
                  play_arrow
                @endif
              </span>
            </a>
          @endif
        @endforeach
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8 py-10 border-y border-slate-700 mb-10">
    <div>
      <h4 class="font-bold text-xs uppercase mb-6 tracking-widest">{{ __('Subscribe') }}</h4>
      @if ($newsletterEnabled)
        <form method="post" action="{{ route('newsletter.subscribe') }}" class="space-y-2">
          @csrf
          <div class="flex">
            <input name="email" value="{{ old('email') }}" class="bg-white text-slate-900 border-none px-4 py-3 text-[13px] w-full rounded-l-sm" placeholder="{{ __('Enter your email...') }}" type="email" autocomplete="email" required />
            <button type="submit" class="bg-brand_orange text-white px-4 flex items-center justify-center rounded-r-sm shrink-0" aria-label="{{ __('Subscribe') }}"><span class="material-symbols-outlined">send</span></button>
          </div>
          @if ($newsletterNote !== '')
            <p class="text-[11px] text-slate-500">{{ $newsletterNote }}</p>
          @endif
          @error('newsletter_email')
            <p class="text-[11px] text-red-400">{{ $message }}</p>
          @enderror
        </form>
      @else
        <p class="text-[12px] text-slate-500">{{ __('Newsletter signup is not enabled.') }}</p>
      @endif
    </div>
    <div>
      <h4 class="font-bold text-xs uppercase mb-6 tracking-widest">{{ __('Sales Hours') }}</h4>
      <div class="text-[12px] text-slate-400 space-y-1">@foreach ($salesHoursLines as $line)<p>{{ $line }}</p>@endforeach</div>
    </div>
    <div>
      <h4 class="font-bold text-xs uppercase mb-6 tracking-widest">{{ __('Service Hours') }}</h4>
      <div class="text-[12px] text-slate-400 space-y-1">@foreach ($serviceHoursLines as $line)<p>{{ $line }}</p>@endforeach</div>
    </div>
    <div>
      <h4 class="font-bold text-xs uppercase mb-6 tracking-widest">{{ __('Parts Hours') }}</h4>
      <div class="text-[12px] text-slate-400 space-y-1">@foreach ($partsHoursLines as $line)<p>{{ $line }}</p>@endforeach</div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-[11px] text-slate-500 font-medium">
    <p>&copy; {{ date('Y') }} {{ $copyrightName }}. {{ __('All rights reserved.') }}</p>
    <div class="flex items-center gap-6 mt-4 md:mt-0">
      <a class="hover:text-white transition-colors" href="{{ $privacyUrl }}">{{ __('Privacy Policy') }}</a>
      <a class="hover:text-white transition-colors" href="{{ $termsUrl }}">{{ __('Terms of Service') }}</a>
    </div>
  </div>
</footer>

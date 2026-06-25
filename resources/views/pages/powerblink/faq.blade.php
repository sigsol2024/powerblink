@extends('layouts.site')

@php
  $s = $sections ?? [];
  $heroImg = \App\Support\PlaceholderMedia::url($s['hero_image'] ?? 'asset/images/powerblink/home-powerblink-fc-044.jpg');
  $ctaImg = \App\Support\PlaceholderMedia::url($s['cta_image'] ?? 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg');
@endphp

@push('head')
<style>
  .faq-accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
  [data-faq-item].is-open .faq-accordion-content { max-height: 480px; }
  [data-faq-item].is-open .faq-chevron { transform: rotate(180deg); }
</style>
@endpush

@section('content')
  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImg,
    'eyebrow' => $s['kicker'] ?? __('Need Help?'),
    'title' => $s['heading'] ?? __('Help Center'),
    'subtitle' => $s['intro'] ?? __('Common questions about registration, training, and academy policies at Powerblink FC.'),
  ])

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
      <aside class="lg:col-span-4">
        <div class="lg:sticky lg:top-28 space-y-3">
          <h2 class="font-headline-md text-primary-container uppercase tracking-widest text-label-caps mb-6">{{ __('Knowledge Base') }}</h2>
          <nav class="flex flex-col gap-2">
            @foreach ([1, 2, 3, 4] as $catIdx)
              @php $catTitle = $s['cat_'.$catIdx.'_title'] ?? ''; @endphp
              @if ($catTitle)
                <a href="#cat-{{ $catIdx }}" class="flex items-center justify-between p-4 rounded-xl bg-surface-container-lowest border border-outline-variant/30 hover:border-secondary transition-colors group min-h-[44px]">
                  <span class="font-headline-md text-sm text-primary">{{ $catTitle }}</span>
                  <span class="material-symbols-outlined text-secondary group-hover:scale-110 transition-transform">{{ $s['cat_'.$catIdx.'_icon'] ?? 'help' }}</span>
                </a>
              @endif
            @endforeach
          </nav>

          <div class="mt-8 p-6 rounded-2xl bg-primary-container text-on-primary border border-white/10">
            <h3 class="font-headline-md text-on-primary mb-2">{{ __('Academy Support') }}</h3>
            <p class="text-sm text-on-primary-container mb-4">{{ __('Our office team can help with registration, payments, and program placement.') }}</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 text-secondary-fixed font-bold text-sm min-h-[44px]">
              {{ __('Contact Us') }}
              <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </a>
          </div>
        </div>
      </aside>

      <div class="lg:col-span-8 space-y-12">
        @foreach ([1, 2, 3, 4] as $catIdx)
          @php $catTitle = $s['cat_'.$catIdx.'_title'] ?? ''; @endphp
          @if ($catTitle)
            <div id="cat-{{ $catIdx }}" class="scroll-mt-28">
              <div class="flex items-center gap-4 mb-6">
                <span class="text-secondary font-bold tracking-[0.2em] uppercase text-label-caps">{{ __('Category') }}</span>
                <h3 class="font-headline-lg text-primary-container">{{ $catTitle }}</h3>
              </div>
              <div class="space-y-3">
                @php
                  $faqsRaw = $s['cat_'.$catIdx.'_faqs'] ?? '[]';
                  $faqs = json_decode($faqsRaw, true) ?: [];
                @endphp
                @foreach ($faqs as $faq)
                  @php $q = $faq['q'] ?? ''; $a = $faq['a'] ?? ''; @endphp
                  @if ($q)
                    <div class="rounded-xl bg-surface-container-lowest border border-outline-variant/30 overflow-hidden" data-faq-item>
                      <button type="button" class="w-full p-4 sm:p-5 text-left flex justify-between items-start gap-4 min-h-[44px]" data-faq-toggle>
                        <span class="font-headline-md text-primary pr-4">{{ $q }}</span>
                        <span class="material-symbols-outlined text-secondary shrink-0 faq-chevron transition-transform">expand_more</span>
                      </button>
                      <div class="faq-accordion-content">
                        <div class="px-4 sm:px-5 pb-5 text-sm text-on-surface-variant leading-relaxed border-t border-outline-variant/20 pt-4">
                          {{ $a }}
                        </div>
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  </section>

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop">
    <div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-2 gap-gutter items-center bg-secondary-container rounded-[2rem] p-8 md:p-12 overflow-hidden">
      <div>
        <h2 class="font-headline-lg text-on-secondary-fixed mb-4">{{ $s['cta_title'] ?? __('Still Have Questions?') }}</h2>
        <p class="text-on-secondary-container mb-6">{{ $s['cta_body'] ?? __('Contact our academy office Monday through Saturday for registration and program support.') }}</p>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('contact') }}" class="inline-flex items-center min-h-[44px] px-6 py-3 rounded-xl bg-primary text-on-primary font-headline-md text-sm">{{ __('Contact Us') }}</a>
          <a href="{{ route('registration.wizard') }}" class="inline-flex items-center min-h-[44px] px-6 py-3 rounded-xl border-2 border-primary text-primary font-headline-md text-sm">{{ __('Register Now') }}</a>
        </div>
      </div>
      <div class="rounded-2xl overflow-hidden min-h-[240px] md:min-h-[320px]">
        <img src="{{ $ctaImg }}" alt="" class="w-full h-full object-cover" />
      </div>
    </div>
  </section>
@endsection

@push('scripts')
<script>
(function () {
  document.querySelectorAll('[data-faq-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('[data-faq-item]');
      if (!item) return;
      var open = item.classList.contains('is-open');
      document.querySelectorAll('[data-faq-item].is-open').forEach(function (el) { el.classList.remove('is-open'); });
      if (!open) item.classList.add('is-open');
    });
  });
  var h = window.location.hash;
  if (h && document.querySelector(h)) {
    document.querySelector(h).scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
})();
</script>
@endpush

@extends('layouts.site')

@php
  $site = $site ?? [];
  $s = $sections ?? [];

  $email = trim($s['services_email'] ?? '');
  $phone = trim($s['services_phone'] ?? '');
  $hours = trim($s['services_hours'] ?? '');
  $studioAddress = trim($s['studio_address'] ?? '');
  $studioAddressPlain = trim(strip_tags(str_replace(['<br/>', '<br />', '<br>'], ', ', $studioAddress)));
  $mapHref = trim($s['map_link_url'] ?? '');
  if ($mapHref === '' && $studioAddressPlain !== '') {
      $mapHref = 'https://www.google.com/maps/search/?api=1&query='.urlencode($studioAddressPlain);
  }

  $instagramUrl = trim($s['social_instagram_url'] ?? '') ?: trim($site['social_instagram'] ?? '');
  $twitterUrl = trim($s['social_twitter_url'] ?? '');
  $atmosphericImg = \App\Support\VehicleImageUrl::url($s['atmospheric_image'] ?? 'asset/images/media/contact-map.jpg');
@endphp

@push('head')
<style>
  .luxe-subtle-pattern {
    background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
    opacity: 0.03;
    pointer-events: none;
  }
</style>
@endpush

@section('content')
  @if(session('status'))
    <div class="max-w-max-container mx-auto px-gutter pt-8">
      <div class="p-4 bg-surface-container text-on-surface border border-outline-variant font-body-md text-body-md">{{ session('status') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="max-w-max-container mx-auto px-gutter pt-8">
      <div class="p-4 bg-error-container text-on-error-container border border-error font-body-md text-body-md">
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <section class="py-section-py-mobile md:py-section-py-desktop relative overflow-hidden">
    <div class="absolute inset-0 luxe-subtle-pattern"></div>
    <div class="max-w-max-container mx-auto px-gutter relative z-10 text-center">
      <span class="font-label-caps text-label-caps text-on-surface-variant tracking-[0.3em] uppercase block mb-6">{{ $s['hero_kicker'] ?? 'Concierge' }}</span>
      <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-8">{{ $s['hero_title'] ?? 'Get in Touch' }}</h1>
      <p class="max-w-2xl mx-auto font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $s['hero_intro'] ?? '' }}</p>
    </div>
  </section>

  <section class="max-w-max-container mx-auto px-gutter pb-section-py-mobile md:pb-section-py-desktop grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
    <div class="lg:col-span-4 space-y-12">
      <div>
        <h3 class="font-headline-md text-headline-md text-primary mb-6 border-b border-outline-variant pb-2">{{ __('Client Services') }}</h3>
        <div class="space-y-4">
          @if($email !== '')
            <div>
              <p class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-1">{{ __('Email') }}</p>
              <a class="font-body-md text-body-md hover:text-secondary transition-colors duration-300" href="mailto:{{ $email }}">{{ $email }}</a>
            </div>
          @endif
          @if($phone !== '')
            <div>
              <p class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-1">{{ __('Phone') }}</p>
              <a class="font-body-md text-body-md hover:text-secondary transition-colors duration-300" href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a>
            </div>
          @endif
          @if($hours !== '')
            <div>
              <p class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-1">{{ __('Hours') }}</p>
              <div class="font-body-md text-body-md text-on-surface-variant space-y-1">{!! nl2br(e($hours)) !!}</div>
            </div>
          @endif
        </div>
      </div>

      @if($studioAddress !== '')
        <div>
          <h3 class="font-headline-md text-headline-md text-primary mb-6 border-b border-outline-variant pb-2">{{ $s['studio_title'] ?? __('Flagship Studio') }}</h3>
          <p class="font-body-md text-body-md text-on-surface-variant mb-4 whitespace-pre-line">{{ $studioAddress }}</p>
          @if($mapHref !== '')
            <a class="inline-flex items-center gap-2 font-label-caps text-label-caps uppercase border-b border-primary pb-1 hover:text-secondary hover:border-secondary transition-all" href="{{ $mapHref }}" target="_blank" rel="noopener noreferrer">
              {{ __('View on Map') }}
              <x-icon name="arrow-right" class="w-3.5 h-3.5" />
            </a>
          @endif
        </div>
      @endif

      @if($instagramUrl !== '' || $twitterUrl !== '')
        <div>
          <h3 class="font-headline-md text-headline-md text-primary mb-6 border-b border-outline-variant pb-2">{{ __('Follow The Journey') }}</h3>
          <div class="flex flex-wrap gap-8">
            @if($instagramUrl !== '')
              <a class="font-label-caps text-label-caps uppercase tracking-widest hover:text-secondary transition-colors duration-300" href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">{{ $s['social_instagram_label'] ?? 'Instagram' }}</a>
            @endif
            @if($twitterUrl !== '')
              <a class="font-label-caps text-label-caps uppercase tracking-widest hover:text-secondary transition-colors duration-300" href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer">{{ $s['social_twitter_label'] ?? 'Twitter' }}</a>
            @endif
          </div>
        </div>
      @endif
    </div>

    <div class="lg:col-span-8 bg-surface-container-low p-8 md:p-12">
      <form class="space-y-8" method="post" action="{{ route('contact.submit') }}" id="contact-form">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2 block" for="contact-name">{{ __('Name') }}</label>
            <input class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 font-body-md text-body-md placeholder:text-outline-variant focus:ring-0 transition-all duration-300" id="contact-name" name="name" type="text" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}" required />
          </div>
          <div>
            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2 block" for="contact-email">{{ __('Email Address') }}</label>
            <input class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 font-body-md text-body-md placeholder:text-outline-variant focus:ring-0 transition-all duration-300" id="contact-email" name="email" type="email" placeholder="example@domain.com" value="{{ old('email') }}" required />
          </div>
        </div>
        <div>
          <label class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2 block" for="contact-subject">{{ __('Subject') }}</label>
          <select class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 font-body-md text-body-md focus:ring-0 transition-all duration-300 appearance-none cursor-pointer" id="contact-subject" name="subject">
            <option value="Bespoke Consultation" @selected(old('subject') === 'Bespoke Consultation')>{{ __('Bespoke Consultation') }}</option>
            <option value="Order Inquiry" @selected(old('subject') === 'Order Inquiry')>{{ __('Order Inquiry') }}</option>
            <option value="Press & Media" @selected(old('subject') === 'Press & Media')>{{ __('Press & Media') }}</option>
            <option value="General Inquiry" @selected(old('subject', 'General Inquiry') === 'General Inquiry')>{{ __('General Inquiry') }}</option>
          </select>
        </div>
        <div>
          <label class="font-label-caps text-label-caps text-on-surface-variant uppercase mb-2 block" for="contact-message">{{ __('Message') }}</label>
          <textarea class="w-full bg-transparent border-0 border-b border-outline-variant py-3 px-0 font-body-md text-body-md placeholder:text-outline-variant focus:ring-0 transition-all duration-300 resize-none" id="contact-message" name="message" rows="4" placeholder="{{ __('How may we assist you?') }}" required>{{ old('message') }}</textarea>
        </div>
        <div class="pt-4">
          <button class="bg-primary text-on-primary font-button-text text-button-text uppercase tracking-widest px-12 py-4 hover:scale-[1.02] transition-transform duration-300 w-full md:w-auto" type="submit" id="contact-submit">
            {{ __('Send Inquiry') }}
          </button>
        </div>
      </form>
    </div>
  </section>

  <section class="max-w-max-container mx-auto px-gutter pb-section-py-mobile md:pb-section-py-desktop">
    <div class="h-[400px] overflow-hidden relative group">
      <img alt="" class="w-full h-full object-cover grayscale opacity-90 transition-transform duration-700 group-hover:scale-105" src="{{ $atmosphericImg }}" />
      <div class="absolute inset-0 bg-primary/10 mix-blend-multiply"></div>
      @if(trim($s['atmospheric_quote'] ?? '') !== '')
        <div class="absolute inset-0 flex items-center justify-center p-8">
          <p class="text-center font-headline-md text-headline-md italic text-on-primary max-w-xl">{{ $s['atmospheric_quote'] }}</p>
        </div>
      @endif
    </div>
  </section>
@endsection

@push('scripts')
<script>
  (function () {
    const form = document.getElementById('contact-form');
    if (!form || form.dataset.bound === '1') return;
    form.dataset.bound = '1';
    form.addEventListener('submit', function () {
      const btn = document.getElementById('contact-submit');
      if (!btn || btn.disabled) return;
      btn.textContent = @json(__('Sending...'));
      btn.disabled = true;
    });
  })();
</script>
@endpush

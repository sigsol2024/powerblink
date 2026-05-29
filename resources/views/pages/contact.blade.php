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

    <div class="lg:col-span-8 bg-surface-container-low p-8 md:p-12" id="contact-form-panel">
      @if(session('status') && ! $errors->any())
        <div id="contact-success" class="py-6 md:py-10 text-center" tabindex="-1">
          <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full border border-outline-variant bg-surface text-primary">
            <x-icon name="check" class="h-8 w-8" />
          </div>
          <h2 class="font-headline-md text-headline-md text-primary mb-4">{{ __('Message sent') }}</h2>
          <p class="mx-auto mb-3 max-w-lg font-body-md text-body-md leading-relaxed text-on-surface">
            {{ session('status') }}
          </p>
          <p class="mx-auto mb-10 max-w-lg font-body-md text-body-md leading-relaxed text-on-surface-variant">
            {{ __('We appreciate you taking the time to write to us. A member of our sales team will review your inquiry and respond as soon as possible.') }}
          </p>
          <a href="{{ route('contact') }}" class="inline-block border border-primary px-8 py-3 font-button-text text-button-text uppercase tracking-widest text-primary transition-colors duration-300 hover:bg-primary hover:text-on-primary">
            {{ __('Send another message') }}
          </a>
        </div>
      @else
      <div id="contact-form-errors" class="hidden mb-6 p-4 border border-error bg-error-container text-on-error-container font-body-md text-body-md" role="alert" aria-live="polite">
        <ul class="list-disc pl-5 space-y-1" data-contact-error-list></ul>
      </div>
      <div id="contact-form-wrap">
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
      @endif
    </div>
  </section>
@endsection

@push('scripts')
<script>
  (function () {
    const success = document.getElementById('contact-success');
    if (success) {
      success.scrollIntoView({ behavior: 'smooth', block: 'center' });
      success.focus({ preventScroll: true });
    }

    const form = document.getElementById('contact-form');
    const panel = document.getElementById('contact-form-panel');
    const formWrap = document.getElementById('contact-form-wrap');
    const errorBox = document.getElementById('contact-form-errors');
    const errorList = errorBox ? errorBox.querySelector('[data-contact-error-list]') : null;
    const submitBtn = document.getElementById('contact-submit');
    const submitLabel = @json(__('Send Inquiry'));
    const csrf = document.querySelector('meta[name="csrf-token"]');

    if (!form || !panel || form.dataset.bound === '1') return;
    form.dataset.bound = '1';

    function showErrors(messages) {
      if (!errorBox || !errorList) return;
      errorList.innerHTML = '';
      messages.forEach(function (msg) {
        const li = document.createElement('li');
        li.textContent = msg;
        errorList.appendChild(li);
      });
      errorBox.classList.remove('hidden');
      errorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function clearErrors() {
      if (!errorBox || !errorList) return;
      errorList.innerHTML = '';
      errorBox.classList.add('hidden');
    }

    function showSuccess(data) {
      const existing = document.getElementById('contact-success');
      if (existing) existing.remove();

      const wrap = document.createElement('div');
      wrap.id = 'contact-success';
      wrap.className = 'py-6 md:py-10 text-center';
      wrap.tabIndex = -1;
      wrap.innerHTML =
        '<div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full border border-outline-variant bg-surface text-primary">' +
          '<svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">' +
            '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />' +
          '</svg>' +
        '</div>' +
        '<h2 class="font-headline-md text-headline-md text-primary mb-4"></h2>' +
        '<p class="mx-auto mb-3 max-w-lg font-body-md text-body-md leading-relaxed text-on-surface contact-success-message"></p>' +
        '<p class="mx-auto mb-10 max-w-lg font-body-md text-body-md leading-relaxed text-on-surface-variant contact-success-detail"></p>' +
        '<button type="button" class="inline-block border border-primary px-8 py-3 font-button-text text-button-text uppercase tracking-widest text-primary transition-colors duration-300 hover:bg-primary hover:text-on-primary contact-success-reset"></button>';

      wrap.querySelector('h2').textContent = data.title || @json(__('Message sent'));
      wrap.querySelector('.contact-success-message').textContent = data.message || '';
      wrap.querySelector('.contact-success-detail').textContent = data.detail || '';
      wrap.querySelector('.contact-success-reset').textContent = data.sendAnother || @json(__('Send another message'));

      if (formWrap) formWrap.classList.add('hidden');
      if (errorBox) errorBox.classList.add('hidden');
      panel.appendChild(wrap);

      wrap.querySelector('.contact-success-reset').addEventListener('click', function () {
        wrap.remove();
        if (formWrap) {
          formWrap.classList.remove('hidden');
          form.reset();
        }
        clearErrors();
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = submitLabel;
        }
      });

      wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
      wrap.focus({ preventScroll: true });
    }

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearErrors();

      if (!submitBtn || submitBtn.disabled) return;

      submitBtn.textContent = @json(__('Sending...'));
      submitBtn.disabled = true;

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf ? csrf.content : '',
          },
          body: new FormData(form),
        });

        const data = await response.json().catch(function () { return {}; });

        if (response.ok) {
          showSuccess(data);
          return;
        }

        const messages = [];
        if (data.errors) {
          Object.keys(data.errors).forEach(function (key) {
            (data.errors[key] || []).forEach(function (msg) { messages.push(msg); });
          });
        }
        if (!messages.length && data.message) {
          messages.push(data.message);
        }
        if (!messages.length) {
          messages.push(@json(__('Could not send message. Please try again later.')));
        }
        showErrors(messages);
      } catch (err) {
        showErrors([@json(__('Could not send message. Please check your connection and try again.'))]);
      } finally {
        if (submitBtn && document.getElementById('contact-form-wrap') && !document.getElementById('contact-form-wrap').classList.contains('hidden')) {
          submitBtn.disabled = false;
          submitBtn.textContent = submitLabel;
        }
      }
    });
  })();
</script>
@endpush

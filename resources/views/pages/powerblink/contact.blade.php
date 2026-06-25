@extends('layouts.site')

@php
  $s = $sections ?? [];
  $heroImage = \App\Support\PlaceholderMedia::url($s['atmospheric_image'] ?? 'asset/images/powerblink/contact-us-powerblink-fc-033.jpg');
  $mapImage = \App\Support\PlaceholderMedia::url('asset/images/powerblink/contact-us-powerblink-fc-034.jpg');
  $email = $s['services_email'] ?? '';
  $phone = $s['services_phone'] ?? '';
  $hours = $s['services_hours'] ?? '';
  $address = $s['studio_address'] ?? __('Ibeju Lekki Coastal Way, Lagos, Nigeria.');
  $mapHref = $s['map_link_url'] ?? 'https://maps.google.com';
  $quote = $s['atmospheric_quote'] ?? __('The distance between dreams and reality is called discipline. Our goal is to provide the bridge for every young talent in Nigeria.');
@endphp

@section('content')
  @include('partials.powerblink.cinematic-hero', [
    'image' => $heroImage,
    'eyebrow' => __('Get in Touch'),
    'title' => $s['hero_title'] ?? __('Contact Powerblink FC'),
    'subtitle' => $s['hero_intro'] ?? __('Our academy staff are available to answer questions about programs, trials, and registration.'),
    'primaryCta' => ['href' => '#contact-form', 'label' => __('Send a Message')],
  ])

  <section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-container-max mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
      <div class="bg-surface-container-lowest p-8 rounded-brand shadow-sm border border-outline-variant/30 card-hover fade-up">
        <div class="w-12 h-12 bg-secondary-container flex items-center justify-center rounded-xl mb-6 text-on-secondary-fixed">
          <span class="material-symbols-outlined text-2xl">school</span>
        </div>
        <h3 class="font-headline-md text-primary mb-4">{{ __('Academy Trials') }}</h3>
        <p class="text-on-surface-variant mb-6 text-sm">{{ __('Registration, age-group placement, and trial scheduling.') }}</p>
        @if ($email !== '')
          <a class="flex items-center gap-3 text-primary font-bold hover:underline text-sm" href="mailto:{{ $email }}">
            <span class="material-symbols-outlined">alternate_email</span> {{ $email }}
          </a>
        @endif
      </div>
      <div class="bg-surface-container-lowest p-8 rounded-brand shadow-sm border border-outline-variant/30 card-hover fade-up" style="transition-delay: 0.1s">
        <div class="w-12 h-12 bg-tertiary-fixed flex items-center justify-center rounded-xl mb-6 text-on-tertiary-fixed">
          <span class="material-symbols-outlined text-2xl">corporate_fare</span>
        </div>
        <h3 class="font-headline-md text-primary mb-4">{{ __('General Inquiries') }}</h3>
        <p class="text-on-surface-variant mb-6 text-sm">{{ __('Corporate partnerships, media requests, and administrative support.') }}</p>
        @if ($hours !== '')
          <p class="flex items-start gap-3 text-on-surface text-sm whitespace-pre-line">
            <span class="material-symbols-outlined shrink-0">schedule</span> {{ $hours }}
          </p>
        @endif
      </div>
      <div class="bg-surface-container-lowest p-8 rounded-brand shadow-sm border border-outline-variant/30 card-hover fade-up" style="transition-delay: 0.2s">
        <div class="w-12 h-12 bg-primary-container flex items-center justify-center rounded-xl mb-6 text-primary-fixed">
          <span class="material-symbols-outlined text-2xl">location_on</span>
        </div>
        <h3 class="font-headline-md text-primary mb-4">{{ __('Training Facility') }}</h3>
        <p class="text-on-surface-variant mb-4 text-sm leading-relaxed font-semibold text-on-surface">{{ $address }}</p>
        <a class="flex items-center gap-3 text-primary hover:text-secondary transition-colors text-sm font-bold" href="#map">
          <span class="material-symbols-outlined">map</span> {{ __('View Map Location') }}
        </a>
      </div>
    </div>
  </section>

  <section class="py-section-gap bg-primary-container text-white overflow-hidden relative" id="contact-form">
    <div class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">
      <div class="bg-surface-container-lowest p-8 md:p-10 rounded-brand shadow-xl text-on-surface fade-up">
        @if(session('status') && ! $errors->any())
          <div id="contact-success" class="py-8 text-center" tabindex="-1">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-secondary/10 text-secondary">
              <span class="material-symbols-outlined text-3xl">check_circle</span>
            </div>
            <h2 class="font-headline-md text-primary mb-4">{{ __('Message sent') }}</h2>
            <p class="text-on-surface-variant mb-8">{{ session('status') }}</p>
            <a href="{{ route('contact') }}" class="inline-block border border-primary px-8 py-3 font-bold text-primary hover:bg-primary hover:text-on-primary transition-colors rounded-lg">{{ __('Send another message') }}</a>
          </div>
        @else
          <h2 class="font-headline-lg text-primary mb-8">{{ __('Send a Message') }}</h2>
          <div id="contact-form-errors" class="hidden mb-6 p-4 border border-error bg-error-container text-on-error-container text-sm rounded-lg" role="alert">
            <ul class="list-disc pl-5 space-y-1" data-contact-error-list></ul>
          </div>
          <div id="contact-form-wrap">
            <form class="space-y-6" method="post" action="{{ route('contact.submit') }}" id="contact-form">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="font-label-caps text-label-caps text-outline" for="contact-name">{{ __('Full Name') }}</label>
                  <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 focus:ring-2 focus:ring-secondary outline-none" id="contact-name" name="name" type="text" value="{{ old('name') }}" required />
                </div>
                <div class="space-y-2">
                  <label class="font-label-caps text-label-caps text-outline" for="contact-email">{{ __('Email Address') }}</label>
                  <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 focus:ring-2 focus:ring-secondary outline-none" id="contact-email" name="email" type="email" value="{{ old('email') }}" required />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="font-label-caps text-label-caps text-outline" for="contact-phone">{{ __('Phone Number') }}</label>
                  <input class="w-full bg-surface border border-outline-variant rounded-lg p-3 focus:ring-2 focus:ring-secondary outline-none" id="contact-phone" name="phone" type="tel" value="{{ old('phone') }}" />
                </div>
                <div class="space-y-2">
                  <label class="font-label-caps text-label-caps text-outline" for="contact-subject">{{ __('Subject') }}</label>
                  <select class="w-full bg-surface border border-outline-variant rounded-lg p-3 focus:ring-2 focus:ring-secondary outline-none" id="contact-subject" name="subject">
                    <option value="Academy Trials" @selected(old('subject') === 'Academy Trials')>{{ __('Academy Trials') }}</option>
                    <option value="Partnership Inquiry" @selected(old('subject') === 'Partnership Inquiry')>{{ __('Partnership Inquiry') }}</option>
                    <option value="Facility Visit" @selected(old('subject') === 'Facility Visit')>{{ __('Facility Visit') }}</option>
                    <option value="General Inquiry" @selected(old('subject', 'General Inquiry') === 'General Inquiry')>{{ __('General Inquiry') }}</option>
                  </select>
                </div>
              </div>
              <div class="space-y-2">
                <label class="font-label-caps text-label-caps text-outline" for="contact-message">{{ __('Your Message') }}</label>
                <textarea class="w-full bg-surface border border-outline-variant rounded-lg p-3 focus:ring-2 focus:ring-secondary outline-none resize-none" id="contact-message" name="message" rows="4" required>{{ old('message') }}</textarea>
              </div>
              <button class="w-full py-4 bg-primary text-on-primary rounded-brand font-bold text-lg hover:bg-secondary transition-all flex items-center justify-center gap-2" type="submit" id="contact-submit">
                {{ __('Send Inquiry') }} <span class="material-symbols-outlined">send</span>
              </button>
            </form>
          </div>
        @endif
      </div>
      <div class="space-y-10 fade-up">
        <div>
          <h3 class="font-headline-md text-tertiary-fixed mb-4">{{ __('Elite Response Commitment') }}</h3>
          <p class="font-body-lg text-on-primary-container leading-relaxed">
            {{ __('We value precision both on the field and in our communications. Our team typically responds within') }}
            <span class="font-bold text-white">24–48 {{ __('hours') }}</span>.
          </p>
        </div>
        <div class="relative pl-8 border-l-4 border-secondary-fixed">
          <span class="material-symbols-outlined text-secondary-fixed text-4xl absolute -left-6 -top-4 opacity-50">format_quote</span>
          <blockquote class="font-headline-md italic text-white mb-4 leading-relaxed">{{ $quote }}</blockquote>
        </div>
      </div>
    </div>
  </section>

  <section class="h-[480px] md:h-[600px] relative" id="map">
    <div class="absolute inset-0 bg-cover bg-center grayscale contrast-125 opacity-50" style="background-image: url('{{ $mapImage }}')"></div>
    <div class="absolute bottom-8 left-8 md:bottom-auto md:top-20 md:left-margin-desktop w-full max-w-sm px-4 md:px-0">
      <div class="bg-surface-container-lowest p-8 rounded-brand shadow-2xl border-t-8 border-primary fade-up">
        <h3 class="font-headline-md text-primary mb-6">{{ __('Training Facility') }}</h3>
        <div class="space-y-4 text-sm">
          <div class="flex gap-4">
            <span class="material-symbols-outlined text-secondary shrink-0">location_on</span>
            <p class="text-on-surface-variant">{{ $address }}</p>
          </div>
          @if ($phone !== '')
            <div class="flex gap-4">
              <span class="material-symbols-outlined text-secondary shrink-0">call</span>
              <p>{{ $phone }}</p>
            </div>
          @endif
        </div>
        @if ($mapHref !== '')
          <a class="block w-full mt-6 py-3 bg-surface-container text-center rounded-lg font-bold text-primary hover:bg-primary hover:text-on-primary transition-all" href="{{ $mapHref }}" target="_blank" rel="noopener noreferrer">
            {{ __('Get Directions') }}
          </a>
        @endif
      </div>
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
    const formWrap = document.getElementById('contact-form-wrap');
    const errorBox = document.getElementById('contact-form-errors');
    const errorList = errorBox ? errorBox.querySelector('[data-contact-error-list]') : null;
    const submitBtn = document.getElementById('contact-submit');
    const submitLabel = @json(__('Send Inquiry'));
    const csrf = document.querySelector('meta[name="csrf-token"]');

    if (!form || !formWrap || form.dataset.bound === '1') return;
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
    }

    function clearErrors() {
      if (!errorBox || !errorList) return;
      errorList.innerHTML = '';
      errorBox.classList.add('hidden');
    }

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      clearErrors();
      if (!submitBtn || submitBtn.disabled) return;

      submitBtn.disabled = true;
      const original = submitBtn.innerHTML;
      submitBtn.textContent = @json(__('Sending...'));

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
          window.location.reload();
          return;
        }

        const messages = [];
        if (data.errors) {
          Object.keys(data.errors).forEach(function (key) {
            (data.errors[key] || []).forEach(function (msg) { messages.push(msg); });
          });
        }
        if (!messages.length && data.message) messages.push(data.message);
        if (!messages.length) messages.push(@json(__('Could not send message. Please try again later.')));
        showErrors(messages);
      } catch (err) {
        showErrors([@json(__('Could not send message. Please check your connection and try again.'))]);
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = original;
        }
      }
    });
  })();
</script>
@endpush

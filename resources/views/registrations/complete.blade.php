@extends('layouts.registration')

@section('content')
<div class="max-w-2xl mx-auto text-center">
  <div class="bg-surface-container-lowest rounded-xl p-8 md:p-12 shadow-sm border border-outline-variant/30">
    <span class="inline-flex w-16 h-16 rounded-full bg-secondary-container items-center justify-center mb-6">
      <x-icon name="check_circle" class="w-8 h-8 text-secondary" />
    </span>
    <h1 class="font-headline-lg text-headline-lg-mobile text-primary mb-3">{{ __('Application received') }}</h1>
    <p class="text-on-surface-variant mb-6">{{ __('Thank you. Your registration reference is:') }}</p>
    <p class="font-stat-md text-stat-md text-secondary mb-8 tracking-wider">{{ $referenceCode }}</p>
    <p class="text-body-md text-on-surface-variant mb-8">{{ __('Check your email for approval or rejection. Do not pay until your application is approved.') }}</p>
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary text-on-primary px-8 py-3 min-h-11 rounded-full font-bold hover:scale-[1.02] transition-transform">
      {{ __('Return home') }}
      <x-icon name="home" class="w-5 h-5" />
    </a>
  </div>
</div>
@endsection

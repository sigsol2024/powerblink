@extends('layouts.registration')

@section('content')
<div class="max-w-lg mx-auto text-center">
  <div class="bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-error/20">
    <span class="material-symbols-outlined text-error text-5xl mb-4">link_off</span>
    <h1 class="font-headline-md text-headline-md text-primary mb-4">{{ $title ?? __('Payment link unavailable') }}</h1>
    <p class="text-on-surface-variant">{{ __('This payment link is invalid, expired, or has already been used. Please contact the academy office if you need assistance.') }}</p>
    <a href="{{ route('home') }}" class="inline-block mt-8 text-secondary font-semibold hover:underline">{{ __('Return home') }}</a>
  </div>
</div>
@endsection

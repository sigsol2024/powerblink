@extends('layouts.registration')

@section('content')
<div class="max-w-xl mx-auto">
  <div class="bg-surface-container-lowest rounded-xl p-6 md:p-10 shadow-sm border border-outline-variant/30">
    <div class="flex items-center gap-3 mb-6">
      <span class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center">
        <x-icon name="payments" class="w-7 h-7 text-secondary" />
      </span>
      <div>
        <h1 class="font-headline-lg text-headline-lg-mobile text-primary">{{ __('Registration payment') }}</h1>
        <p class="text-sm text-on-surface-variant">{{ __('Secure checkout via Paystack') }}</p>
      </div>
    </div>

    <dl class="space-y-4 mb-8">
      @foreach ([
        __('Player') => $registration->player_name,
        __('Program') => $registration->program?->name,
        __('Guardian') => $registration->guardian?->name,
        __('Fee') => $feeDisplay,
        __('Payment plan') => str_replace('_', ' ', $registration->payment_plan),
      ] as $label => $value)
        <div class="flex justify-between gap-4 py-3 border-b border-outline-variant/50">
          <dt class="text-label-caps text-xs uppercase text-on-surface-variant">{{ $label }}</dt>
          <dd class="font-semibold text-primary text-right">{{ $value ?? '—' }}</dd>
        </div>
      @endforeach
    </dl>

    <form method="POST" action="{{ route('registration.pay.initialize', $token) }}">
      @csrf
      <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-secondary text-on-secondary px-6 py-4 min-h-12 rounded-full font-bold text-lg hover:scale-[1.01] active:scale-95 transition-transform">
        <x-icon name="lock" class="w-5 h-5" />
        {{ __('Pay now') }}
      </button>
    </form>

    <p class="mt-6 text-center text-xs text-on-surface-variant">{{ __('You will be redirected to Paystack to complete payment securely.') }}</p>
  </div>
</div>
@endsection

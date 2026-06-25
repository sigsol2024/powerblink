@extends('emails.layouts.branded')

@section('content')
<p>{{ __('Congratulations! Your registration for :player has been approved.', ['player' => $registration->player_name]) }}</p>
<p>{{ __('Program') }}: <strong>{{ $registration->program?->name }}</strong></p>
<p>{{ __('Registration fee') }}: <strong>{{ $feeDisplay }}</strong></p>
<p style="margin:24px 0;">
  <a href="{{ $payUrl }}" style="display:inline-block;background:#ffb129;color:#111;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;">{{ __('Pay now') }}</a>
</p>
<p>{{ __('Reference') }}: {{ $registration->reference_code }}</p>
@endsection

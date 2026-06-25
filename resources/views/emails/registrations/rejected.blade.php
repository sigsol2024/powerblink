@extends('emails.layouts.branded')

@section('content')
<p>{{ __('Thank you for applying to PowerBlink FC. Unfortunately we cannot offer a place at this time for :player.', ['player' => $registration->player_name]) }}</p>
@if ($registration->rejected_reason)
  <p><strong>{{ __('Reason') }}:</strong> {{ $registration->rejected_reason }}</p>
@endif
<p>{{ __('Reference') }}: {{ $registration->reference_code }}</p>
@endsection

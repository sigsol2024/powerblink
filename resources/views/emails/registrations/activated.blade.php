@extends('emails.layouts.branded')

@section('content')
<p>{{ __('Payment received — :player is now an active academy player!', ['player' => $player->name]) }}</p>
<p>{{ __('Player code') }}: <strong>{{ $player->player_code }}</strong></p>
<p>{{ __('Program') }}: {{ $registration->program?->name }}</p>
<p>{{ __('We will be in touch with training schedule and next steps.') }}</p>
@endsection

@extends('layouts.registration')

@section('content')
@php
  $selectedProgramId = (int) old('program_id', $wizard['program_id'] ?? 0);
  $selectedPlan = old('payment_plan', $wizard['payment_plan'] ?? 'lump_sum');
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6">
  @include('partials.powerblink.registration-progress', ['step' => $step])

  @if (! $season)
    <div class="rounded-xl border border-tertiary-fixed-dim/50 bg-tertiary-fixed/20 p-6 text-on-tertiary-fixed-variant">
      <p class="font-headline-md text-headline-md text-primary mb-1">{{ __('Registration closed') }}</p>
      <p>{{ __('Registration is not open — no active season is configured.') }}</p>
    </div>
  @else
    @if ($errors->any())
      <div class="mb-6 rounded-xl border border-error/30 bg-error-container p-4 text-sm text-on-error-container">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl p-6 md:p-10 shadow-sm border border-outline-variant/30">
      <form method="POST" action="{{ $step < 6 ? route('registration.wizard.step') : route('registration.wizard.submit') }}" class="space-y-8" @if($step === 4) enctype="multipart/form-data" @endif>
        @csrf
        @if ($step < 6)
          <input type="hidden" name="step" value="{{ $step }}">
        @endif

        <div>
          <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-2">{{ $steps[$step] }}</h2>
          <p class="text-on-surface-variant text-body-md">
            @switch($step)
              @case(1) {{ __("Let's start with the athlete's basic details.") }} @break
              @case(2) {{ __("Contact information for the athlete's legal guardian.") }} @break
              @case(3) {{ __('Crucial for athlete safety and performance monitoring.') }} @break
              @case(4) {{ __('Upload supporting documents (optional).') }} @break
              @case(5) {{ __('Select your pathway and payment preference.') }} @break
              @case(6) {{ __('Please confirm all details before submission.') }} @break
            @endswitch
          </p>
        </div>

        @if ($step === 1)
          <div class="grid grid-cols-1 md:grid-cols-2 gap-element-gap">
            <div class="space-y-2 md:col-span-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Player name') }}</label>
              <input type="text" name="player_name" value="{{ old('player_name', $wizard['player_name'] ?? '') }}" required
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Date of birth') }}</label>
              <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $wizard['date_of_birth'] ?? '') }}" required
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Nationality') }}</label>
              <input type="text" name="nationality" value="{{ old('nationality', $wizard['nationality'] ?? '') }}"
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Primary position') }}</label>
              <input type="text" name="primary_position" value="{{ old('primary_position', $wizard['primary_position'] ?? '') }}"
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Secondary position') }}</label>
              <input type="text" name="secondary_position" value="{{ old('secondary_position', $wizard['secondary_position'] ?? '') }}"
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
          </div>
        @elseif ($step === 2)
          <div class="grid grid-cols-1 md:grid-cols-2 gap-element-gap">
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Guardian name') }}</label>
              <input type="text" name="guardian_name" value="{{ old('guardian_name', $wizard['guardian_name'] ?? '') }}" required
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Relationship') }}</label>
              <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship', $wizard['guardian_relationship'] ?? '') }}"
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Phone') }}</label>
              <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $wizard['guardian_phone'] ?? '') }}"
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Email') }}</label>
              <input type="email" name="guardian_email" value="{{ old('guardian_email', $wizard['guardian_email'] ?? '') }}" required
                     class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md" />
            </div>
            <div class="space-y-2 md:col-span-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Address') }}</label>
              <textarea name="guardian_address" rows="3"
                        class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md">{{ old('guardian_address', $wizard['guardian_address'] ?? '') }}</textarea>
            </div>
          </div>
        @elseif ($step === 3)
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Allergies') }}</label>
              <textarea name="allergies" rows="3"
                        class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md">{{ old('allergies', $wizard['allergies'] ?? '') }}</textarea>
            </div>
            <div class="space-y-2">
              <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Medical history') }}</label>
              <textarea name="medical_history" rows="4"
                        class="w-full bg-surface border-outline-variant rounded-lg p-4 input-focus transition-all font-body-md">{{ old('medical_history', $wizard['medical_history'] ?? '') }}</textarea>
            </div>
            <label class="flex items-start gap-3 p-4 bg-primary-container/10 rounded-lg border border-primary-container/20 cursor-pointer">
              <input type="checkbox" name="fitness_certified" value="1" @checked(old('fitness_certified', $wizard['fitness_certified'] ?? false))
                     class="mt-1 w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary" />
              <span class="text-sm text-on-primary-fixed-variant">{{ __('I certify the player is fit to participate in academy training.') }}</span>
            </label>
          </div>
        @elseif ($step === 4)
          <p class="text-sm text-on-surface-variant mb-2">{{ __('Accepted formats: JPG, PNG, WEBP, or PDF up to 5MB each.') }}</p>
          <div class="space-y-5">
            @foreach (['profile_photo' => __('Profile photo'), 'birth_certificate' => __('Birth certificate'), 'medical_clearance' => __('Medical clearance')] as $field => $label)
              <div class="space-y-2">
                <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ $label }}</label>
                <div class="flex items-center gap-4 border-2 border-dashed border-outline-variant rounded-lg p-4 bg-surface/50">
                  <span class="material-symbols-outlined text-outline">upload_file</span>
                  <input type="file" name="{{ $field }}" class="text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-container file:text-on-primary-container hover:file:brightness-110 cursor-pointer w-full" />
                </div>
              </div>
            @endforeach
          </div>
        @elseif ($step === 5)
          <div class="grid grid-cols-1 md:grid-cols-2 gap-element-gap mb-8">
            @forelse ($programs as $program)
              <label class="relative flex flex-col p-6 border-2 rounded-xl cursor-pointer hover:bg-surface transition-all has-[:checked]:border-secondary has-[:checked]:bg-secondary-container/10 min-h-[11rem]">
                <input type="radio" name="program_id" value="{{ $program->id }}" @checked($selectedProgramId === $program->id) required
                       class="absolute top-4 right-4 text-secondary focus:ring-secondary w-5 h-5" />
                <span class="font-headline-md text-primary mb-1 pr-8">{{ $program->name }}</span>
                <span class="text-sm text-on-surface-variant mb-4">{{ $program->age_group }}</span>
                @if ($program->registration_fee)
                  <span class="font-stat-md text-stat-md text-secondary mt-auto">{{ format_currency($program->registration_fee) }}</span>
                @endif
              </label>
            @empty
              <p class="md:col-span-2 text-on-surface-variant">{{ __('No programs available for this season.') }}</p>
            @endforelse
          </div>
          <div class="space-y-4" x-data="{ plan: '{{ $selectedPlan }}' }">
            <label class="text-label-caps font-label-caps uppercase text-on-surface text-xs">{{ __('Payment plan') }}</label>
            <input type="hidden" name="payment_plan" :value="plan">
            <div class="flex flex-wrap gap-3">
              <button type="button" @click="plan = 'lump_sum'"
                      :class="plan === 'lump_sum' ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant text-on-surface hover:border-primary'"
                      class="px-6 py-3 min-h-11 border-2 rounded-lg font-bold transition-all">{{ __('Lump sum') }}</button>
              <button type="button" @click="plan = 'installments'"
                      :class="plan === 'installments' ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant text-on-surface hover:border-primary'"
                      class="px-6 py-3 min-h-11 border-2 rounded-lg font-bold transition-all">{{ __('Installments') }}</button>
            </div>
          </div>
        @elseif ($step === 6)
          @php
            $reviewProgram = $programs->firstWhere('id', $wizard['program_id'] ?? 0);
          @endphp
          <div class="p-6 bg-surface-container rounded-xl grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
              <p class="text-label-caps text-on-surface-variant mb-1 uppercase text-xs">{{ __('Player') }}</p>
              <p class="font-bold text-lg text-primary">{{ $wizard['player_name'] ?? '—' }}</p>
            </div>
            <div>
              <p class="text-label-caps text-on-surface-variant mb-1 uppercase text-xs">{{ __('Guardian') }}</p>
              <p class="font-bold text-lg text-primary">{{ $wizard['guardian_name'] ?? '—' }}</p>
              <p class="text-on-surface-variant text-sm">{{ $wizard['guardian_email'] ?? '' }}</p>
            </div>
            <div>
              <p class="text-label-caps text-on-surface-variant mb-1 uppercase text-xs">{{ __('Program') }}</p>
              <p class="font-bold text-lg text-primary">{{ $reviewProgram?->name ?? '—' }}</p>
            </div>
            <div>
              <p class="text-label-caps text-on-surface-variant mb-1 uppercase text-xs">{{ __('Payment plan') }}</p>
              <p class="font-bold text-lg text-primary">{{ str_replace('_', ' ', $wizard['payment_plan'] ?? 'lump_sum') }}</p>
            </div>
          </div>
          <div class="p-4 bg-secondary-container/20 border border-secondary/20 rounded-xl flex items-start gap-4">
            <span class="material-symbols-outlined text-secondary shrink-0">info</span>
            <p class="text-sm text-on-secondary-container">{{ __('No payment is required now. You will receive an email after admin review.') }}</p>
          </div>
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="terms_accepted" value="1" required class="mt-1 w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary" />
            <span class="text-sm">{{ __('I accept the academy terms and conditions') }}</span>
          </label>
        @endif

        <div class="flex items-center justify-between pt-6 border-t border-outline-variant/30 gap-4">
          @if ($step > 1)
            <a href="{{ route('registration.wizard', ['step' => $step - 1]) }}"
               class="inline-flex items-center gap-2 px-6 py-3 min-h-11 font-bold text-on-surface hover:bg-surface-container-high rounded-full transition-all">
              <span class="material-symbols-outlined">arrow_back</span>
              {{ __('Back') }}
            </a>
          @else
            <span></span>
          @endif
          <button type="submit"
                  class="inline-flex items-center gap-2 px-8 py-3 min-h-11 rounded-full font-headline-md tracking-tight hover:scale-[1.02] active:scale-95 transition-transform {{ $step === 6 ? 'bg-secondary text-on-secondary' : 'bg-primary text-on-primary' }}">
            <span>{{ $step < 6 ? __('Continue') : __('Submit application') }}</span>
            <span class="material-symbols-outlined">{{ $step < 6 ? 'arrow_forward' : 'check_circle' }}</span>
          </button>
        </div>
      </form>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
@endpush

<x-guest-layout>
  @php
    $showGoogleAuth = false;
    $googleAuthConfigured = is_string(config('services.google.client_id')) && trim((string) config('services.google.client_id')) !== ''
        && is_string(config('services.google.client_secret')) && trim((string) config('services.google.client_secret')) !== ''
        && is_string(config('services.google.redirect')) && trim((string) config('services.google.redirect')) !== '';
    $rememberedGoogleEmail = trim((string) request()->cookie('mt_google_email', ''));
  @endphp

  <div class="w-full">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password') && ! $errors->has('name'))
      <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
        <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="mb-6">
      <h1 class="text-2xl font-bold tracking-tight text-zinc-900">{{ __('Sign in') }}</h1>
      <p class="mt-1 text-sm text-zinc-600">{{ __('Use your account email and password to access the dashboard.') }}</p>
    </div>

    <div class="space-y-4">
      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="_tab" value="login" />
        <div>
          <x-input-label for="login_email" :value="__('Email')" />
          <x-text-input id="login_email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-4" x-data="{ showPassword: false }">
          <x-input-label for="login_password" :value="__('Password')" />
          <div class="relative mt-1">
            <x-text-input id="login_password" class="block w-full pr-10" x-bind:type="showPassword ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password" />
            <button type="button" class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-500 hover:text-gray-700" @click="showPassword = !showPassword">
              <span class="sr-only">{{ __('Toggle visibility') }}</span>
              <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
          </div>
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="block mt-4">
          <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
          </label>
        </div>
        <div class="flex flex-col gap-3 mt-6">
          @if ($showGoogleAuth && $googleAuthConfigured && $rememberedGoogleEmail !== '')
            <a href="{{ route('auth.google.redirect', ['intent' => 'login', 'login_hint' => $rememberedGoogleEmail]) }}" class="inline-flex justify-center items-center gap-2 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-800 hover:bg-indigo-100">
              <span class="truncate">{{ __('Continue with') }} {{ $rememberedGoogleEmail }}</span>
            </a>
          @endif
          @if ($showGoogleAuth && $googleAuthConfigured)
            <a href="{{ route('auth.google.redirect', ['intent' => 'login']) }}" class="inline-flex justify-center items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
              <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
              {{ __('Continue with Google') }}
            </a>
          @endif
          <div class="flex items-center justify-between gap-2">
            @if (Route::has('password.request'))
              <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
            <x-primary-button>{{ __('Log in') }}</x-primary-button>
          </div>
        </div>
      </form>
    </div>

    <div hidden class="space-y-4" aria-hidden="true">
      <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="_tab" value="register" />
        <div>
          <x-input-label for="reg_name" :value="__('Name')" />
          <x-text-input id="reg_name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
          <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div class="mt-4">
          <x-input-label for="reg_email" :value="__('Email')" />
          <x-text-input id="reg_email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-4" x-data="{ showPassword: false }">
          <x-input-label for="reg_password" :value="__('Password')" />
          <div class="relative mt-1">
            <x-text-input id="reg_password" class="block w-full pr-10" x-bind:type="showPassword ? 'text' : 'password'" type="password" name="password" required autocomplete="new-password" />
            <button type="button" class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-500" @click="showPassword = !showPassword"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>
          </div>
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-4" x-data="{ showPassword2: false }">
          <x-input-label for="reg_password_confirmation" :value="__('Confirm Password')" />
          <div class="relative mt-1">
            <x-text-input id="reg_password_confirmation" class="block w-full pr-10" x-bind:type="showPassword2 ? 'text' : 'password'" type="password" name="password_confirmation" required autocomplete="new-password" />
            <button type="button" class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-500" @click="showPassword2 = !showPassword2"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .638C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></button>
          </div>
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <div class="flex flex-col gap-3 mt-6">
          @if ($showGoogleAuth && $googleAuthConfigured && $rememberedGoogleEmail !== '')
            <a href="{{ route('auth.google.redirect', ['intent' => 'register', 'login_hint' => $rememberedGoogleEmail]) }}" class="inline-flex justify-center items-center gap-2 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-800 hover:bg-indigo-100">
              <span class="truncate">{{ __('Continue with') }} {{ $rememberedGoogleEmail }}</span>
            </a>
          @endif
          @if ($showGoogleAuth && $googleAuthConfigured)
            <a href="{{ route('auth.google.redirect', ['intent' => 'register']) }}" class="inline-flex justify-center items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
              <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
              {{ __('Sign up with Google') }}
            </a>
          @endif
          <x-primary-button class="w-full justify-center">{{ __('Continue') }}</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-guest-layout>

<x-app-layout>
  <x-admin.page-header :subtitle="__('Update your name, email address, and password.')" />

  <x-admin.page-content class="space-y-6">
    @if (session('status'))
      <div class="rounded-xl border border-secondary/30 bg-secondary-container/30 px-4 py-3 text-sm text-on-secondary-container">{{ session('status') }}</div>
    @endif

    @if ($errors->any() && ! $errors->hasBag('userDeletion'))
      <div class="rounded-xl border border-error/30 bg-error-container px-4 py-3 text-sm text-on-error-container">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
      <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
      </form>
    @endif

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
      @csrf
      @method('patch')

      <x-admin.panel :title="__('Profile information')" :description="__('Update your account profile and email address.')">
        <div class="pb-admin-form max-w-3xl">
          <div class="pb-field">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
          </div>
          <div class="pb-field">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
              <p class="mt-2 text-sm text-on-surface-variant">
                {{ __('Your email address is unverified.') }}
                <button form="send-verification" type="submit" class="font-semibold text-secondary hover:underline">{{ __('Re-send verification email') }}</button>
              </p>
              @if (session('status') === 'verification-link-sent')
                <p class="mt-2 text-sm font-medium text-secondary">{{ __('A new verification link has been sent.') }}</p>
              @endif
            @endif
          </div>
        </div>
      </x-admin.panel>

      <x-admin.panel :title="__('Password')" :description="__('Leave blank to keep your current password.')">
        <div class="pb-admin-form max-w-3xl">
          <div class="pb-field">
            <label for="current_password">{{ __('Current password') }}</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div class="pb-field">
              <label for="password">{{ __('New password') }}</label>
              <input id="password" name="password" type="password" autocomplete="new-password" />
              <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>
            <div class="pb-field">
              <label for="password_confirmation">{{ __('Confirm new password') }}</label>
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
              <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
            </div>
          </div>
        </div>
      </x-admin.panel>

      <x-admin.button type="submit">{{ __('Save changes') }}</x-admin.button>
    </form>

    @unless ($user->isEditor())
      <x-admin.panel :title="__('Delete account')" :description="__('Permanently remove your account and all associated data.')" variant="danger">
        @include('profile.partials.delete-user-form')
      </x-admin.panel>
    @endunless
  </x-admin.page-content>
</x-app-layout>

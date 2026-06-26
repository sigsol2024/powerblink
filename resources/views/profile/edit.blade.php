<x-app-layout>
  <div class="flex flex-col">
    <x-admin.page-header
      :title="__('My account')"
      :subtitle="__('Update your name, email address, and password.')"
    />

    <x-admin.page-content class="space-y-8">
      @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
      @endif

      @if ($errors->any() && ! $errors->hasBag('userDeletion'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
          <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
          @csrf
        </form>
      @endif

      <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')

        <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
          <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Profile information') }}</h2>
            <p class="mt-1 text-xs text-zinc-500">{{ __("Update your account's profile information and email address.") }}</p>
          </div>
          <div class="space-y-4 px-6 py-5">
            <div>
              <x-input-label for="name" :value="__('Name')" />
              <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
              <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
              <x-input-label for="email" :value="__('Email')" />
              <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
              <x-input-error class="mt-2" :messages="$errors->get('email')" />

              @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-zinc-600">
                  {{ __('Your email address is unverified.') }}
                  <button form="send-verification" class="underline hover:text-zinc-900">{{ __('Click here to re-send the verification email.') }}</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                  <p class="mt-2 text-sm font-medium text-emerald-600">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
              @endif
            </div>
          </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm">
          <div class="border-b border-zinc-100 bg-zinc-50 px-6 py-4">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Password') }}</h2>
            <p class="mt-1 text-xs text-zinc-500">{{ __('Leave blank to keep your current password.') }}</p>
          </div>
          <div class="space-y-4 px-6 py-5">
            <div>
              <x-input-label for="current_password" :value="__('Current password')" />
              <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
              <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <x-input-label for="password" :value="__('New password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
              </div>

              <div>
                <x-input-label for="password_confirmation" :value="__('Confirm new password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
              </div>
            </div>
          </div>
        </section>

        <div class="flex items-center gap-3">
          <x-admin.button type="submit">{{ __('Save changes') }}</x-admin.button>
        </div>
      </form>

      @unless ($user->isEditor())
      <section class="overflow-hidden rounded-xl border border-red-200 bg-white shadow-sm">
        <div class="border-b border-red-100 bg-red-50 px-6 py-4">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-red-800">{{ __('Delete account') }}</h2>
          <p class="mt-1 text-xs text-red-700">{{ __('Permanently remove your account and all associated data.') }}</p>
        </div>
        <div class="px-6 py-5">
          @include('profile.partials.delete-user-form')
        </div>
      </section>
      @endunless
    </x-admin.page-content>
  </div>
</x-app-layout>

@php
  /** @var \App\Models\VendorProfile $profile */
@endphp
<x-app-layout>
  <x-slot name="header">
    <div>
      <p class="admin-page-eyebrow">{{ __('Account') }}</p>
      <h2 class="admin-page-title truncate">{{ __('Dealer contact') }}</h2>
    </div>
  </x-slot>

  <div class="mx-auto w-full max-w-6xl space-y-6">
    @if (session('status'))
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="post" action="{{ route('dashboard.vendor-settings.update') }}" class="space-y-8">
      @csrf
      @method('PUT')

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Public contact on listings') }}</h2>
        <p class="mt-1 text-xs text-zinc-500">{{ __('When enabled, buyers see this information on your vehicle detail page instead of only the per-listing fields.') }}</p>
        <div class="mt-4 space-y-4">
          <label class="flex items-center gap-2">
            <input type="checkbox" name="show_on_listings" value="1" @checked(old('show_on_listings', $profile->show_on_listings)) class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
            <span class="text-sm text-zinc-800">{{ __('Use dealer profile on listings') }}</span>
          </label>
          <div>
            <label for="business_name" class="block text-sm font-medium text-zinc-700">{{ __('Business / display name') }}</label>
            <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $profile->business_name) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="public_email" class="block text-sm font-medium text-zinc-700">{{ __('Public email') }}</label>
              <input type="email" name="public_email" id="public_email" value="{{ old('public_email', $profile->public_email) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
            </div>
            <div>
              <label for="public_phone" class="block text-sm font-medium text-zinc-700">{{ __('Public phone') }}</label>
              <input type="text" name="public_phone" id="public_phone" value="{{ old('public_phone', $profile->public_phone) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
            </div>
          </div>
          <div>
            <label for="public_address" class="block text-sm font-medium text-zinc-700">{{ __('Public address') }}</label>
            <input type="text" name="public_address" id="public_address" value="{{ old('public_address', $profile->public_address) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
          </div>
          <div>
            <label for="map_location" class="block text-sm font-medium text-zinc-700">{{ __('Map search text') }}</label>
            <input type="text" name="map_location" id="map_location" value="{{ old('map_location', $profile->map_location) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="website" class="block text-sm font-medium text-zinc-700">{{ __('Website') }}</label>
              <input type="text" name="website" id="website" value="{{ old('website', $profile->website) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
            </div>
            <div>
              <label for="whatsapp" class="block text-sm font-medium text-zinc-700">{{ __('WhatsApp') }}</label>
              <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $profile->whatsapp) }}" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm sm:text-sm" />
            </div>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-700">{{ __('Email login code (OTP)') }}</h2>
        <p class="mt-1 text-xs text-zinc-500">{{ __('After you enable this, you will receive a code by email each time you sign in with your password.') }}</p>
        <div class="mt-4 space-y-4">
          <label class="flex items-center gap-2">
            <input type="checkbox" name="email_login_otp_enabled" value="1" @checked(old('email_login_otp_enabled', auth()->user()->email_login_otp_enabled)) class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
            <span class="text-sm text-zinc-800">{{ __('Require email code on login') }}</span>
          </label>
          <x-input-error :messages="$errors->get('email_login_otp_enabled')" class="mt-1" />
          <div>
            <label for="otp_code" class="block text-sm font-medium text-zinc-700">{{ __('Confirmation code (when enabling)') }}</label>
            <input type="text" name="otp_code" id="otp_code" maxlength="6" inputmode="numeric" autocomplete="one-time-code" class="mt-1 block w-full max-w-xs rounded-md border-zinc-300 font-mono tracking-widest shadow-sm sm:text-sm" placeholder="000000" />
            <p class="mt-1 text-xs text-zinc-500">{{ __('Save once to receive a code by email, then enter it here and save again to confirm.') }}</p>
            <x-input-error :messages="$errors->get('otp_code')" class="mt-1" />
          </div>
        </div>
      </section>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50">{{ __('Cancel') }}</a>
        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">{{ __('Save') }}</button>
      </div>
    </form>
  </div>
</x-app-layout>

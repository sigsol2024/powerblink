<x-app-layout>
  <x-slot name="header">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-xl font-semibold text-slate-800">{{ __('Welcome') }}</h2>
    </div>
  </x-slot>

  <div class="w-full">
    <div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <p class="text-sm font-semibold text-slate-700">{{ __('Your account is ready.') }}</p>
      @if (!empty($name))
        <p class="mt-2 text-2xl font-black tracking-tight text-slate-900">{{ __('Hi') }}, {{ $name }}.</p>
      @endif
      @if (!empty($email))
        <p class="mt-2 text-sm text-slate-600">{{ __('Signed in as') }} <span class="font-semibold text-slate-800">{{ $email }}</span>.</p>
      @endif

      <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a
          href="{{ route('inventory.index') }}"
          class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
        >
          {{ __('Browse inventory') }}
        </a>
        <a
          href="{{ route('dashboard') }}"
          class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-sm hover:bg-amber-400"
        >
          {{ __('Go to dashboard') }}
        </a>
      </div>
    </div>
  </div>
</x-app-layout>


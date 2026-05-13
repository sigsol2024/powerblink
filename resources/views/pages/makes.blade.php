@extends('layouts.site')

@section('content')
  <div class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-[1280px] px-4 py-10 sm:px-6 lg:px-8">
      <nav class="text-xs font-semibold text-zinc-500">
        <a href="{{ route('home') }}" class="hover:text-[#1280DF]">{{ __('Home') }}</a>
        <span class="mx-1.5">/</span>
        <span class="text-zinc-800">{{ __('Search by make') }}</span>
      </nav>
      <h1 class="mt-3 font-headline text-3xl font-black uppercase tracking-tight text-zinc-900 sm:text-4xl">{{ __('Search by make') }}</h1>
      <p class="mt-2 max-w-2xl text-sm text-zinc-600">{{ __('Choose a make to search inventory.') }}</p>
    </div>
  </div>

  <div class="mx-auto max-w-[1280px] px-4 py-10 sm:px-6 lg:px-8">
    @if ($makes->isEmpty())
      <p class="text-sm text-zinc-600">{{ __('No makes are available yet.') }}</p>
    @else
      <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
        @foreach ($makes as $makeOpt)
          <li>
            <a href="{{ route('inventory.index', ['make_listing_option_id' => $makeOpt->id]) }}" class="group flex h-full flex-col items-center gap-2 rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-[#1280DF]/40 hover:shadow-md">
              @if (! empty($makeOpt->logo_path))
                <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg bg-slate-50 ring-1 ring-slate-200">
                  <img src="{{ \App\Support\VehicleImageUrl::url($makeOpt->logo_path) }}" alt="" class="h-full w-full object-contain p-1" />
                </span>
              @elseif (! empty(trim((string) ($makeOpt->flag_emoji ?? ''))))
                <span class="flex h-16 w-16 items-center justify-center text-3xl leading-none" style="font-family: 'Segoe UI Emoji','Apple Color Emoji','Noto Color Emoji',sans-serif" aria-hidden="true">{{ trim((string) $makeOpt->flag_emoji) }}</span>
              @else
                <span class="flex h-16 w-16 items-center justify-center rounded-lg bg-slate-200 text-sm font-black text-zinc-700">{{ strtoupper(\Illuminate\Support\Str::substr($makeOpt->value, 0, 2)) }}</span>
              @endif
              <span class="line-clamp-2 text-xs font-bold uppercase leading-snug text-zinc-800 group-hover:text-[#1280DF]">{{ $makeOpt->value }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
@endsection

@php
  $isMake = $category->slug === 'make';
  $isCountry = $category->slug === 'country';
  $batchFormId = 'listing-options-batch-form';
@endphp
<x-app-layout>
  <x-slot name="header">
    <div class="admin-page-header flex flex-col gap-2 sm:gap-3">
      <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-zinc-500">{{ __('Catalog') }}</p>
      <h2 class="admin-page-title">{{ __('Listing options') }}: {{ $category->label }}</h2>
      <div class="admin-header-actions flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
        <a href="{{ route('admin.listing-options.index') }}" class="admin-btn">{{ __('All categories') }}</a>
      </div>
    </div>
  </x-slot>

  <div
    class="space-y-6"
    x-data="{ addOpen: false, openId: null, toggleOpen(id) { this.openId = this.openId === id ? null : id; } }"
    @keydown.escape.window="addOpen = false"
  >
    @if ($errors->any())
      <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <ul class="list-disc space-y-1 pl-5">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('status'))
      <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
      <p class="max-w-2xl text-sm leading-relaxed text-zinc-600">
        {{ __('Edit values, display order, and visibility. Use Save changes at the bottom to apply all edits at once.') }}
      </p>
      <button
        type="button"
        class="admin-btn-primary !border-zinc-900 !bg-zinc-900 !text-white shadow-sm transition hover:bg-zinc-800"
        @click="addOpen = true"
      >
        {{ __('Add option') }}
      </button>
    </div>

    {{-- Add option modal --}}
    <div
      x-show="addOpen"
      x-cloak
      class="fixed inset-0 z-[200] flex items-center justify-center p-4"
      x-transition.opacity.duration.200ms
    >
      <div class="absolute inset-0 bg-black/55 backdrop-blur-[1px]" @click="addOpen = false" aria-hidden="true"></div>
      <div
        class="relative z-10 w-full max-w-lg rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="add-option-title"
        @click.stop
      >
        <div class="flex items-start justify-between gap-3">
          <h3 id="add-option-title" class="text-lg font-bold text-zinc-900">{{ __('Add option') }}</h3>
          <button type="button" class="rounded-lg p-1 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800" @click="addOpen = false" aria-label="{{ __('Close') }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form method="post" action="{{ route('admin.listing-options.store', $category) }}" class="mt-5 space-y-4">
          @csrf
          <div>
            <x-input-label for="modal_value" :value="__('Value')" />
            <x-text-input id="modal_value" name="value" type="text" class="mt-1 block w-full" required value="{{ old('value') }}" />
          </div>
          @if ($category->slug === 'model')
            <div>
              <x-input-label for="modal_parent_id" :value="__('Parent make')" />
              <select id="modal_parent_id" name="parent_id" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500" required>
                <option value="">{{ __('Select make') }}</option>
                @foreach ($makeOptions as $m)
                  <option value="{{ $m->id }}" @selected((string) old('parent_id') === (string) $m->id)>{{ $m->value }}</option>
                @endforeach
              </select>
            </div>
          @endif
          @if ($isMake)
            <div>
              <x-input-label for="modal_make_logo_path" :value="__('Logo (optional)')" />
              <input type="hidden" name="logo_path" id="modal_make_logo_path" value="{{ old('logo_path') }}" />
              @php $modalLogoPath = trim((string) old('logo_path')); @endphp
              <div class="mt-2 flex flex-wrap items-center gap-2">
                <div data-mt-logo-preview-wrap="modal_make_logo_path" class="relative inline-block {{ $modalLogoPath !== '' ? '' : 'hidden' }}">
                  <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-zinc-50">
                    <img data-mt-logo-preview="modal_make_logo_path" src="{{ $modalLogoPath !== '' ? \App\Support\VehicleImageUrl::url($modalLogoPath) : '' }}" alt="" class="h-full w-full object-contain p-0.5" />
                  </div>
                  <button type="button" data-mt-logo-clear="modal_make_logo_path" class="absolute -right-1 -top-1 inline-flex h-6 w-6 items-center justify-center rounded-full border border-zinc-200 bg-white text-sm font-bold leading-none text-zinc-600 shadow-sm transition hover:bg-red-50 hover:text-red-700" title="{{ __('Remove logo') }}" aria-label="{{ __('Remove logo') }}">×</button>
                </div>
                <button type="button" class="js-mt-media-pick inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:border-amber-300/60 hover:bg-amber-50/50 {{ $modalLogoPath !== '' ? 'hidden' : '' }}" data-mt-media-target="modal_make_logo_path" title="{{ __('Add logo image') }}" aria-label="{{ __('Add logo image') }}">
                  <svg class="h-5 w-5 shrink-0 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                  <span class="sr-only">{{ __('Add logo') }}</span>
                </button>
              </div>
            </div>
          @endif
          <div class="flex items-center gap-2">
            <input id="modal_is_active" name="is_active" type="checkbox" value="1" class="rounded border-zinc-300 text-amber-600 focus:ring-amber-500" @checked(old('is_active', true)) />
            <x-input-label for="modal_is_active" :value="__('Active')" class="!mb-0" />
          </div>
          <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4">
            <button type="button" class="rounded-lg border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-50" @click="addOpen = false">{{ __('Cancel') }}</button>
            <x-primary-button type="submit">{{ __('Add') }}</x-primary-button>
          </div>
        </form>
      </div>
    </div>

    @if ($options->isEmpty())
      <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/60 px-6 py-10 text-center text-sm text-zinc-600">
        {{ __('No options yet. Use Add option to create the first value.') }}
      </div>
    @else
      <form id="{{ $batchFormId }}" method="post" action="{{ route('admin.listing-options.batch-update', $category) }}">
        @csrf
        @method('PUT')

      <div class="overflow-hidden rounded-2xl border border-zinc-200/90 bg-white shadow-sm ring-1 ring-black/[0.02]">
        <div class="hidden lg:block overflow-x-auto">
          <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-zinc-50">
              <tr>
                @if ($isMake)
                  <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Logo') }}</th>
                @endif
                @if ($isCountry)
                  <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Flag') }}</th>
                @endif
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Value') }}</th>
                @if ($category->slug === 'model')
                  <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Make') }}</th>
                @endif
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">
                  <span class="block">{{ __('Display order') }}</span>
                  <span class="mt-0.5 block font-normal normal-case text-zinc-500">{{ __('Lower numbers appear first in dropdowns within the same group.') }}</span>
                </th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Active') }}</th>
                <th class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Reorder') }}</th>
                <th class="px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-zinc-500">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white">
              @foreach ($options as $option)
                <tr class="align-top">
                  @if ($isMake)
                    <td class="px-4 py-3">
                      <input type="hidden" name="logo_paths[{{ $option->id }}]" id="make_logo_path_{{ $option->id }}" value="{{ old('logo_paths.'.$option->id, $option->logo_path) }}" />
                      @php $rowLogo = trim((string) old('logo_paths.'.$option->id, $option->logo_path)); @endphp
                      <div data-mt-logo-preview-wrap="make_logo_path_{{ $option->id }}" class="relative inline-block {{ $rowLogo !== '' ? '' : 'hidden' }}">
                        <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-lg border border-zinc-200 bg-zinc-50">
                          <img data-mt-logo-preview="make_logo_path_{{ $option->id }}" src="{{ $rowLogo !== '' ? \App\Support\VehicleImageUrl::url($rowLogo) : '' }}" alt="" class="h-full w-full object-contain p-0.5" />
                        </div>
                        <button type="button" data-mt-logo-clear="make_logo_path_{{ $option->id }}" class="absolute -right-1 -top-1 inline-flex h-5 w-5 items-center justify-center rounded-full border border-zinc-200 bg-white text-xs font-bold leading-none text-zinc-600 shadow-sm transition hover:bg-red-50 hover:text-red-700" title="{{ __('Remove logo') }}" aria-label="{{ __('Remove logo') }}">×</button>
                      </div>
                      <button type="button" class="js-mt-media-pick mt-2 inline-flex w-full max-w-[10rem] items-center justify-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-2 py-2 text-zinc-600 shadow-sm transition hover:border-amber-300/60 hover:bg-amber-50/40 {{ $rowLogo !== '' ? 'hidden' : '' }}" data-mt-media-target="make_logo_path_{{ $option->id }}" title="{{ __('Add logo image') }}" aria-label="{{ __('Add logo image') }}">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                        <span class="sr-only">{{ __('Add logo') }}</span>
                      </button>
                    </td>
                  @endif
                  @if ($isCountry)
                    <td class="px-4 py-3 text-2xl leading-none" title="{{ $option->value }}">{{ $option->flag_emoji ?? '—' }}</td>
                  @endif
                  <td class="px-4 py-3">
                    <input
                      type="text"
                      name="options[{{ $option->id }}][value]"
                      value="{{ old('options.'.$option->id.'.value', $option->value) }}"
                      class="block w-full min-w-[12rem] max-w-xs rounded-lg border-zinc-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                      required
                    />
                  </td>
                  @if ($category->slug === 'model')
                    <td class="px-4 py-3 text-zinc-700">{{ $option->parent?->value ?? '—' }}</td>
                  @endif
                  <td class="px-4 py-3">
                    <input
                      type="number"
                      name="options[{{ $option->id }}][sort_order]"
                      value="{{ old('options.'.$option->id.'.sort_order', $option->sort_order) }}"
                      min="0"
                      max="65535"
                      class="w-24 rounded-lg border-zinc-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    />
                  </td>
                  <td class="px-4 py-3">
                    <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
                      <input type="checkbox" name="options[{{ $option->id }}][is_active]" value="1" class="rounded border-zinc-300 text-amber-600 focus:ring-amber-500" @checked(old('options.'.$option->id.'.is_active', $option->is_active)) />
                      <span>{{ __('Visible') }}</span>
                    </label>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div class="inline-flex flex-col gap-1">
                      <button type="submit" form="move-up-{{ $option->id }}" class="text-xs font-semibold text-zinc-600 hover:text-amber-700">{{ __('Up') }}</button>
                      <button type="submit" form="move-down-{{ $option->id }}" class="text-xs font-semibold text-zinc-600 hover:text-amber-700">{{ __('Down') }}</button>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <x-danger-button type="submit" form="delete-option-{{ $option->id }}" class="!py-1.5 !text-xs">{{ __('Delete') }}</x-danger-button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>


        <div class="lg:hidden space-y-3 p-4">
          @foreach ($options as $option)
            <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
              <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen({{ $option->id }})" :aria-expanded="openId === {{ $option->id }} ? 'true' : 'false'">
                <span class="min-w-0 flex-1 truncate font-semibold text-zinc-900">{{ old('options.' . $option->id . '.value', $option->value) }}</span>
                <svg class="h-5 w-5 shrink-0 text-zinc-400 transition-transform" :class="openId === {{ $option->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div x-show="openId === {{ $option->id }}" x-cloak class="space-y-3 border-t border-zinc-100 bg-zinc-50 px-4 py-4 text-sm">
                <input type="text" name="options[{{ $option->id }}][value]" value="{{ old('options.' . $option->id . '.value', $option->value) }}" class="block w-full rounded-lg border-zinc-300 text-sm" required />
                <input type="number" name="options[{{ $option->id }}][sort_order]" value="{{ old('options.' . $option->id . '.sort_order', $option->sort_order) }}" min="0" max="65535" class="w-full rounded-lg border-zinc-300 text-sm" />
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="options[{{ $option->id }}][is_active]" value="1" class="rounded border-zinc-300 text-amber-600" @checked(old('options.' . $option->id . '.is_active', $option->is_active)) /><span>{{ __('Visible') }}</span></label>
                <div class="flex flex-col gap-2">
                  <button type="submit" form="move-up-{{ $option->id }}" class="admin-btn">{{ __('Up') }}</button>
                  <button type="submit" form="move-down-{{ $option->id }}" class="admin-btn">{{ __('Down') }}</button>
                  <button type="submit" form="delete-option-{{ $option->id }}" class="admin-btn text-rose-700">{{ __('Delete') }}</button>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      <div class="mt-4 flex flex-col items-stretch gap-2 border-t border-zinc-200/90 pt-4 sm:flex-row sm:items-center sm:justify-between">
        @if ($isMake)
          <p class="text-xs text-zinc-500">{{ __('Logos are chosen from the media library. Saving applies the path for each make you changed.') }}</p>
        @else
          <span></span>
        @endif
        <x-primary-button type="submit" class="sm:ml-auto">{{ __('Save changes') }}</x-primary-button>
      </div>
      </form>

      @foreach ($options as $option)
        <form id="move-up-{{ $option->id }}" method="post" action="{{ route('admin.listing-options.move', [$category, $option]) }}" class="hidden" aria-hidden="true">
          @csrf
          <input type="hidden" name="direction" value="up" />
        </form>
        <form id="move-down-{{ $option->id }}" method="post" action="{{ route('admin.listing-options.move', [$category, $option]) }}" class="hidden" aria-hidden="true">
          @csrf
          <input type="hidden" name="direction" value="down" />
        </form>
        <form
          id="delete-option-{{ $option->id }}"
          method="post"
          action="{{ route('admin.listing-options.destroy', [$category, $option]) }}"
          class="hidden"
          aria-hidden="true"
          onsubmit="return confirm('{{ addslashes(__('Delete this option?')) }}')"
        >
          @csrf
          @method('DELETE')
        </form>
      @endforeach
    @endif
  </div>
</x-app-layout>

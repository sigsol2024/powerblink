<x-app-layout>
    <x-slot name="header">
    <div>
      <p class="admin-page-eyebrow">{{ __('Catalog') }}</p>
      <h2 class="admin-page-title truncate">{{ __('Listing options') }}</h2>
    </div>
  </x-slot>

  <div class="space-y-6">
    <div class="overflow-hidden rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm ring-1 ring-black/[0.02] sm:p-8">
      <h2 class="text-base font-semibold text-zinc-900">{{ __('Controlled values') }}</h2>
      <p class="mt-2 max-w-3xl text-sm leading-relaxed text-zinc-600">
        {{ __('Each category defines the dropdown values used on listing forms (make, model, condition, and more). Open a category to add, reorder, or deactivate options.') }}
      </p>

      @if ($categories->isEmpty())
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
          {{ __('No categories found. Run migrations to create listing option tables.') }}
        </div>
      @else
        <ul class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          @foreach ($categories as $cat)
            <li class="flex min-h-[7.5rem] flex-col rounded-xl border border-zinc-200/90 bg-zinc-50/50 p-4 shadow-sm ring-1 ring-black/[0.02] transition hover:border-amber-200/80 hover:bg-white">
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-baseline gap-2">
                  <span class="text-base font-semibold text-zinc-900">{{ $cat->label }}</span>
                  <span class="rounded-md bg-zinc-200/90 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-zinc-600">{{ $cat->slug }}</span>
                  @php $count = (int) ($optionCounts[$cat->id] ?? 0); @endphp
                  @if ($count > 0)
                    <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold text-zinc-600">{{ trans_choice(':count option|:count options', $count, ['count' => number_format($count)]) }}</span>
                  @endif
                </div>
                @if ($count > $optionsPerPage)
                  <p class="mt-2 text-xs text-zinc-500">{{ __('Large list — opens with pages of :size.', ['size' => $optionsPerPage]) }}</p>
                @endif
              </div>
              <div class="mt-4 flex shrink-0 justify-end border-t border-zinc-200/80 pt-3">
                <a
                  href="{{ route('admin.listing-options.show', $cat) }}"
                  class="inline-flex items-center rounded-lg bg-zinc-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-zinc-800"
                >
                  {{ __('Manage') }}
                  <span class="ml-1 opacity-70" aria-hidden="true">→</span>
                </a>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</x-app-layout>

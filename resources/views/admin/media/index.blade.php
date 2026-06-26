@php
  $allItemIds = array_map(static fn ($i) => (int) ($i['id'] ?? 0), $items);
@endphp

<x-app-layout>
  <div
    class="flex flex-col"
    x-data="{
      selected: [],
      allIds: @js($allItemIds),
      openId: null,
      toggleOpen(id) { this.openId = this.openId === id ? null : id; },
      view: (localStorage.getItem('admin_media_view') || 'grid'),
      toggleSelectAll() {
        this.selected = (this.selected.length === this.allIds.length) ? [] : [...this.allIds];
      },
      setView(mode) {
        this.view = mode;
        localStorage.setItem('admin_media_view', mode);
      },
      isSelected(id) {
        return this.selected.includes(id);
      }
    }"
  >
    <x-admin.page-header :subtitle="__('Upload and reuse images across pages and product editors.')" />

    <x-admin.page-content class="space-y-6">
      @include('admin.partials.flash')

      <x-admin.card>
        <form method="get" action="{{ route('admin.media.index') }}" class="flex flex-wrap items-center gap-3">
          <div class="pb-field min-w-[16rem] flex-1 !mb-0">
            <label for="media-search-q" class="sr-only">{{ __('Search') }}</label>
            <input type="search" id="media-search-q" name="q" value="{{ $query }}" placeholder="{{ __('Search media file name...') }}" />
          </div>
          <x-admin.button type="submit" variant="secondary">{{ __('Search') }}</x-admin.button>
          @if ($query !== '')
            <x-admin.button variant="ghost" :href="route('admin.media.index')">{{ __('Clear') }}</x-admin.button>
          @endif
          <div class="ml-auto inline-flex overflow-hidden rounded-xl border border-outline-variant/60 bg-surface">
            <button type="button" @click="setView('grid')" :class="view === 'grid' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low'" class="px-3 py-2 text-xs font-semibold transition-colors">{{ __('Grid') }}</button>
            <button type="button" @click="setView('list')" :class="view === 'list' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low'" class="px-3 py-2 text-xs font-semibold transition-colors">{{ __('List') }}</button>
          </div>
        </form>
      </x-admin.card>

      <x-admin.card>
        <form method="post" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
          @csrf
          <div class="pb-field flex-1 min-w-[12rem] !mb-0">
            <label for="media-upload-files">{{ __('Upload images') }}</label>
            <input type="file" id="media-upload-files" name="files[]" accept="image/jpeg,image/jpg,image/png,image/webp" multiple required />
          </div>
          <x-admin.button type="submit" class="self-end">{{ __('Upload') }}</x-admin.button>
        </form>
      </x-admin.card>

      <x-admin.card>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
          <p class="text-sm text-on-surface-variant">{{ __('All uploaded images used by editors and pages.') }}</p>
          <div class="flex flex-wrap items-center gap-2">
            <x-admin.button type="button" variant="secondary" @click="toggleSelectAll()">
              <span x-text="selected.length === allIds.length ? '{{ __('Unselect all') }}' : '{{ __('Select all') }}'"></span>
            </x-admin.button>
            <form method="post" action="{{ route('admin.media.bulk-destroy') }}" x-show="selected.length > 0" x-cloak @submit="if (!confirm('{{ __('Delete selected media files? This cannot be undone.') }}')) $event.preventDefault();">
              @csrf
              <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
              </template>
              <x-admin.button type="submit" variant="danger">
                {{ __('Delete selected') }} (<span x-text="selected.length"></span>)
              </x-admin.button>
            </form>
            <button
              type="button"
              x-show="selected.length === 1"
              x-cloak
              @click="navigator.clipboard?.writeText($el.dataset.url || '')"
              :data-url="(() => { const one = selected[0]; const item = @js($items).find(i => Number(i.id) === Number(one)); return item?.url || ''; })()"
              class="inline-flex items-center rounded-xl border border-outline-variant/60 px-3 py-2 text-xs font-semibold text-on-surface-variant hover:bg-surface-container-low transition-colors"
            >
              {{ __('Copy selected URL') }}
            </button>
          </div>
        </div>

        <div x-show="view === 'grid'" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
          @forelse ($items as $item)
            <div class="overflow-hidden rounded-xl border border-outline-variant/60 bg-surface">
              <div class="flex items-center justify-between border-b border-outline-variant/40 px-2 py-1.5">
                <label class="inline-flex items-center gap-1 text-[11px] text-on-surface-variant normal-case tracking-normal font-normal">
                  <input type="checkbox" class="rounded border-outline-variant text-secondary" :value="{{ (int) $item['id'] }}" x-model.number="selected">
                  {{ __('Select') }}
                </label>
                <span class="text-[11px] text-on-surface-variant">{{ number_format($item['size'] / 1024, 1) }} KB</span>
              </div>
              <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="h-24 w-full object-cover" />
              <div class="space-y-2 p-2">
                <p class="truncate text-xs font-medium text-on-surface" title="{{ $item['name'] }}">{{ $item['name'] }}</p>
                <p class="truncate rounded-lg bg-surface-container-low px-1.5 py-1 text-[11px] text-on-surface-variant" title="{{ $item['url'] }}">{{ $item['url'] }}</p>
                <div class="flex items-center gap-1.5">
                  <button
                    type="button"
                    class="rounded-lg border border-outline-variant/60 px-2 py-1 text-[11px] font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors"
                    data-copy-url="{{ $item['url'] }}"
                  >{{ __('Copy URL') }}</button>
                  <form method="post" action="{{ route('admin.media.destroy.post', ['media' => $item['id']]) }}" onsubmit="return confirm('{{ __('Delete this image? This cannot be undone.') }}');">
                    @csrf
                    <button type="submit" class="rounded-lg border border-error/40 px-2 py-1 text-[11px] font-medium text-error hover:bg-error-container/30 transition-colors">{{ __('Delete') }}</button>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <x-admin.empty-state class="col-span-full">{{ __('No media files found.') }}</x-admin.empty-state>
          @endforelse
        </div>

        <div x-show="view === 'list'" x-cloak class="lg:hidden space-y-3">
          @foreach ($items as $item)
            <article class="overflow-hidden rounded-xl border border-outline-variant/60 bg-surface">
              <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen({{ (int) $item['id'] }})" :aria-expanded="openId === {{ (int) $item['id'] }} ? 'true' : 'false'">
                <span class="min-w-0 flex-1 truncate font-medium text-on-surface">{{ $item['name'] }}</span>
                <x-icon name="expand_more" class="h-5 w-5 shrink-0 text-on-surface-variant transition-transform" :class="openId === {{ (int) $item['id'] }} ? 'rotate-180' : ''" />
              </button>
              <div x-show="openId === {{ (int) $item['id'] }}" x-cloak class="border-t border-outline-variant/40 bg-surface-container-low px-4 py-4 text-sm">
                <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="mb-3 h-24 w-full rounded-lg border border-outline-variant/60 object-cover" />
                <p class="text-xs text-on-surface-variant">{{ number_format($item['size'] / 1024, 1) }} KB</p>
                <p class="mt-1 truncate text-xs text-on-surface-variant" title="{{ $item['path'] }}">{{ $item['path'] }}</p>
                <label class="mt-3 inline-flex items-center gap-2 text-xs text-on-surface-variant normal-case tracking-normal font-normal">
                  <input type="checkbox" class="rounded border-outline-variant text-secondary" :value="{{ (int) $item['id'] }}" x-model.number="selected">
                  {{ __('Select') }}
                </label>
                <div class="mt-3 flex flex-col gap-2">
                  <button type="button" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/60 px-3 py-2 text-xs font-semibold text-on-surface-variant hover:bg-surface-container-low" data-copy-url="{{ $item['url'] }}">{{ __('Copy URL') }}</button>
                  <form method="post" action="{{ route('admin.media.destroy.post', ['media' => $item['id']]) }}" onsubmit="return confirm('{{ __('Delete this image? This cannot be undone.') }}');">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl border border-error/40 px-3 py-2 text-xs font-semibold text-error hover:bg-error-container/30">{{ __('Delete') }}</button>
                  </form>
                </div>
              </div>
            </article>
          @endforeach
        </div>

        <div x-show="view === 'list'" x-cloak class="hidden lg:block overflow-x-auto rounded-xl border border-outline-variant/60">
          <table class="pb-admin-table min-w-full text-sm">
            <thead>
              <tr>
                <th>{{ __('Pick') }}</th>
                <th>{{ __('Preview') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Size') }}</th>
                <th>{{ __('Path') }}</th>
                <th class="text-right">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($items as $item)
                <tr>
                  <td>
                    <input type="checkbox" class="rounded border-outline-variant text-secondary" :value="{{ (int) $item['id'] }}" x-model.number="selected">
                  </td>
                  <td>
                    <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="h-12 w-16 rounded-lg border border-outline-variant/60 object-cover" />
                  </td>
                  <td class="font-medium">{{ $item['name'] }}</td>
                  <td class="text-on-surface-variant">{{ number_format($item['size'] / 1024, 1) }} KB</td>
                  <td class="max-w-sm truncate text-xs text-on-surface-variant" title="{{ $item['path'] }}">{{ $item['path'] }}</td>
                  <td class="text-right">
                    <div class="flex justify-end gap-2">
                      <button type="button" class="rounded-lg border border-outline-variant/60 px-2 py-1 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low" data-copy-url="{{ $item['url'] }}">{{ __('Copy URL') }}</button>
                      <form method="post" action="{{ route('admin.media.destroy.post', ['media' => $item['id']]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this image? This cannot be undone.') }}');">
                        @csrf
                        <button type="submit" class="rounded-lg border border-error/40 px-2 py-1 text-xs font-medium text-error hover:bg-error-container/30">{{ __('Delete') }}</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </x-admin.card>
    </x-admin.page-content>
  </div>
</x-app-layout>

@push('body-end')
  <script>
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-copy-url]');
      if (!btn) return;
      const url = btn.getAttribute('data-copy-url') || '';
      if (!url) return;
      try {
        await navigator.clipboard.writeText(url);
        const old = btn.textContent;
        btn.textContent = '{{ __('Copied') }}';
        setTimeout(() => { btn.textContent = old; }, 1200);
      } catch (_) {
        // ignore clipboard failures
      }
    });
  </script>
@endpush

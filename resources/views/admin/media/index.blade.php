@php
  $allItemIds = array_map(static fn ($i) => (int) ($i['id'] ?? 0), $items);
@endphp

<x-app-layout>
  <x-slot name="header">
    <h2 class="admin-page-title truncate">{{ __('Media Library') }}</h2>
  </x-slot>

  <div
    class="w-full space-y-6"
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
    @if (session('status'))
      <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('status') }}
      </div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow-sm sm:rounded-lg">
      <form method="get" action="{{ route('admin.media.index') }}" class="flex flex-wrap items-center gap-2">
        <input type="search" name="q" value="{{ $query }}" class="min-w-[16rem] flex-1 rounded border-gray-300" placeholder="Search media file name..." />
        <button type="submit" class="admin-btn-primary">Search</button>
        @if ($query !== '')
          <a href="{{ route('admin.media.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Clear</a>
        @endif
        <div class="ml-auto inline-flex overflow-hidden rounded border border-gray-300 bg-white">
          <button type="button" @click="setView('grid')" :class="view === 'grid' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50'" class="px-3 py-2 text-xs font-semibold">Grid</button>
          <button type="button" @click="setView('list')" :class="view === 'list' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50'" class="px-3 py-2 text-xs font-semibold">List</button>
        </div>
      </form>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm sm:rounded-lg">
      <form method="post" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
        @csrf
        <input type="file" name="files[]" accept="image/jpeg,image/jpg,image/png,image/webp" class="block w-full max-w-lg text-sm text-gray-700" multiple required />
        <button type="submit" class="admin-btn-primary whitespace-nowrap">Upload</button>
      </form>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm sm:rounded-lg">
      <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-gray-600">All uploaded images used by editors and pages.</p>
        <div class="flex items-center gap-2">
          <button type="button" @click="toggleSelectAll()" class="rounded border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
            <span x-text="selected.length === allIds.length ? 'Unselect all' : 'Select all'"></span>
          </button>
          <form method="post" action="{{ route('admin.media.bulk-destroy') }}" x-show="selected.length > 0" x-cloak @submit="if (!confirm('Delete selected media files? This cannot be undone.')) $event.preventDefault();">
            @csrf
            <template x-for="id in selected" :key="id">
              <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit" class="rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
              Delete selected (<span x-text="selected.length"></span>)
            </button>
          </form>
          <button
            type="button"
            x-show="selected.length === 1"
            x-cloak
            @click="navigator.clipboard?.writeText($el.dataset.url || '')"
            :data-url="(() => { const one = selected[0]; const item = @js($items).find(i => Number(i.id) === Number(one)); return item?.url || ''; })()"
            class="rounded border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50"
          >
            Copy selected URL
          </button>
        </div>
      </div>

      <div x-show="view === 'grid'" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
        @forelse ($items as $item)
          <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 px-2 py-1.5">
              <label class="inline-flex items-center gap-1 text-[11px] text-gray-600">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600" :value="{{ (int) $item['id'] }}" x-model.number="selected">
                Select
              </label>
              <span class="text-[11px] text-gray-500">{{ number_format($item['size'] / 1024, 1) }} KB</span>
            </div>
            <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="h-24 w-full object-cover" />
            <div class="space-y-2 p-2">
              <p class="truncate text-xs text-gray-800" title="{{ $item['name'] }}">{{ $item['name'] }}</p>
              <p class="truncate rounded bg-gray-50 px-1.5 py-1 text-[11px] text-gray-600" title="{{ $item['url'] }}">{{ $item['url'] }}</p>
              <div class="flex items-center gap-1.5">
                <button
                  type="button"
                  class="rounded border border-gray-300 px-2 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50"
                  data-copy-url="{{ $item['url'] }}"
                >Copy URL</button>
                <form method="post" action="{{ route('admin.media.destroy.post', ['media' => $item['id']]) }}" onsubmit="return confirm('Delete this image? This cannot be undone.');">
                  @csrf
                  <button type="submit" class="rounded border border-red-300 px-2 py-1 text-[11px] font-medium text-red-700 hover:bg-red-50">Delete</button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <p class="col-span-full text-sm text-gray-500">No media files found.</p>
        @endforelse
      </div>

            <div x-show="view === 'list'" x-cloak class="lg:hidden space-y-3">
        @foreach ($items as $item)
          <article class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <button type="button" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left" @click="toggleOpen({{ (int) $item['id'] }})" :aria-expanded="openId === {{ (int) $item['id'] }} ? 'true' : 'false'">
              <span class="min-w-0 flex-1 truncate font-medium text-gray-800">{{ $item['name'] }}</span>
              <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform" :class="openId === {{ (int) $item['id'] }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="openId === {{ (int) $item['id'] }}" x-cloak class="border-t border-gray-100 bg-gray-50 px-4 py-4 text-sm">
              <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="mb-3 h-24 w-full rounded border border-gray-200 object-cover" />
              <p class="text-xs text-gray-600">{{ number_format($item['size'] / 1024, 1) }} KB</p>
              <p class="mt-1 truncate text-xs text-gray-600" title="{{ $item['path'] }}">{{ $item['path'] }}</p>
              <label class="mt-3 inline-flex items-center gap-2 text-xs text-gray-600"><input type="checkbox" class="rounded border-gray-300 text-indigo-600" :value="{{ (int) $item['id'] }}" x-model.number="selected"> Select</label>
              <div class="mt-3 flex flex-col gap-2">
                <button type="button" class="admin-btn" data-copy-url="{{ $item['url'] }}">Copy URL</button>
                <form method="post" action="{{ route('admin.media.destroy.post', ['media' => $item['id']]) }}" onsubmit="return confirm('Delete this image? This cannot be undone.');">
                  @csrf
                  <button type="submit" class="admin-btn w-full text-rose-700">Delete</button>
                </form>
              </div>
            </div>
          </article>
        @endforeach
      </div>
      <div x-show="view === 'list'" x-cloak class="hidden lg:block overflow-x-auto rounded border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
            <tr>
              <th class="px-3 py-2">Pick</th>
              <th class="px-3 py-2">Preview</th>
              <th class="px-3 py-2">Name</th>
              <th class="px-3 py-2">Size</th>
              <th class="px-3 py-2">Path</th>
              <th class="px-3 py-2 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach ($items as $item)
              <tr class="align-middle">
                <td class="px-3 py-2">
                  <input type="checkbox" class="rounded border-gray-300 text-indigo-600" :value="{{ (int) $item['id'] }}" x-model.number="selected">
                </td>
                <td class="px-3 py-2">
                  <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="h-12 w-16 rounded border border-gray-200 object-cover" />
                </td>
                <td class="px-3 py-2 font-medium text-gray-800">{{ $item['name'] }}</td>
                <td class="px-3 py-2 text-gray-600">{{ number_format($item['size'] / 1024, 1) }} KB</td>
                <td class="max-w-sm truncate px-3 py-2 text-xs text-gray-600" title="{{ $item['path'] }}">{{ $item['path'] }}</td>
                <td class="px-3 py-2">
                  <div class="flex justify-end gap-2">
                    <button type="button" class="rounded border border-gray-300 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50" data-copy-url="{{ $item['url'] }}">Copy URL</button>
                    <form method="post" action="{{ route('admin.media.destroy.post', ['media' => $item['id']]) }}" onsubmit="return confirm('Delete this image? This cannot be undone.');">
                      @csrf
                      <button type="submit" class="rounded border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
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
        btn.textContent = 'Copied';
        setTimeout(() => { btn.textContent = old; }, 1200);
      } catch (_) {
        // ignore clipboard failures
      }
    });
  </script>
@endpush


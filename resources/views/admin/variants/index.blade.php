<x-app-layout>
  @php
    $type = $type ?? 'size';
    $rows = $rows ?? collect();
    $usage = $usage ?? collect();
    $activeCount = $rows->where('is_active', true)->count();
    $typeLabel = $type === 'color' ? __('Colors') : __('Sizes');
  @endphp

  <x-admin.page-header :title="__('Product variants')" :subtitle="__('Manage size and color options for variant matrices.')">
    <x-slot name="actions">
      <x-admin.button variant="primary" type="button" @click="$dispatch('open-create-variant')">
        <x-icon name="plus" class="w-4 h-4" /> {{ __('Add option') }}
      </x-admin.button>
    </x-slot>
  </x-admin.page-header>

  <div
    class="px-4 md:px-6 py-4 md:py-5 space-y-4"
    x-data="{
      createOpen: {{ $errors->hasAny(['value', 'is_active']) ? 'true' : 'false' }},
      editingId: {{ $errors->hasAny(['value', 'is_active']) && old('_editing_id') ? (int) old('_editing_id') : 'null' }},
      q: '',
      visible(name) {
        const query = this.q.trim().toLowerCase();
        return !query || name.toLowerCase().includes(query);
      },
    }"
    @open-create-variant.window="createOpen = true"
    @keydown.escape.window="createOpen = false; editingId = null"
  >
    <div class="flex flex-wrap gap-2 border-b border-wp-border pb-3">
      <a href="{{ route('admin.variants.index', ['type' => 'size']) }}" class="px-3 py-1.5 text-sm rounded border {{ $type === 'size' ? 'bg-wp-link text-white border-wp-link' : 'border-wp-border bg-white text-wp-text hover:bg-wp-bg' }}">{{ __('Sizes') }}</a>
      <a href="{{ route('admin.variants.index', ['type' => 'color']) }}" class="px-3 py-1.5 text-sm rounded border {{ $type === 'color' ? 'bg-wp-link text-white border-wp-link' : 'border-wp-border bg-white text-wp-text hover:bg-wp-bg' }}">{{ __('Colors') }}</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ $typeLabel }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($rows->count()) }}</span>
      </x-admin.card>
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Active') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($activeCount) }}</span>
      </x-admin.card>
      <x-admin.card variant="stats">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('In use') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format((int) $usage->sum()) }}</span>
      </x-admin.card>
    </div>

    <x-admin.card variant="toolbar">
      <div class="relative flex-1 sm:max-w-xs">
        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-wp-text-muted pointer-events-none"><x-icon name="search" class="w-4 h-4" /></span>
        <input type="search" x-model.debounce.150ms="q" placeholder="{{ __('Search options…') }}" class="w-full pl-9 pr-3 py-2 text-sm border border-wp-border rounded" />
      </div>
    </x-admin.card>

    @if (session('status'))
      <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <x-admin.card variant="table" class="hidden lg:block">
      <table class="w-full text-left border-collapse text-sm admin-luxe-table">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th class="w-32">{{ __('Sort') }}</th>
            <th class="w-28">{{ __('Status') }}</th>
            <th class="w-28">{{ __('Usage') }}</th>
            <th class="text-right w-44">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rows as $row)
            <tr x-show="visible('{{ addslashes($row->value) }}')">
              <td class="font-medium">{{ $row->value }}</td>
              <td class="text-wp-text-muted">{{ $row->sort_order }}</td>
              <td>
                @if ($row->is_active)
                  <x-admin.status-pill variant="success">{{ __('Active') }}</x-admin.status-pill>
                @else
                  <x-admin.status-pill variant="neutral">{{ __('Hidden') }}</x-admin.status-pill>
                @endif
              </td>
              <td class="text-wp-text-muted">{{ number_format((int) ($usage[$row->id] ?? 0)) }}</td>
              <td class="text-right">
                <button type="button" class="text-wp-link text-sm" @click="editingId = {{ $row->id }}">{{ __('Edit') }}</button>
                <form method="post" action="{{ route('admin.variants.destroy', $row->id) }}?type={{ $type }}" class="inline-block ml-3" onsubmit="return confirm('{{ __('Delete this option?') }}')">
                  @csrf
                  @method('DELETE')
                  <input type="hidden" name="type" value="{{ $type }}" />
                  <button type="submit" class="text-rose-600 text-sm">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5"><x-admin.empty-state :title="__('No options yet.')" /></td></tr>
          @endforelse
        </tbody>
      </table>
    </x-admin.card>

    <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.self="createOpen = false">
      <div class="bg-white rounded w-full max-w-md p-5 border border-wp-border">
        <h3 class="text-base font-semibold text-wp-text mb-3">{{ __('Add :type option', ['type' => $typeLabel]) }}</h3>
        <form method="post" action="{{ route('admin.variants.store') }}" class="space-y-4">
          @csrf
          <input type="hidden" name="type" value="{{ $type }}" />
          <div>
            <x-input-label for="value" :value="__('Name')" />
            <x-text-input id="value" name="value" type="text" class="mt-1 block w-full" value="{{ old('value') }}" required />
            <x-input-error :messages="$errors->get('value')" class="mt-2" />
          </div>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded" />
            <span>{{ __('Active') }}</span>
          </label>
          <div class="flex justify-end gap-2">
            <x-admin.button variant="secondary" type="button" @click="createOpen = false">{{ __('Cancel') }}</x-admin.button>
            <x-admin.button variant="primary" type="submit">{{ __('Add') }}</x-admin.button>
          </div>
        </form>
      </div>
    </div>

    @foreach ($rows as $row)
      <div x-show="editingId === {{ $row->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
        <div class="bg-white rounded w-full max-w-md p-5 border border-wp-border">
          <h3 class="text-base font-semibold text-wp-text mb-3">{{ __('Edit option') }}</h3>
          <form method="post" action="{{ route('admin.variants.update', $row->id) }}?type={{ $type }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="{{ $type }}" />
            <input type="hidden" name="_editing_id" value="{{ $row->id }}" />
            <div>
              <x-input-label for="value-{{ $row->id }}" :value="__('Name')" />
              <x-text-input id="value-{{ $row->id }}" name="value" type="text" class="mt-1 block w-full" value="{{ old('value', $row->value) }}" required />
            </div>
            <div>
              <x-input-label for="sort-{{ $row->id }}" :value="__('Sort order')" />
              <x-text-input id="sort-{{ $row->id }}" name="sort_order" type="number" class="mt-1 block w-full" value="{{ old('sort_order', $row->sort_order) }}" min="0" />
            </div>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $row->is_active)) />
              <span>{{ __('Active') }}</span>
            </label>
            <div class="flex justify-end gap-2">
              <x-admin.button variant="secondary" type="button" @click="editingId = null">{{ __('Cancel') }}</x-admin.button>
              <x-admin.button variant="primary" type="submit">{{ __('Save') }}</x-admin.button>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</x-app-layout>

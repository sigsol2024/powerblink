<x-app-layout>
  @php
    $rows = $rows ?? collect();
    $usage = $usage ?? collect();
    $activeCount = $rows->where('is_active', true)->count();
    $totalUsage = (int) $usage->sum();
  @endphp

  <x-admin.page-header
    :title="__('Product categories')"
    :count="trans_choice(':count category|:count categories', $rows->count(), ['count' => number_format($rows->count())])"
  >
    <x-slot name="actions">
      <x-admin.button variant="primary" type="button" @click="$dispatch('open-create-category')">
        <x-icon name="plus" class="w-4 h-4" /> {{ __('Add category') }}
      </x-admin.button>
    </x-slot>
  </x-admin.page-header>

  <div
    class="px-4 md:px-6 py-4 md:py-5 space-y-4"
    x-data="{
      createOpen: {{ $errors->hasAny(['value', 'is_active']) && ! request()->routeIs('admin.categories.update') ? 'true' : 'false' }},
      editingId: {{ $errors->hasAny(['value', 'is_active']) && old('_editing_id') ? (int) old('_editing_id') : 'null' }},
      q: '',
      visible(name) {
        const query = this.q.trim().toLowerCase();
        return !query || name.toLowerCase().includes(query);
      },
    }"
    @open-create-category.window="createOpen = true"
    @keydown.escape.window="createOpen = false; editingId = null"
  >
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Categories') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($rows->count()) }}</span>
      </div>
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Active') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($activeCount) }}</span>
      </div>
      <div class="bg-white border border-wp-border rounded p-4 flex flex-col justify-between min-h-[5.5rem]">
        <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Products linked') }}</span>
        <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format($totalUsage) }}</span>
      </div>
    </div>

    <div class="bg-white border border-wp-border rounded p-3 md:p-4 flex flex-col gap-3">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
        <div class="relative flex-1 sm:flex-initial">
          <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-wp-text-muted pointer-events-none">
            <x-icon name="search" class="w-4 h-4" />
          </span>
          <input type="search" x-model.debounce.150ms="q" placeholder="{{ __('Search categories…') }}" class="w-full sm:w-72 pl-9 pr-3 py-2 text-sm border border-wp-border rounded focus:outline-none focus:ring-1 focus:ring-wp-link" aria-label="{{ __('Search categories') }}" />
        </div>
      </div>
    </div>

    @if (session('status'))
      <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    @if ($errors->any() && ! $errors->hasAny(['value', 'is_active']))
      <div class="rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        @foreach ($errors->all() as $message)<p>{{ $message }}</p>@endforeach
      </div>
    @endif

    <div class="bg-white border border-wp-border rounded overflow-hidden hidden lg:block">
      <table class="w-full text-left border-collapse text-sm">
        <thead class="bg-wp-bg border-b border-wp-border">
          <tr>
            <th class="px-4 py-3 font-semibold text-wp-text-muted text-xs uppercase tracking-wide">{{ __('Name') }}</th>
            <th class="px-4 py-3 font-semibold text-wp-text-muted text-xs uppercase tracking-wide w-32">{{ __('Sort') }}</th>
            <th class="px-4 py-3 font-semibold text-wp-text-muted text-xs uppercase tracking-wide w-28">{{ __('Status') }}</th>
            <th class="px-4 py-3 font-semibold text-wp-text-muted text-xs uppercase tracking-wide w-28">{{ __('Products') }}</th>
            <th class="px-4 py-3 font-semibold text-wp-text-muted text-xs uppercase tracking-wide text-right w-44">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rows as $row)
            <tr class="border-b border-wp-border last:border-0" x-show="visible('{{ addslashes($row->value) }}')">
              <td class="px-4 py-3 font-medium text-wp-text">{{ $row->value }}</td>
              <td class="px-4 py-3 text-wp-text-muted">{{ $row->sort_order }}</td>
              <td class="px-4 py-3">
                @if ($row->is_active)
                  <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">{{ __('Active') }}</span>
                @else
                  <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ __('Hidden') }}</span>
                @endif
              </td>
              <td class="px-4 py-3 text-wp-text-muted">{{ number_format((int) ($usage[$row->id] ?? 0)) }}</td>
              <td class="px-4 py-3 text-right">
                <button type="button" class="text-wp-link hover:text-wp-link-hover text-sm" @click="editingId = {{ $row->id }}">{{ __('Edit') }}</button>
                <form method="post" action="{{ route('admin.categories.destroy', $row->id) }}" class="inline-block ml-3" onsubmit="return confirm('{{ __('Delete this category?') }}')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-rose-600 hover:text-rose-700 text-sm">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-wp-text-muted">{{ __('No categories yet. Click “Add category” to start.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="space-y-3 lg:hidden">
      @forelse ($rows as $row)
        <article class="bg-white border border-wp-border rounded overflow-hidden" x-show="visible('{{ addslashes($row->value) }}')">
          <div class="px-4 py-3 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <p class="font-semibold text-wp-text truncate">{{ $row->value }}</p>
              <p class="text-xs text-wp-text-muted">{{ number_format((int) ($usage[$row->id] ?? 0)) }} {{ __('products') }} · {{ __('Sort') }} {{ $row->sort_order }}</p>
            </div>
            @if ($row->is_active)
              <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 shrink-0">{{ __('Active') }}</span>
            @else
              <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700 shrink-0">{{ __('Hidden') }}</span>
            @endif
          </div>
          <div class="border-t border-wp-border bg-slate-50 px-4 py-2 flex items-center justify-end gap-3 text-sm">
            <button type="button" class="text-wp-link hover:text-wp-link-hover" @click="editingId = {{ $row->id }}">{{ __('Edit') }}</button>
            <form method="post" action="{{ route('admin.categories.destroy', $row->id) }}" onsubmit="return confirm('{{ __('Delete this category?') }}')">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-rose-600 hover:text-rose-700">{{ __('Delete') }}</button>
            </form>
          </div>
        </article>
      @empty
        <p class="text-sm text-wp-text-muted text-center py-6">{{ __('No categories yet.') }}</p>
      @endforelse
    </div>

    <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.self="createOpen = false">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-5">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-base font-semibold text-wp-text">{{ __('Add category') }}</h3>
          <button type="button" class="text-wp-text-muted hover:text-wp-text" @click="createOpen = false" aria-label="{{ __('Close') }}">
            <x-icon name="close" class="w-5 h-5" />
          </button>
        </div>
        <form method="post" action="{{ route('admin.categories.store') }}" class="space-y-4">
          @csrf
          <div>
            <x-input-label for="value" :value="__('Category name')" />
            <x-text-input id="value" name="value" type="text" class="mt-1 block w-full" value="{{ old('value') }}" required />
            <x-input-error :messages="$errors->get('value')" class="mt-2" />
          </div>
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300" />
            <span>{{ __('Active (visible to customers)') }}</span>
          </label>
          <div class="flex items-center justify-end gap-2 pt-2">
            <button type="button" class="text-sm text-wp-text-muted hover:text-wp-text px-3 py-2" @click="createOpen = false">{{ __('Cancel') }}</button>
            <button type="submit" class="admin-luxe-btn-primary">{{ __('Add category') }}</button>
          </div>
        </form>
      </div>
    </div>

    @foreach ($rows as $row)
      <div x-show="editingId === {{ $row->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.self="editingId = null">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-5">
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-semibold text-wp-text">{{ __('Edit category') }}</h3>
            <button type="button" class="text-wp-text-muted hover:text-wp-text" @click="editingId = null" aria-label="{{ __('Close') }}">
              <x-icon name="close" class="w-5 h-5" />
            </button>
          </div>
          <form method="post" action="{{ route('admin.categories.update', $row->id) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_editing_id" value="{{ $row->id }}" />
            <div>
              <x-input-label for="value-{{ $row->id }}" :value="__('Category name')" />
              <x-text-input id="value-{{ $row->id }}" name="value" type="text" class="mt-1 block w-full" value="{{ old('value', $row->value) }}" required />
              <x-input-error :messages="$errors->get('value')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="sort-{{ $row->id }}" :value="__('Sort order')" />
              <x-text-input id="sort-{{ $row->id }}" name="sort_order" type="number" class="mt-1 block w-full" value="{{ old('sort_order', $row->sort_order) }}" min="0" />
              <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
            </div>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $row->is_active)) class="rounded border-slate-300" />
              <span>{{ __('Active (visible to customers)') }}</span>
            </label>
            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" class="text-sm text-wp-text-muted hover:text-wp-text px-3 py-2" @click="editingId = null">{{ __('Cancel') }}</button>
              <button type="submit" class="admin-luxe-btn-primary">{{ __('Save changes') }}</button>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</x-app-layout>

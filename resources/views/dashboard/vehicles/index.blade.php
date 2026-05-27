@php
  $isAdminList = $isAdminList ?? false;
  $statusFilter = $statusFilter ?? '';
@endphp
<x-app-layout>
  <div
    class="flex flex-col min-h-full"
    x-data="{
      openId: null,
      openMenuId: null,
      rejectExpandedId: null,
      expandedMobileId: null,
      selectedStatus: @js((string) $statusFilter),
      searchQuery: '',
      pageStatuses: @js($vehicles->pluck('status')->values()->all()),
      rowMatchesSearch(title) {
        if (!this.searchQuery.trim()) return true;
        return title.toLowerCase().includes(this.searchQuery.trim().toLowerCase());
      },
      toggleOpen(id) {
        this.openId = this.openId === id ? null : id;
      },
      toggleMobile(id) {
        this.expandedMobileId = this.expandedMobileId === id ? null : id;
      },
      toggleMenu(id) {
        if (this.openMenuId === id) {
          this.openMenuId = null;
          this.rejectExpandedId = null;
        } else {
          this.openMenuId = id;
          this.rejectExpandedId = null;
        }
      },
      closeMenus() {
        this.openMenuId = null;
        this.rejectExpandedId = null;
      },
      toggleReject(id) {
        this.rejectExpandedId = this.rejectExpandedId === id ? null : id;
      },
      matchesStatus(status) {
        return this.selectedStatus === '' || this.selectedStatus === status;
      },
      countFor(status) {
        if (status === '') return this.pageStatuses.length;
        return this.pageStatuses.filter((s) => s === status).length;
      },
      filteredCount() {
        return this.countFor(this.selectedStatus);
      },
    }"
    @keydown.escape.window="closeMenus(); openId = null"
    @scroll.window="openMenuId != null && closeMenus()"
  >
    @if ($isAdminList)
      <x-admin.page-header :title="__('Products')" :subtitle="__('Manage your catalog.')">
        <x-slot name="actions">
          <x-admin.button variant="primary" :href="route('dashboard.vehicles.create')">
            <x-icon name="plus" class="w-4 h-4" /> {{ __('Add product') }}
          </x-admin.button>
        </x-slot>
      </x-admin.page-header>

      <x-admin.page-content class="pb-8">
        @if (session('status'))
          <div class="border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-900 rounded">{{ session('status') }}</div>
        @endif

        @if ($stats)
          {{-- Stats: 3 separate boxes matching dashboard style --}}
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <x-admin.card variant="stats">
              <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Total') }}</span>
              <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format((int) $stats['total']) }}</span>
            </x-admin.card>
            <x-admin.card variant="stats">
              <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Pending') }}</span>
              <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format((int) $stats['pending']) }}</span>
            </x-admin.card>
            <x-admin.card variant="stats">
              <span class="text-xs uppercase tracking-wide text-wp-text-muted">{{ __('Approved') }}</span>
              <span class="text-2xl font-semibold text-wp-text leading-none">{{ number_format((int) $stats['approved']) }}</span>
            </x-admin.card>
          </div>

          <x-admin.card variant="toolbar">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
              <div class="relative flex-1 sm:flex-initial">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-wp-text-muted pointer-events-none">
                  <x-icon name="search" class="w-4 h-4" />
                </span>
                <input
                  type="search"
                  x-model.debounce.250ms="searchQuery"
                  class="w-full sm:w-72 pl-9 pr-3 text-sm"
                  placeholder="{{ __('Search products…') }}"
                  aria-label="{{ __('Search products') }}"
                  @keydown.enter.prevent
                />
              </div>
              <button
                type="button"
                @click="searchQuery = $refs.searchInput?.value ?? searchQuery"
                class="bg-black text-white border border-black hover:opacity-90 px-4 py-2 text-xs font-medium rounded transition-opacity inline-flex items-center justify-center"
              >
                {{ __('Search') }}
              </button>
              <button
                type="button"
                x-show="searchQuery.length > 0"
                x-cloak
                @click="searchQuery = ''"
                class="text-xs text-wp-text-muted hover:text-wp-text underline"
              >{{ __('Clear') }}</button>
            </div>
            <div class="flex flex-wrap items-center gap-1.5">
              <span class="text-[11px] text-wp-text-muted mr-1">{{ __('Filter') }}:</span>
              <button type="button" @click="selectedStatus = ''" :class="selectedStatus === '' ? 'bg-wp-link text-white border-wp-link' : 'border-wp-border bg-white hover:bg-wp-bg text-wp-text'" class="border px-2.5 py-1 text-xs rounded">{{ __('All') }} (<span x-text="countFor('')"></span>)</button>
              @foreach (['pending' => __('Pending'), 'approved' => __('Approved'), 'draft' => __('Draft'), 'rejected' => __('Rejected')] as $st => $label)
                <button type="button" @click="selectedStatus = '{{ $st }}'" :class="selectedStatus === '{{ $st }}' ? 'bg-wp-link text-white border-wp-link' : 'border-wp-border bg-white hover:bg-wp-bg text-wp-text'" class="border px-2.5 py-1 text-xs rounded">{{ $label }} (<span x-text="countFor('{{ $st }}')"></span>)</button>
              @endforeach
            </div>
          </x-admin.card>
        @endif

        @if ($vehicles->total() === 0)
          <x-admin.empty-state :title="__('No products match this filter.')" />
        @else
          {{-- Desktop table (lg+) --}}
          <x-admin.card variant="table" class="hidden lg:block">
            <div class="overflow-x-auto">
              <table class="w-full border-collapse text-left admin-luxe-table">
                <thead>
                  <tr>
                    <th class="w-20">{{ __('Image') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($vehicles as $vehicle)
                    @include('dashboard.vehicles.partials.index-row-luxe', ['vehicle' => $vehicle, 'isAdminList' => $isAdminList])
                  @endforeach
                </tbody>
              </table>
            </div>
          </x-admin.card>

          {{-- Mobile accordion (<lg) --}}
          <div class="lg:hidden space-y-2">
            @foreach ($vehicles as $vehicle)
              @include('dashboard.vehicles.partials.index-card-mobile', ['vehicle' => $vehicle, 'isAdminList' => $isAdminList])
            @endforeach
          </div>

          <div class="flex flex-col sm:flex-row items-center justify-between gap-2 py-4 admin-luxe-pagination">
            <span class="text-xs text-wp-text-muted">
              {{ __('Showing :from–:to of :total products', [
                'from' => $vehicles->firstItem() ?? 0,
                'to' => $vehicles->lastItem() ?? 0,
                'total' => $vehicles->total(),
              ]) }}
            </span>
            {{ $vehicles->links() }}
          </div>
        @endif
      </x-admin.page-content>

      @include('admin.partials.luxe-footer')
    @else
      {{-- Dealer view (non-admin): same WooCommerce-style shell, scoped to the dealer's products. --}}
      <div class="flex flex-col md:flex-row md:items-center justify-between px-4 md:px-6 pt-4 pb-3 gap-3">
        <div>
          <h2 class="text-lg font-semibold text-wp-text">{{ __('My products') }}</h2>
          <p class="text-wp-text-muted text-xs mt-0.5">{{ __('Manage your product listings.') }}</p>
        </div>
        <a href="{{ route('dashboard.vehicles.create') }}" class="admin-luxe-btn-primary">
          <x-icon name="plus" class="w-4 h-4" /> {{ __('New product') }}
        </a>
      </div>

      <div class="px-4 md:px-6 pb-8">
        @if (session('status'))
          <div class="mb-4 border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-900 rounded">{{ session('status') }}</div>
        @endif

        @if ($vehicles->total() === 0)
          <p class="text-wp-text-muted py-12 text-sm text-center">{{ __('You have no listings yet.') }}</p>
        @else
          <div class="hidden lg:block bg-white border border-wp-border rounded overflow-hidden">
            <div class="overflow-x-auto">
              <table class="w-full border-collapse text-left">
                <thead>
                  <tr>
                    <th class="w-20">{{ __('Image') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th class="text-right">{{ __('Actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($vehicles as $vehicle)
                    @include('dashboard.vehicles.partials.index-row-luxe', ['vehicle' => $vehicle, 'isAdminList' => false])
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="lg:hidden space-y-2">
            @foreach ($vehicles as $vehicle)
              @include('dashboard.vehicles.partials.index-card-mobile', ['vehicle' => $vehicle, 'isAdminList' => false])
            @endforeach
          </div>

          <div class="py-4 admin-luxe-pagination">{{ $vehicles->links() }}</div>
        @endif
      </div>
    @endif
  </div>
</x-app-layout>

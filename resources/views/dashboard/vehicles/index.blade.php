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
      <div class="flex flex-col md:flex-row md:items-end justify-between px-margin-mobile md:px-gutter pt-8 md:pt-12 pb-6 md:pb-8 gap-6 max-w-max-container w-full mx-auto shrink-0">
        <div>
          <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary tracking-tight">{{ __('Product Management') }}</h2>
          <p class="text-on-surface-variant font-body-md mt-2 max-w-xl">{{ __('Curate and manage the collection. High-precision control over inventory and digital presentation.') }}</p>
        </div>
        <div>
          <a href="{{ route('dashboard.vehicles.create') }}" class="admin-luxe-btn-primary">
            <span class="material-symbols-outlined text-lg">add</span>
            {{ __('Add New Product') }}
          </a>
        </div>
      </div>

      <div class="flex-1 px-margin-mobile md:px-gutter pb-section-py-mobile md:pb-section-py-desktop overflow-y-auto">
        <div class="max-w-max-container mx-auto">
          @if (session('status'))
            <div class="mb-6 border border-secondary/30 bg-secondary-fixed/20 px-4 py-3 text-sm text-on-surface">{{ session('status') }}</div>
          @endif

          @if ($stats)
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 border-b border-outline-variant pb-6">
              <div class="flex gap-6 md:gap-8 flex-wrap">
                <div class="flex flex-col">
                  <span class="font-label-caps text-on-surface-variant text-[10px]">{{ __('TOTAL PRODUCTS') }}</span>
                  <span class="font-headline-md">{{ $stats['total'] }}</span>
                </div>
                <div class="flex flex-col">
                  <span class="font-label-caps text-on-surface-variant text-[10px]">{{ __('PENDING REVIEW') }}</span>
                  <span class="font-headline-md">{{ $stats['pending'] }}</span>
                </div>
                <div class="flex flex-col">
                  <span class="font-label-caps text-on-surface-variant text-[10px]">{{ __('APPROVED LIVE') }}</span>
                  <span class="font-headline-md">{{ $stats['approved'] }}</span>
                </div>
              </div>
              <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-initial">
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                  <input
                    type="search"
                    x-model="searchQuery"
                    class="w-full sm:w-64 pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant focus:ring-0 focus:border-primary font-label-caps text-[11px] placeholder:text-on-surface-variant"
                    placeholder="{{ __('SEARCH COLLECTION...') }}"
                    aria-label="{{ __('Search products') }}"
                  />
                </div>
                <button type="button" class="border border-outline-variant px-4 py-2 font-label-caps text-[11px] hover:bg-surface-container-high transition-colors flex items-center justify-center gap-2" @click="selectedStatus = selectedStatus === '' ? 'pending' : ''">
                  <span class="material-symbols-outlined text-sm">filter_list</span>
                  {{ __('FILTERS') }}
                </button>
              </div>
              <div class="flex flex-wrap items-center gap-2 w-full">
                <span class="font-label-caps text-[10px] text-on-surface-variant w-full sm:w-auto">{{ __('FILTER') }}:</span>
                <button type="button" @click="selectedStatus = ''" :class="selectedStatus === '' ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant hover:bg-surface-container-high'" class="border px-3 py-2 font-label-caps text-[11px]">{{ __('ALL') }} (<span x-text="countFor('')"></span>)</button>
                @foreach (['pending' => __('Pending'), 'approved' => __('Approved'), 'draft' => __('Draft'), 'rejected' => __('Rejected')] as $st => $label)
                  <button type="button" @click="selectedStatus = '{{ $st }}'" :class="selectedStatus === '{{ $st }}' ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant hover:bg-surface-container-high'" class="border px-3 py-2 font-label-caps text-[11px]">{{ $label }} (<span x-text="countFor('{{ $st }}')"></span>)</button>
                @endforeach
              </div>
            </div>
          @endif

          @if ($vehicles->total() === 0)
            <p class="text-on-surface-variant py-12 text-center">{{ __('No products match this filter.') }}</p>
          @else
            <div class="overflow-x-auto -mx-margin-mobile md:mx-0 px-margin-mobile md:px-0">
              <table class="w-full border-collapse text-left admin-luxe-table min-w-[640px]">
                <thead>
                  <tr class="border-b border-outline-variant">
                    <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em] w-24">{{ __('IMAGE') }}</th>
                    <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('PRODUCT NAME') }}</th>
                    <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('CATEGORY') }}</th>
                    <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('PRICE') }}</th>
                    <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('STATUS') }}</th>
                    <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em] text-right">{{ __('ACTIONS') }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                  @foreach ($vehicles as $vehicle)
                    @include('dashboard.vehicles.partials.index-row-luxe', ['vehicle' => $vehicle, 'isAdminList' => $isAdminList])
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="flex items-center justify-between py-10 border-t border-outline-variant mt-8 admin-luxe-pagination">
              <span class="text-label-caps text-[10px] text-on-surface-variant">
                {{ __('Showing :from–:to of :total products', [
                  'from' => $vehicles->firstItem() ?? 0,
                  'to' => $vehicles->lastItem() ?? 0,
                  'total' => $vehicles->total(),
                ]) }}
              </span>
              {{ $vehicles->links() }}
            </div>
          @endif
        </div>
      </div>

      @include('admin.partials.luxe-footer')
    @else
      {{-- Dealer: luxe shell with simplified listing table (same CRUD partials) --}}
      <div class="flex flex-col md:flex-row md:items-end justify-between px-gutter pt-12 pb-8 gap-6 max-w-max-container w-full mx-auto">
        <div>
          <h2 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary tracking-tight">{{ __('My Products') }}</h2>
          <p class="text-on-surface-variant font-body-md mt-2">{{ __('Manage your product listings.') }}</p>
        </div>
        <a href="{{ route('dashboard.vehicles.create') }}" class="admin-luxe-btn-primary">
          <span class="material-symbols-outlined text-lg">add</span>
          {{ __('New product') }}
        </a>
      </div>

      <div class="flex-1 px-gutter pb-12 max-w-max-container mx-auto w-full">
        @if (session('status'))
          <div class="mb-6 border border-secondary/30 bg-secondary-fixed/20 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        @if ($vehicles->total() === 0)
          <p class="text-on-surface-variant py-12">{{ __('You have no listings yet.') }}</p>
        @else
          <div class="overflow-x-auto bg-surface-container-lowest border border-outline-variant">
            <table class="w-full border-collapse text-left">
              <thead>
                <tr class="border-b border-outline-variant bg-surface-container-low">
                  <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em] w-24">{{ __('IMAGE') }}</th>
                  <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('PRODUCT NAME') }}</th>
                  <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('CATEGORY') }}</th>
                  <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('PRICE') }}</th>
                  <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em]">{{ __('STATUS') }}</th>
                  <th class="py-4 font-label-caps text-[11px] text-on-surface-variant tracking-[0.25em] text-right">{{ __('ACTIONS') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant">
                @foreach ($vehicles as $vehicle)
                  @include('dashboard.vehicles.partials.index-row-luxe', ['vehicle' => $vehicle, 'isAdminList' => false])
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-8 admin-luxe-pagination">{{ $vehicles->links() }}</div>
        @endif
      </div>
    @endif
  </div>
</x-app-layout>

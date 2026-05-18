@php
  $isAdminList = $isAdminList ?? false;
  $statusFilter = $statusFilter ?? '';
@endphp
<x-app-layout>
  <x-slot name="header">
    <h2 class="admin-page-title truncate">
      {{ $isAdminList ? __('All listings') : __('My vehicles') }}
    </h2>
  </x-slot>

  <div class="admin-content-toolbar">
    <div class="admin-content-toolbar__actions">
      <a href="{{ route('dashboard.vehicles.create') }}" class="admin-btn-primary">{{ __('New listing') }}</a>
    </div>
  </div>

  <div
    class="w-full"
    x-data="{
      openId: null,
      openMenuId: null,
      rejectExpandedId: null,
      selectedStatus: @js((string) $statusFilter),
      pageStatuses: @js($vehicles->pluck('status')->values()->all()),
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
    @if($isAdminList && $stats)
      <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
          <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Total on site') }}</div>
          <div class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total'] }}</div>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
          <div class="text-xs font-semibold uppercase tracking-wide text-amber-800">{{ __('Pending review') }}</div>
          <div class="mt-2 text-3xl font-bold text-amber-900">{{ $stats['pending'] }}</div>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
          <div class="text-xs font-semibold uppercase tracking-wide text-emerald-800">{{ __('Approved live') }}</div>
          <div class="mt-2 text-3xl font-bold text-emerald-900">{{ $stats['approved'] }}</div>
        </div>
      </div>

      <div class="mb-4 flex flex-wrap gap-2 gap-y-2">
        <span class="w-full text-sm font-medium text-slate-600 sm:w-auto sm:self-center">{{ __('Filter') }}:</span>
        <button type="button" @click="selectedStatus = ''" :class="selectedStatus === '' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50'" class="rounded-full px-3 py-1 text-xs font-medium sm:text-sm">{{ __('All') }} (<span x-text="countFor('')"></span>)</button>
        @foreach (['pending' => __('Pending'), 'approved' => __('Approved'), 'draft' => __('Draft'), 'rejected' => __('Rejected')] as $st => $label)
          <button type="button" @click="selectedStatus = '{{ $st }}'" :class="selectedStatus === '{{ $st }}' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50'" class="rounded-full px-3 py-1 text-xs font-medium sm:text-sm">{{ $label }} (<span x-text="countFor('{{ $st }}')"></span>)</button>
        @endforeach
        <span class="w-full text-xs text-slate-500 sm:w-auto sm:self-center">{{ __('Showing') }} <span x-text="filteredCount()"></span> {{ __('on this page') }}</span>
      </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="p-4 sm:p-6">
        @if(session('status'))
          <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-100">
            {{ session('status') }}
          </div>
        @endif

        @if($vehicles->total() === 0)
          <p class="text-slate-600">{{ $isAdminList ? __('No vehicles match this filter.') : __('You have no listings yet.') }}</p>
        @else
          {{-- Desktop table --}}
          <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
              <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                  <th class="whitespace-nowrap px-4 py-3">{{ __('Listing') }}</th>
                  @if($isAdminList)
                    <th class="whitespace-nowrap px-4 py-3">{{ __('Posted by') }}</th>
                  @endif
                  <th class="whitespace-nowrap px-4 py-3">{{ __('Status') }}</th>
                  <th class="whitespace-nowrap px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                @foreach($vehicles as $vehicle)
                  @include('dashboard.vehicles.partials.index-row', ['vehicle' => $vehicle, 'isAdminList' => $isAdminList, 'layout' => 'desktop'])
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Mobile accordion cards --}}
          <div class="lg:hidden space-y-3 pb-4">
            @foreach($vehicles as $vehicle)
              @include('dashboard.vehicles.partials.index-row', ['vehicle' => $vehicle, 'isAdminList' => $isAdminList, 'layout' => 'mobile'])
            @endforeach
          </div>

          <div class="mt-6 border-t border-slate-100 pt-4">
            {{ $vehicles->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>

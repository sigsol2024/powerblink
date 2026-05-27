@php
  $owner = $vehicle->user;
  $isStaffListing = $owner && $owner->hasRole('admin');
  $viewUrl = $vehicle->status === 'approved' ? route('inventory.show', ['slug' => $vehicle->slug]) : null;
  $canApprove = $isAdminList && in_array($vehicle->status, ['pending', 'draft', 'rejected'], true);
  $canReject = $isAdminList && $vehicle->status !== 'rejected';
  $rejectReasonDefault = old('rejection_reason', $vehicle->rejection_reason ?? '');
  $listingWhen = $vehicle->submitted_at ?? $vehicle->created_at;
  $coverImage = $vehicle->images->first();
  $thumbUrl = $coverImage ? \App\Support\VehicleImageUrl::url($coverImage->path) : \App\Support\PlaceholderMedia::url('asset/images/media/inventory-listing-fallback.jpg');
  $statusClass = match ($vehicle->status) {
      'approved' => 'bg-emerald-100 text-emerald-800',
      'pending' => 'bg-amber-100 text-amber-900',
      'rejected' => 'bg-rose-100 text-rose-800',
      default => 'bg-slate-100 text-slate-700',
  };
  $layout = $layout ?? 'desktop';
@endphp

@if ($layout === 'desktop')
  <tr class="align-top" x-show="matchesStatus('{{ $vehicle->status }}')">
    <td class="min-w-0 max-w-xs px-4 py-3 sm:max-w-md">
      <div class="flex items-start gap-3">
        <img src="{{ $thumbUrl }}" alt="" class="h-12 w-16 shrink-0 rounded-md border border-slate-200 object-cover" loading="lazy" />
        <div class="min-w-0">
          <div class="truncate font-semibold text-slate-900" title="{{ $vehicle->title }}">{{ $vehicle->title }}</div>
          <div class="text-xs text-slate-500">{{ $vehicle->categoryOption?->value ?? '—' }}</div>
          <div class="mt-0.5 text-[11px] leading-snug text-slate-500">
            @if($listingWhen)
              {{ __('Submitted') }} {{ $listingWhen->format('M j, Y') }} · {{ $listingWhen->format('g:i a') }}
            @else
              —
            @endif
          </div>
        </div>
      </div>
    </td>
    @if($isAdminList)
      <td class="px-4 py-3 text-slate-700">
        <div class="font-medium">{{ $owner?->name ?? '—' }}</div>
        <div class="text-xs text-slate-500">{{ $owner?->email }}</div>
        <div class="mt-1">
          @if($isStaffListing)
            <span class="inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-800">{{ __('Staff listing') }}</span>
          @else
            <span class="inline-flex rounded-full bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-800">{{ __('Dealer listing') }}</span>
          @endif
        </div>
      </td>
    @endif
    <td class="px-4 py-3">
      <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ strtoupper($vehicle->status) }}</span>
    </td>
    <td class="relative px-4 py-3 text-right">
      <button
        type="button"
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900"
        title="{{ __('Actions') }}"
        @click.stop="toggleMenu({{ $vehicle->id }})"
        :aria-expanded="openMenuId === {{ $vehicle->id }} ? 'true' : 'false'"
        aria-label="{{ __('Actions') }}"
      >
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
      </button>
      <div
        x-show="openMenuId === {{ $vehicle->id }}"
        x-cloak
        x-transition
        @click.outside="closeMenus()"
        class="absolute right-4 top-full z-[300] mt-1 w-52 max-h-[min(70vh,22rem)] overflow-y-auto rounded-lg border border-slate-200 bg-white py-1 text-left shadow-lg ring-1 ring-black/5"
        role="menu"
      >
        @include('dashboard.vehicles.partials.index-row-actions', ['vehicle' => $vehicle, 'viewUrl' => $viewUrl, 'canApprove' => $canApprove, 'canReject' => $canReject, 'rejectReasonDefault' => $rejectReasonDefault, 'menu' => true])
      </div>
    </td>
  </tr>
@else
  <article
    class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"
    x-show="matchesStatus('{{ $vehicle->status }}')"
  >
    <button
      type="button"
      class="flex w-full items-center gap-3 px-4 py-3 text-left"
      @click="toggleOpen({{ $vehicle->id }})"
      :aria-expanded="openId === {{ $vehicle->id }} ? 'true' : 'false'"
    >
      <img src="{{ $thumbUrl }}" alt="" class="h-12 w-16 shrink-0 rounded-md border border-slate-200 object-cover" loading="lazy" />
      <div class="min-w-0 flex-1">
        <div class="truncate font-semibold text-slate-900">{{ $vehicle->title }}</div>
        <div class="mt-1 flex flex-wrap items-center gap-2">
          <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ strtoupper($vehicle->status) }}</span>
        </div>
      </div>
      <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform" :class="openId === {{ $vehicle->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="openId === {{ $vehicle->id }}" x-cloak class="border-t border-slate-100 bg-slate-50/80 px-4 py-4 text-sm text-slate-700">
      <p class="text-xs text-slate-500">{{ $vehicle->categoryOption?->value ?? '—' }}</p>
      @if($listingWhen)
        <p class="mt-1 text-xs text-slate-500">{{ __('Submitted') }} {{ $listingWhen->format('M j, Y') }} · {{ $listingWhen->format('g:i a') }}</p>
      @endif
      @if($isAdminList && $owner)
        <p class="mt-2 font-medium text-slate-800">{{ $owner->name }}</p>
        <p class="text-xs text-slate-500">{{ $owner->email }}</p>
        <p class="mt-1">
          @if($isStaffListing)
            <span class="inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-xs font-medium text-violet-800">{{ __('Staff listing') }}</span>
          @else
            <span class="inline-flex rounded-full bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-800">{{ __('Dealer listing') }}</span>
          @endif
        </p>
      @endif
      <div class="mt-4 flex flex-col gap-2">
        @include('dashboard.vehicles.partials.index-row-actions', ['vehicle' => $vehicle, 'viewUrl' => $viewUrl, 'canApprove' => $canApprove, 'canReject' => $canReject, 'rejectReasonDefault' => $rejectReasonDefault, 'menu' => false])
      </div>
    </div>
  </article>
@endif

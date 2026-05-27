@php
  $owner = $vehicle->user;
  $viewUrl = $vehicle->status === 'approved' ? route('product.show', ['slug' => $vehicle->slug]) : null;
  $canApprove = $isAdminList && in_array($vehicle->status, ['pending', 'draft', 'rejected'], true);
  $canReject = $isAdminList && $vehicle->status !== 'rejected';
  $rejectReasonDefault = old('rejection_reason', $vehicle->rejection_reason ?? '');
  $coverImage = $vehicle->images->first();
  $thumbUrl = $coverImage ? \App\Support\VehicleImageUrl::url($coverImage->path) : \App\Support\PlaceholderMedia::url('asset/images/media/inventory-listing-fallback.jpg');
  $categoryLabel = $vehicle->categoryOption?->value ?? '—';
  $priceLabel = ! is_null($vehicle->price) ? format_currency($vehicle->price) : __('Ask');
  $statusBadge = match ($vehicle->status) {
      'approved' => ['label' => __('Published'), 'class' => 'bg-green-100 text-green-800 border-green-200'],
      'pending' => ['label' => __('Pending'), 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200'],
      'rejected' => ['label' => __('Rejected'), 'class' => 'bg-red-100 text-red-800 border-red-200'],
      default => ['label' => ucfirst($vehicle->status), 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
  };
  $sku = $vehicle->vin ?: ('PRD-'.$vehicle->id);
@endphp

<div
  class="bg-white border border-wp-border rounded overflow-hidden"
  x-show="matchesStatus('{{ $vehicle->status }}') && rowMatchesSearch(@js($vehicle->title))"
>
  <div class="flex items-center gap-3 p-3">
    <button
      type="button"
      class="flex items-center gap-3 flex-1 min-w-0 text-left"
      @click="toggleMobile({{ $vehicle->id }})"
      :aria-expanded="expandedMobileId === {{ $vehicle->id }} ? 'true' : 'false'"
    >
      <div class="w-14 h-14 bg-wp-bg overflow-hidden rounded shrink-0">
        <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover" loading="lazy" />
      </div>
      <div class="flex flex-col min-w-0 flex-1">
        <span class="text-wp-link font-medium text-sm truncate">{{ $vehicle->title }}</span>
        <span class="inline-flex items-center mt-1 self-start px-2 py-0.5 border rounded text-[10px] font-medium {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
      </div>
      <span class="text-wp-text-muted shrink-0">
        <svg :class="expandedMobileId === {{ $vehicle->id }} ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </span>
    </button>

    <div class="relative shrink-0">
      <button
        type="button"
        class="text-wp-text-muted hover:text-wp-text transition-colors inline-flex items-center p-1"
        title="{{ __('More actions') }}"
        @click.stop="toggleMenu({{ $vehicle->id }})"
        :aria-expanded="openMenuId === {{ $vehicle->id }} ? 'true' : 'false'"
        aria-label="{{ __('More actions') }}"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
        </svg>
      </button>
      <div
        x-show="openMenuId === {{ $vehicle->id }}"
        x-cloak
        x-transition
        @click.outside="closeMenus()"
        class="absolute right-0 top-full z-[300] mt-1 w-56 max-h-[min(70vh,22rem)] overflow-y-auto bg-white border border-wp-border py-1 text-left shadow-lg rounded"
        role="menu"
      >
        @include('dashboard.vehicles.partials.index-row-actions', [
          'vehicle' => $vehicle,
          'viewUrl' => $viewUrl,
          'canApprove' => $canApprove,
          'canReject' => $canReject,
          'rejectReasonDefault' => $rejectReasonDefault,
          'menu' => true,
          'luxeMenu' => true,
        ])
      </div>
    </div>
  </div>

  <div
    x-show="expandedMobileId === {{ $vehicle->id }}"
    x-cloak
    x-transition.duration.150ms
    class="border-t border-wp-border bg-wp-bg/40 px-3 py-3 space-y-2 text-sm"
  >
    <div class="flex justify-between gap-3">
      <span class="text-xs text-wp-text-muted">{{ __('Category') }}</span>
      <span class="text-wp-text text-right">{{ $categoryLabel }}</span>
    </div>
    <div class="flex justify-between gap-3">
      <span class="text-xs text-wp-text-muted">{{ __('Price') }}</span>
      <span class="text-wp-text font-medium text-right">{{ $priceLabel }}</span>
    </div>
    <div class="flex justify-between gap-3">
      <span class="text-xs text-wp-text-muted">{{ __('SKU') }}</span>
      <span class="text-wp-text text-right">{{ $sku }}</span>
    </div>
    <div class="pt-2">
      <a href="{{ route('dashboard.vehicles.edit', $vehicle) }}" class="block w-full text-center bg-black text-white border border-black hover:opacity-90 px-3 py-2 text-xs font-medium rounded transition-opacity">{{ __('Edit product') }}</a>
    </div>
  </div>
</div>

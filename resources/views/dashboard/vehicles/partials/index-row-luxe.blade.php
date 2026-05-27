@php
  $owner = $vehicle->user;
  $isStaffListing = $owner && $owner->hasRole('admin');
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

<tr class="group" x-show="matchesStatus('{{ $vehicle->status }}') && rowMatchesSearch(@js($vehicle->title))">
  <td>
    <div class="w-12 h-14 bg-wp-bg overflow-hidden rounded">
      <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover" loading="lazy" />
    </div>
  </td>
  <td>
    <div class="flex flex-col min-w-0">
      <a href="{{ route('dashboard.vehicles.edit', $vehicle) }}" class="text-wp-link hover:text-wp-link-hover font-medium text-sm">{{ $vehicle->title }}</a>
      <span class="text-[11px] text-wp-text-muted mt-0.5">{{ __('SKU') }}: {{ $sku }}</span>
    </div>
  </td>
  <td>
    <span class="text-sm text-wp-text">{{ $categoryLabel }}</span>
  </td>
  <td>
    <span class="text-sm font-medium text-wp-text">{{ $priceLabel }}</span>
  </td>
  <td>
    <span class="inline-flex items-center px-2 py-0.5 border rounded text-[11px] font-medium {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
  </td>
  <td class="text-right relative">
    <div class="flex justify-end gap-2 items-center">
      <button
        type="button"
        class="text-wp-text-muted hover:text-wp-text transition-colors inline-flex items-center"
        title="{{ __('More actions') }}"
        @click.stop="toggleMenu({{ $vehicle->id }})"
        :aria-expanded="openMenuId === {{ $vehicle->id }} ? 'true' : 'false'"
        aria-label="{{ __('More actions') }}"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
        </svg>
      </button>
    </div>
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
  </td>
</tr>

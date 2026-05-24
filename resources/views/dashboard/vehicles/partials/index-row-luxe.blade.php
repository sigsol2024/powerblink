@php
  $owner = $vehicle->user;
  $isStaffListing = $owner && $owner->hasRole('admin');
  $viewUrl = $vehicle->status === 'approved' ? route('product.show', ['slug' => $vehicle->slug]) : null;
  $canApprove = $isAdminList && in_array($vehicle->status, ['pending', 'draft', 'rejected'], true);
  $canReject = $isAdminList && $vehicle->status !== 'rejected';
  $rejectReasonDefault = old('rejection_reason', $vehicle->rejection_reason ?? '');
  $coverImage = $vehicle->images->first();
  $thumbUrl = $coverImage ? \App\Support\VehicleImageUrl::url($coverImage->path) : \App\Support\PlaceholderMedia::url('asset/images/media/inventory-listing-fallback.jpg');
  $categoryLabel = $vehicle->makeOption?->value ?? $vehicle->bodyTypeOption?->value ?? '—';
  $priceLabel = ! is_null($vehicle->price) ? format_currency($vehicle->price) : __('Ask');
  $statusBadge = match ($vehicle->status) {
      'approved' => ['label' => __('In Stock'), 'class' => 'border-primary text-primary'],
      'pending' => ['label' => __('Pending'), 'class' => 'border-secondary text-secondary'],
      'rejected' => ['label' => __('Rejected'), 'class' => 'border-error text-error'],
      default => ['label' => strtoupper($vehicle->status), 'class' => 'border-outline text-on-surface-variant'],
  };
  $sku = $vehicle->vin ?: ('PRD-'.$vehicle->id);
@endphp

<tr class="group" x-show="matchesStatus('{{ $vehicle->status }}') && rowMatchesSearch(@js($vehicle->title))">
  <td class="py-6 pr-4">
    <div class="w-16 h-20 bg-surface-container overflow-hidden">
      <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" loading="lazy" />
    </div>
  </td>
  <td class="py-6">
    <div class="flex flex-col min-w-0">
      <span class="font-body-md font-semibold text-primary uppercase">{{ $vehicle->title }}</span>
      <span class="text-[10px] text-on-surface-variant uppercase tracking-widest mt-1">{{ __('SKU') }}: {{ $sku }}</span>
    </div>
  </td>
  <td class="py-6">
    <span class="font-body-md text-on-surface-variant">{{ $categoryLabel }}</span>
  </td>
  <td class="py-6">
    <span class="font-body-md font-medium">{{ $priceLabel }}</span>
  </td>
  <td class="py-6">
    <span class="inline-flex items-center px-2 py-0.5 border {{ $statusBadge['class'] }} text-[10px] font-bold tracking-widest uppercase">{{ $statusBadge['label'] }}</span>
  </td>
  <td class="py-6 text-right relative">
    <div class="flex justify-end gap-4 items-center">
      <a href="{{ route('dashboard.vehicles.edit', $vehicle) }}" class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors" title="{{ __('Edit') }}">edit</a>
      <button
        type="button"
        class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors"
        title="{{ __('More actions') }}"
        @click.stop="toggleMenu({{ $vehicle->id }})"
        :aria-expanded="openMenuId === {{ $vehicle->id }} ? 'true' : 'false'"
      >more_vert</button>
    </div>
    <div
      x-show="openMenuId === {{ $vehicle->id }}"
      x-cloak
      x-transition
      @click.outside="closeMenus()"
      class="absolute right-0 top-full z-[300] mt-1 w-56 max-h-[min(70vh,22rem)] overflow-y-auto bg-surface-container-lowest border border-outline-variant py-1 text-left shadow-lg"
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

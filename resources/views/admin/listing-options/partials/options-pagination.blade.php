@php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $options */
@endphp
<div class="flex flex-col gap-3 rounded-xl border border-zinc-200/90 bg-zinc-50/80 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
  <p class="text-sm text-zinc-600">
    {{ __('Showing :from–:to of :total options', [
      'from' => $options->firstItem() ?? 0,
      'to' => $options->lastItem() ?? 0,
      'total' => $options->total(),
    ]) }}
    @if ($options->hasPages())
      <span class="mt-1 block text-xs text-zinc-500">{{ __(':count per page. Save changes applies to this page only.', ['count' => $options->perPage()]) }}</span>
    @endif
  </p>
  @if ($options->hasPages())
    <div class="admin-listing-options-pagination shrink-0">
      {{ $options->links() }}
    </div>
  @endif
</div>

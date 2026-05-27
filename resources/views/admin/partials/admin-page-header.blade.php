{{-- Deprecated: use <x-admin.page-header> instead. Kept for backward compatibility. --}}
<x-admin.page-header :title="$title ?? ''" :subtitle="$subtitle ?? null" :count="$count ?? null">
  @isset($actions)
    <x-slot name="actions">{{ $actions }}</x-slot>
  @endisset
</x-admin.page-header>

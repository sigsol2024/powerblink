@php
  $menu = $menu ?? false;
  $linkClass = $menu
      ? 'flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-slate-800 hover:bg-slate-50'
      : 'admin-btn w-full justify-start';
  $editClass = $menu
      ? 'flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-amber-900 hover:bg-amber-50'
      : 'admin-btn w-full justify-start';
@endphp

@if($viewUrl)
  <a href="{{ $viewUrl }}" target="_blank" rel="noopener noreferrer" class="{{ $linkClass }}" @if($menu) role="menuitem" @click="closeMenus()" @endif>{{ __('View live listing') }}</a>
@endif
<a href="{{ route('dashboard.vehicles.edit', $vehicle) }}" class="{{ $editClass }}" @if($menu) role="menuitem" @endif>{{ __('Edit') }}</a>

@if($canApprove)
  <form method="post" action="{{ route('admin.vehicles.approve', $vehicle) }}" class="{{ $menu ? 'border-t border-slate-100' : '' }}">
    @csrf
    <button type="submit" class="{{ $menu ? 'flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm font-medium text-emerald-800 hover:bg-emerald-50' : 'admin-btn w-full justify-start text-emerald-800' }}" @if($menu) role="menuitem" @endif>{{ __('Approve') }}</button>
  </form>
@endif

@if($canReject)
  @if($menu)
    <div class="border-t border-slate-100">
      <button type="button" class="flex w-full items-center justify-between gap-2 px-3 py-2.5 text-left text-sm font-medium text-rose-800 hover:bg-rose-50" @click.stop="toggleReject({{ $vehicle->id }})">
        <span>{{ __('Reject…') }}</span>
        <svg class="h-4 w-4 shrink-0 transition-transform" :class="rejectExpandedId === {{ $vehicle->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <form x-show="rejectExpandedId === {{ $vehicle->id }}" method="post" action="{{ route('admin.vehicles.reject', $vehicle) }}" class="space-y-2 border-t border-slate-100 bg-slate-50/90 px-3 py-3" x-cloak>
        @csrf
        <label class="block text-xs font-medium text-slate-600">{{ __('Reason (optional)') }}</label>
        <textarea name="rejection_reason" rows="3" class="w-full rounded-md border border-slate-300 text-sm shadow-sm focus:border-rose-400 focus:ring-rose-400" placeholder="{{ __('Explain to the dealer…') }}">{{ $rejectReasonDefault }}</textarea>
        <button type="submit" class="admin-btn-primary w-full">{{ __('Reject listing') }}</button>
      </form>
    </div>
  @else
    <form method="post" action="{{ route('admin.vehicles.reject', $vehicle) }}" class="space-y-2 rounded-lg border border-rose-200 bg-white p-3">
      @csrf
      <label class="block text-xs font-medium text-slate-600">{{ __('Reason (optional)') }}</label>
      <textarea name="rejection_reason" rows="3" class="w-full rounded-md border border-slate-300 text-sm shadow-sm focus:border-rose-400 focus:ring-rose-400" placeholder="{{ __('Explain to the dealer…') }}">{{ $rejectReasonDefault }}</textarea>
      <button type="submit" class="admin-btn-primary w-full">{{ __('Reject listing') }}</button>
    </form>
  @endif
@endif

<form method="post" action="{{ route('dashboard.vehicles.destroy', $vehicle) }}" class="{{ $menu ? 'border-t border-slate-100' : '' }}" onsubmit="return confirm('{{ __('Delete this listing permanently?') }}');">
  @csrf
  @method('DELETE')
  <button type="submit" class="{{ $menu ? 'flex w-full items-center gap-2 px-3 py-2.5 text-left text-sm font-medium text-rose-900 hover:bg-rose-50' : 'admin-btn w-full justify-start text-rose-900' }}" @if($menu) role="menuitem" @endif>{{ __('Delete') }}</button>
</form>

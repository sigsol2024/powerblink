@php
  $status = (string) ($status ?? '');
  $classes = match ($status) {
      'paid' => 'active-status-badge',
      'pending_payment' => 'border border-outline text-on-surface-variant bg-transparent',
      'fulfilled' => 'bg-primary text-on-primary',
      'failed', 'cancelled' => 'bg-error/10 text-error border border-error/20',
      'refunded' => 'bg-surface-container-highest text-on-surface-variant border border-outline-variant',
      default => 'bg-surface-container-highest text-on-surface-variant border border-outline-variant',
  };
@endphp
<span class="inline-block px-3 py-1 font-label-caps text-[10px] tracking-widest rounded-none uppercase {{ $classes }}">
  {{ str_replace('_', ' ', $status) }}
</span>

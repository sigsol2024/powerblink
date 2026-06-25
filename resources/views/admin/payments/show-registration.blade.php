<x-app-layout>
  <x-admin.page-header :title="__('Registration payment')" />
  <x-admin.page-content>
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Reference') }}</dt><dd class="font-mono">{{ $payment->reference }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Status') }}</dt><dd>{{ $payment->status }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Amount') }}</dt><dd>{{ format_currency($payment->amount) }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Paid at') }}</dt><dd>{{ $payment->paid_at?->format('M j, Y H:i') ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Registration') }}</dt><dd>{{ $payment->registration?->reference_code ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Player') }}</dt><dd>{{ $payment->player?->name ?? $payment->registration?->player_name ?? '—' }}</dd></div>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

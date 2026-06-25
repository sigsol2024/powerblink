<x-app-layout>
  <x-admin.page-header :title="__('Academy payment')" />
  <x-admin.page-content>
    <x-admin.card>
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Reference') }}</dt><dd class="font-mono">{{ $payment->reference }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Status') }}</dt><dd>{{ $payment->status }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Amount') }}</dt><dd>{{ format_currency($payment->amount) }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Type') }}</dt><dd>{{ $payment->type }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Player') }}</dt><dd>{{ $payment->player?->name ?? '—' }}</dd></div>
        <div><dt class="text-pb-muted text-xs uppercase">{{ __('Season') }}</dt><dd>{{ $payment->season?->name ?? '—' }}</dd></div>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

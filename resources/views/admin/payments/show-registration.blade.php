<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.payments.index')"
    :back-label="__('Payments')"
    :subtitle="__('Registration payment')"
  />

  <x-admin.page-content>
    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Reference')"><span class="font-mono">{{ $payment->reference }}</span></x-admin.detail-field>
        <x-admin.detail-field :label="__('Status')">{{ $payment->status }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Amount')"><span class="font-semibold">{{ format_currency($payment->amount) }}</span></x-admin.detail-field>
        <x-admin.detail-field :label="__('Paid at')">{{ $payment->paid_at?->format('M j, Y H:i') ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Registration')">{{ $payment->registration?->reference_code ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Player')">{{ $payment->player?->name ?? $payment->registration?->player_name ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

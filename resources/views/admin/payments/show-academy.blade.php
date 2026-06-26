<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.payments.index', ['tab' => 'academy'])"
    :back-label="__('Payments')"
    :subtitle="__('Academy payment')"
  />

  <x-admin.page-content>
    <x-admin.card>
      <dl class="pb-admin-detail-grid">
        <x-admin.detail-field :label="__('Reference')"><span class="font-mono">{{ $payment->reference }}</span></x-admin.detail-field>
        <x-admin.detail-field :label="__('Status')">{{ $payment->status }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Amount')"><span class="font-semibold">{{ format_currency($payment->amount) }}</span></x-admin.detail-field>
        <x-admin.detail-field :label="__('Type')">{{ $payment->type }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Player')">{{ $payment->player?->name ?? '—' }}</x-admin.detail-field>
        <x-admin.detail-field :label="__('Season')">{{ $payment->season?->name ?? '—' }}</x-admin.detail-field>
      </dl>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>

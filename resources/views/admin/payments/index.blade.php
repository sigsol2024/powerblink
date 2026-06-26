<x-app-layout>
  <x-admin.page-content>
    <div class="flex flex-wrap gap-2 mb-5">
      <a href="{{ route('admin.payments.index', ['tab' => 'registration']) }}" class="inline-flex items-center min-h-11 px-4 py-2 rounded-full text-sm font-semibold {{ $tab === 'registration' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ __('Registration payments') }}</a>
      <a href="{{ route('admin.payments.index', ['tab' => 'academy']) }}" class="inline-flex items-center min-h-11 px-4 py-2 rounded-full text-sm font-semibold {{ $tab === 'academy' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">{{ __('Academy payments') }}</a>
    </div>

    @php $payments = $tab === 'registration' ? $registrationPayments : $academyPayments; @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      @include('partials.powerblink.dashboard-stat-card', ['label' => __('Listed'), 'value' => number_format($payments->total()), 'accent' => 'navy'])
      @include('partials.powerblink.dashboard-stat-card', ['label' => __('This page'), 'value' => number_format($payments->count()), 'accent' => 'secondary'])
      @include('partials.powerblink.dashboard-stat-card', ['label' => __('Tab'), 'value' => $tab === 'registration' ? __('Registration') : __('Academy'), 'accent' => 'gold'])
    </div>

    <x-admin.card variant="table" class="overflow-hidden">
      <div class="overflow-x-auto">
      <table class="pb-admin-table min-w-full text-sm">
        <thead><tr>
          <th>{{ __('Reference') }}</th><th>{{ __('Player') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th></th>
        </tr></thead>
        <tbody>
          @forelse ($payments as $payment)
            <tr>
              <td class="font-mono text-xs">{{ $payment->reference }}</td>
              <td>{{ $payment->registration?->player_name ?? $payment->player?->name }}</td>
              <td class="font-semibold">{{ format_currency($payment->amount) }}</td>
              <td><x-admin.status-pill :variant="$payment->status">{{ $payment->status }}</x-admin.status-pill></td>
              <td class="text-right">
                <a href="{{ $tab === 'registration' ? route('admin.payments.registration.show', $payment) : route('admin.payments.academy.show', $payment) }}" class="text-secondary font-semibold min-h-11 inline-flex items-center">{{ __('View') }}</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="p-8 text-center text-on-surface-variant">{{ __('No payments found.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </x-admin.card>
    <div class="mt-4">{{ $payments->links() }}</div>
  </x-admin.page-content>
</x-app-layout>

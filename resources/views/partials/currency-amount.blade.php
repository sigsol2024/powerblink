@php
  $amount = (float) ($amount ?? 0);
  $decimals = (int) ($decimals ?? 0);
@endphp
<span data-currency-amount="{{ $amount }}" data-currency-decimals="{{ $decimals }}">{{ \App\Support\CurrencyDisplay::formatForSite($amount, $decimals) }}</span>

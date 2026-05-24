@extends('emails.layouts.branded')

@section('content')
  <h2 style="margin:0 0 16px;font-size:20px;">{{ __('Thank you for your order') }}</h2>
  <p>{{ __('Hi :name,', ['name' => $order->customer_name]) }}</p>
  <p>{{ __('We have received your payment. Your order details are below.') }}</p>
  <p><strong>{{ __('Order number') }}:</strong> {{ $order->order_number }}</p>
  <p><strong>{{ __('Total') }}:</strong> {{ \App\Support\Money::formatKobo($order->total) }}</p>
  @if($order->items->isNotEmpty())
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:16px 0;border-collapse:collapse;">
      @foreach($order->items as $item)
        <tr>
          <td style="padding:8px 0;border-bottom:1px solid #e4e4e7;">{{ $item->name }} × {{ $item->qty }}</td>
          <td style="padding:8px 0;border-bottom:1px solid #e4e4e7;text-align:right;">{{ \App\Support\Money::formatKobo($item->line_total) }}</td>
        </tr>
      @endforeach
    </table>
  @endif
  <p style="margin-top:24px;">
    <a href="{{ $orderUrl }}" style="display:inline-block;background:#1e2229;color:#ffb129;padding:12px 20px;text-decoration:none;border-radius:6px;font-weight:600;">{{ __('View your order') }}</a>
  </p>
@endsection

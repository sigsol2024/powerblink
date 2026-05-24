<h2 style="margin:0 0 16px;font-size:20px;">{{ __('New paid order') }}</h2>
<p><strong>{{ __('Order number') }}:</strong> {{ $order->order_number }}</p>
<p><strong>{{ __('Customer') }}:</strong> {{ $order->customer_name }} &lt;{{ $order->customer_email }}&gt;</p>
@if($order->customer_phone)
  <p><strong>{{ __('Phone') }}:</strong> {{ $order->customer_phone }}</p>
@endif
<p><strong>{{ __('Total') }}:</strong> {{ \App\Support\Money::formatKobo($order->total) }}</p>
<p><strong>{{ __('Shipping') }}:</strong><br>
  {{ $order->shipping_address_line1 }}<br>
  @if($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
  {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif
  @if($order->shipping_postal_code) {{ $order->shipping_postal_code }}@endif<br>
  {{ $order->shipping_country }}
</p>
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
  <a href="{{ $adminUrl }}" style="display:inline-block;background:#1e2229;color:#ffb129;padding:12px 20px;text-decoration:none;border-radius:6px;font-weight:600;">{{ __('View in admin') }}</a>
</p>

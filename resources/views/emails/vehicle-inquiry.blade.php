<h2>New vehicle inquiry</h2>
<p><strong>Listing:</strong> {{ $vehicle->title }}</p>
@if ($listingOwner ?? null)
  <p><strong>Listing owner:</strong> {{ $listingOwner->name ?? '—' }} @if(! empty($listingOwner->email))&lt;{{ $listingOwner->email }}&gt;@endif</p>
@endif
<p><strong>From:</strong> {{ $senderName }} &lt;{{ $senderEmail }}&gt;@if(! empty(trim((string) ($senderPhone ?? '')))) — {{ trim((string) $senderPhone) }}@endif</p>
<p><strong>Message:</strong></p>
<p>{!! nl2br(e($body)) !!}</p>
<p><a href="{{ $listingUrl }}">View listing</a></p>

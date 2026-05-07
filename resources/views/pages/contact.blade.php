@extends('layouts.site')

@php
  $site = $site ?? [];

  $heroImg = \App\Support\VehicleImageUrl::url($sections['hero_image'] ?? 'asset/images/media/contact-hero-bg.jpg');

  $mapAddress = $sections['map_address'] ?? '';
  $mapEmbedUrl = $sections['map_embed_url'] ?? '';
  $mapFallbackImage = \App\Support\VehicleImageUrl::url($sections['map_fallback_image'] ?? 'asset/images/media/contact-map.jpg');
  $googleMapsApiKey = config('services.google.maps_api_key', '');

  $mapAddressPlain = trim(strip_tags(str_replace(['<br/>', '<br />', '<br>'], ', ', $sections['sales_address'] ?? '')));
  if (trim($mapAddress) !== '') {
      $mapAddressPlain = trim($mapAddress);
  }

  $mapUrl = '';
  if (trim($mapEmbedUrl) !== '') {
      $mapUrl = trim($mapEmbedUrl);
  } elseif (trim($googleMapsApiKey) !== '' && $mapAddressPlain !== '') {
      $mapUrl = 'https://www.google.com/maps/embed/v1/place?key=' . urlencode($googleMapsApiKey) . '&q=' . urlencode($mapAddressPlain);
  } elseif ($mapAddressPlain !== '') {
      $mapUrl = 'https://maps.google.com/maps?q=' . urlencode($mapAddressPlain) . '&t=&z=13&ie=UTF8&iwloc=&output=embed';
  }
@endphp

@section('content')
<style>
    .hero-section {
        background-image: url('{{ $heroImg }}');
        background-size: cover;
        background-position: center;
    }
</style>

<main>
  @if(session('status'))
    <div class="max-w-5xl mx-auto px-4 pt-8"><div class="p-4 bg-green-100 text-green-900 rounded">{{ session('status') }}</div></div>
  @endif

  @if($errors->any())
    <div class="max-w-5xl mx-auto px-4 pt-8"><div class="p-4 bg-red-100 text-red-900 rounded"><ul class="list-disc pl-5">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div></div>
  @endif

<!-- Hero & Contact Form Section -->
<section class="relative min-h-[700px] flex items-center justify-center py-20 px-4 hero-section">
<div class="absolute inset-0 bg-black/30"></div>
<div class="relative w-full max-w-5xl bg-white shadow-2xl rounded-sm p-10 md:p-16">
<h1 class="font-headline text-4xl md:text-5xl font-black mb-12 tracking-tight text-slate-900">{{ $sections['heading'] ?? 'CONTACT US' }}</h1>
<form class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6" method="post" action="{{ route('contact.submit') }}">
  @csrf
<div class="space-y-2">
<label class="text-[13px] font-bold text-slate-900">First Name*</label>
<input name="first_name" value="{{ old('first_name') }}" required class="w-full bg-[#ebf1f7] border-none p-4 rounded-sm text-sm focus:ring-1 focus:ring-[#4e77ed]" placeholder="Enter your first name" type="text"/>
</div>
<div class="space-y-2 md:row-span-3">
<label class="text-[13px] font-bold text-slate-900">Comment</label>
<textarea name="message" required class="w-full bg-[#ebf1f7] border-none p-4 rounded-sm text-sm focus:ring-1 focus:ring-[#4e77ed] h-[calc(100%-28px)]" placeholder="Enter your message..." rows="8">{{ old('message') }}</textarea>
</div>
<div class="space-y-2">
<label class="text-[13px] font-bold text-slate-900">Last Name*</label>
<input name="last_name" value="{{ old('last_name') }}" required class="w-full bg-[#ebf1f7] border-none p-4 rounded-sm text-sm focus:ring-1 focus:ring-[#4e77ed]" placeholder="Enter your last name" type="text"/>
</div>
<div class="space-y-2">
<label class="text-[13px] font-bold text-slate-900">Email*</label>
<input name="email" value="{{ old('email') }}" required class="w-full bg-[#ebf1f7] border-none p-4 rounded-sm text-sm focus:ring-1 focus:ring-[#4e77ed]" placeholder="email@domain.com" type="email"/>
</div>
<div class="space-y-2">
<label class="text-[13px] font-bold text-slate-900">Phone</label>
<input name="phone" value="{{ old('phone') }}" class="w-full bg-[#ebf1f7] border-none p-4 rounded-sm text-sm focus:ring-1 focus:ring-[#4e77ed]" placeholder="Phone number" type="tel"/>
</div>
<div class="md:col-span-2 flex flex-col md:flex-row items-center justify-between gap-6 mt-8">
<label class="flex items-center gap-3 cursor-pointer">
<input name="newsletter_subscribe" value="1" class="w-4 h-4 border-none bg-[#ebf1f7] text-[#4e77ed] rounded-sm focus:ring-0" type="checkbox" @checked(old('newsletter_subscribe'))/>
<span class="text-[13px] text-slate-500">{{ $site['newsletter_note'] ?? __('Subscribe and get latest updates and offers by email.') }}</span>
</label>
<button class="bg-[#4e77ed] text-white px-14 py-4 font-bold text-sm rounded shadow-lg hover:brightness-110 transition-all uppercase tracking-widest" type="submit">
                        SUBMIT
                    </button>
</div>
</form>
</div>
</section>

<!-- Info Section with Tabs and Map -->
<section class="bg-white py-24">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12">
<!-- Left Column: Tabs & Info -->
<div class="lg:col-span-4 space-y-8">
<!-- Tabbed Interface -->
<div class="flex bg-slate-900 text-white font-bold text-[13px] uppercase tracking-wider">
<button class="flex-1 py-4 bg-white text-slate-900 border-t-2 border-[#f9a825]" data-contact-tab="parts">PARTS</button>
<button class="flex-1 py-4 hover:bg-slate-800 transition-colors" data-contact-tab="sales">SALES</button>
<button class="flex-1 py-4 hover:bg-slate-800 transition-colors" data-contact-tab="renting">RENTING</button>
</div>

@foreach(['parts','sales','renting'] as $panel)
@php
  $panelAddress = $sections[$panel . '_address'] ?? '';
  $panelPhone = $sections[$panel . '_phone'] ?? '';
  $panelHours = $sections[$panel . '_hours'] ?? '';
  $panelTitle = ucfirst($panel);
@endphp
<div class="space-y-10 pt-4 {{ $loop->first ? '' : 'hidden' }}" data-contact-panel="{{ $panel }}">
<div class="flex items-start gap-6">
<div class="w-12 h-12 shrink-0 border-2 border-[#f9a825] rounded-full flex items-center justify-center text-[#f9a825]">
<span class="material-symbols-outlined text-2xl">location_on</span>
</div>
<div>
<h4 class="font-bold text-[14px] text-slate-900 mb-1 uppercase">Address</h4>
<p class="text-slate-500 text-[14px] leading-relaxed">{!! nl2br(e($panelAddress)) !!}</p>
</div>
</div>
<div class="flex items-start gap-6">
<div class="w-12 h-12 shrink-0 border-2 border-[#f9a825] rounded-full flex items-center justify-center text-[#f9a825]">
<span class="material-symbols-outlined text-2xl">call</span>
</div>
<div>
<h4 class="font-bold text-[14px] text-slate-900 mb-1 uppercase">{{ $panel === 'parts' ? __('Telephone') : ($panelTitle . ' ' . __('Phone')) }}</h4>
<p class="text-slate-500 text-[14px]">{{ $panelPhone }}</p>
</div>
</div>
<div class="flex items-start gap-6">
<div class="w-12 h-12 shrink-0 border-2 border-[#f9a825] rounded-full flex items-center justify-center text-[#f9a825]">
<span class="material-symbols-outlined text-2xl">schedule</span>
</div>
<div>
<h4 class="font-bold text-[14px] text-slate-900 mb-1 uppercase">{{ $panelTitle }} Hours</h4>
<div class="text-slate-500 text-[14px] space-y-1">
{!! nl2br(e($panelHours)) !!}
</div>
</div>
</div>
</div>
@endforeach
</div>

<!-- Right Column: Map -->
<div class="lg:col-span-8 h-[550px] relative rounded shadow-lg overflow-hidden border border-gray-100">
  @if($mapUrl !== '')
    <iframe class="absolute inset-0 w-full h-full border-0" src="{{ $mapUrl }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map Location"></iframe>
  @else
    <img alt="Map" class="w-full h-full object-cover" src="{{ $mapFallbackImage }}"/>
  @endif
<div class="absolute inset-0 pointer-events-none border-[12px] border-white/10"></div>
</div>
</div>
</section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('[data-contact-tab]');
    const panels = document.querySelectorAll('[data-contact-panel]');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-contact-tab');
            
            // Reset all tabs
            tabs.forEach(t => {
                t.className = 'flex-1 py-4 hover:bg-slate-800 transition-colors';
            });
            // Set active tab
            tab.className = 'flex-1 py-4 bg-white text-slate-900 border-t-2 border-[#f9a825]';

            // Hide all panels
            panels.forEach(p => p.classList.add('hidden'));
            // Show target panel
            const targetPanel = document.querySelector(`[data-contact-panel="${target}"]`);
            if(targetPanel) targetPanel.classList.remove('hidden');
        });
    });
});
</script>
@endsection
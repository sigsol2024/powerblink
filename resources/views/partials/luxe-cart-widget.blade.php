{{-- Floating cart summary (left side) — visible when bag has items --}}
<div
  id="luxe-cart-widget"
  class="luxe-store fixed right-4 md:right-6 bottom-6 z-[55] hidden"
  data-luxe-cart-widget
  aria-hidden="true"
>
  <button
    type="button"
    class="flex items-center gap-3 px-4 py-3 rounded-full bg-primary text-on-primary shadow-lg hover:opacity-95 transition-opacity border-0 cursor-pointer"
    data-cart-toggle
    aria-label="{{ __('Open shopping bag') }}"
  >
    <x-icon name="shopping-bag" class="w-5 h-5 shrink-0" />
    <span class="flex flex-col items-start leading-tight text-left">
      <span class="text-[10px] font-bold uppercase tracking-wider opacity-90" data-luxe-cart-widget-count>0 {{ __('items') }}</span>
      <span class="text-sm font-bold" data-luxe-cart-widget-total>{{ format_currency(0) }}</span>
    </span>
  </button>
</div>

<div
  id="luxe-cart-toast"
  class="luxe-store fixed top-24 left-1/2 -translate-x-1/2 z-[70] max-w-md w-[calc(100%-2rem)] hidden pointer-events-none"
  role="status"
  aria-live="polite"
  data-luxe-cart-toast
>
  <div class="bg-primary text-on-primary px-6 py-4 shadow-xl text-center font-body-md text-sm">
    <p class="font-semibold" data-luxe-cart-toast-message></p>
  </div>
</div>

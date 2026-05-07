@php
  $site = $site ?? [];
  $brandName = ! empty(trim((string) ($site['site_display_name'] ?? ''))) ? trim((string) $site['site_display_name']) : config('app.name', 'Site');
  $rawPhone = trim((string) ($site['dealer_sales_phone'] ?? ''));
  $waDigits = preg_replace('/\D/', '', $rawPhone);
@endphp

@if (!empty($waDigits))
  <div
    id="waWidget"
    class="fixed bottom-5 left-5 z-[95]"
    data-wa-number="{{ $waDigits }}"
    data-wa-brand="{{ e($brandName) }}"
  >
    <button
      type="button"
      id="waBubble"
      class="group inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] shadow-2xl ring-1 ring-black/10 transition hover:shadow-[0_20px_50px_-20px_rgba(0,0,0,0.8)] focus:outline-none focus:ring-4 focus:ring-[#25D366]/30"
      aria-label="{{ __('Chat on WhatsApp') }}"
      aria-haspopup="dialog"
      aria-expanded="false"
    >
      {{-- WhatsApp mark (inline SVG) --}}
      <svg viewBox="0 0 32 32" class="h-7 w-7 text-white drop-shadow" fill="currentColor" aria-hidden="true">
        <path d="M19.11 17.37c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.15-.42-2.19-1.34-.81-.72-1.36-1.6-1.52-1.88-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.48-.84-2.03-.22-.53-.45-.46-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.95.93-.95 2.27 0 1.34.98 2.63 1.12 2.82.14.18 1.93 2.95 4.68 4.13.65.28 1.16.45 1.56.58.65.21 1.25.18 1.72.11.52-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32zM16.02 27.2h-.01c-1.86 0-3.69-.5-5.29-1.44l-.38-.23-3.95 1.04 1.05-3.85-.25-.4a11.06 11.06 0 0 1-1.71-5.9c0-6.1 4.97-11.06 11.08-11.06 2.96 0 5.74 1.15 7.84 3.23a10.98 10.98 0 0 1 3.25 7.82c0 6.1-4.97 11.06-11.08 11.06zm9.41-20.47A13.2 13.2 0 0 0 16.02 3.2C8.74 3.2 2.82 9.11 2.82 16.4c0 2.29.6 4.52 1.75 6.5L2.7 29.74l6.98-1.83a13.13 13.13 0 0 0 6.33 1.62h.01c7.28 0 13.2-5.91 13.2-13.2 0-3.52-1.37-6.83-3.79-9.6z"/>
      </svg>
    </button>

    <div
      id="waPopup"
      class="pointer-events-none absolute bottom-[4.25rem] left-0 w-[min(20.5rem,calc(100vw-2.5rem))] translate-y-2 opacity-0 transition-all duration-200"
      role="dialog"
      aria-label="{{ __('WhatsApp chat') }}"
      aria-hidden="true"
    >
      <div class="overflow-hidden rounded-2xl border border-black/10 bg-white shadow-2xl ring-1 ring-black/5">
        <div class="flex items-start gap-3 bg-[#111316] px-4 py-3">
          <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-white">
            <svg viewBox="0 0 32 32" class="h-5 w-5" fill="currentColor" aria-hidden="true">
              <path d="M19.11 17.37c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.06-.16.18-.32.2-.59.07-.27-.14-1.15-.42-2.19-1.34-.81-.72-1.36-1.6-1.52-1.88-.16-.27-.02-.42.12-.56.12-.12.27-.32.41-.48.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.48-.07-.14-.61-1.48-.84-2.03-.22-.53-.45-.46-.61-.46h-.52c-.18 0-.48.07-.73.34-.25.27-.95.93-.95 2.27 0 1.34.98 2.63 1.12 2.82.14.18 1.93 2.95 4.68 4.13.65.28 1.16.45 1.56.58.65.21 1.25.18 1.72.11.52-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"/>
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-extrabold tracking-tight text-white">{{ $brandName }}</div>
            <div class="mt-0.5 text-xs text-white/70">{{ __('How can we help you today?') }}</div>
          </div>
          <button type="button" id="waClose" class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-white/70 hover:bg-white/10 hover:text-white" aria-label="{{ __('Close') }}">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="space-y-3 px-4 py-4">
          <textarea id="waMessage" rows="3" class="w-full resize-none rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-[#25D366] focus:ring-[#25D366]" placeholder="{{ __('Type your message…') }}"></textarea>
          <button type="button" id="waSend" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:brightness-95 focus:outline-none focus:ring-4 focus:ring-[#25D366]/30">
            <span>{{ __('Send') }}</span>
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13"/><path stroke-linecap="round" stroke-linejoin="round" d="M22 2l-7 20-4-9-9-4z"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const root = document.getElementById('waWidget');
      if (!root) return;
      const number = (root.getAttribute('data-wa-number') || '').trim();
      if (!number) return;

      const bubble = document.getElementById('waBubble');
      const popup = document.getElementById('waPopup');
      const closeBtn = document.getElementById('waClose');
      const sendBtn = document.getElementById('waSend');
      const messageEl = document.getElementById('waMessage');
      if (!bubble || !popup || !closeBtn || !sendBtn || !messageEl) return;

      let open = false;
      function setOpen(next) {
        open = !!next;
        bubble.setAttribute('aria-expanded', open ? 'true' : 'false');
        popup.setAttribute('aria-hidden', open ? 'false' : 'true');
        popup.classList.toggle('pointer-events-none', !open);
        popup.classList.toggle('opacity-0', !open);
        popup.classList.toggle('translate-y-2', !open);
        popup.classList.toggle('opacity-100', open);
        popup.classList.toggle('translate-y-0', open);
        if (open) {
          setTimeout(() => messageEl.focus(), 50);
        }
      }

      bubble.addEventListener('click', () => setOpen(!open));
      closeBtn.addEventListener('click', () => setOpen(false));
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
      document.addEventListener('click', (e) => {
        if (!open) return;
        if (root.contains(e.target)) return;
        setOpen(false);
      });

      function go() {
        const msg = (messageEl.value || '').trim();
        const url = 'https://wa.me/' + encodeURIComponent(number) + (msg ? ('?text=' + encodeURIComponent(msg)) : '');
        window.location.href = url;
      }
      sendBtn.addEventListener('click', go);
      messageEl.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') go();
      });
    })();
  </script>
@endif


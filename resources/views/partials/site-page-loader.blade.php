@php
  $site = $site ?? [];
  $brandName = \App\Support\SiteBrand::displayName($site);
  $loaderLogoPath = trim((string) ($site['logo_path'] ?? ''));
  if ($loaderLogoPath === '') {
    $loaderLogoPath = trim((string) ($site['logo_light_path'] ?? ''));
  }
  if ($loaderLogoPath === '') {
    $loaderLogoPath = trim((string) ($site['logo_url'] ?? ''));
  }
  $loaderLogoUrl = $loaderLogoPath !== '' ? \App\Support\MediaImageUrl::url($loaderLogoPath) : null;
@endphp
<div
  id="site-page-loader"
  class="site-page-loader"
  role="status"
  aria-live="polite"
  aria-label="{{ __('Loading') }}"
  style="position:fixed;top:0;right:0;bottom:0;left:0;width:100%;height:100%;z-index:2147483646;display:flex;align-items:center;justify-content:center;background:#fff;margin:0;padding:0;"
>
  <div
    class="site-page-loader__inner"
    style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;margin:0;padding:1.5rem;box-sizing:border-box;"
  >
    @if ($loaderLogoUrl)
      <img
        src="{{ $loaderLogoUrl }}"
        alt="{{ $brandName }}"
        class="site-page-loader__logo"
        width="280"
        height="100"
        decoding="async"
        style="display:block;margin:0 auto;max-width:min(72vw,280px);max-height:100px;width:auto;height:auto;object-fit:contain;"
      />
    @else
      <p class="site-page-loader__brand" style="margin:0 auto;text-align:center;">{{ $brandName }}</p>
    @endif
  </div>
</div>
<script>
  (function () {
    var loader = document.getElementById('site-page-loader');
    var root = document.documentElement;
    if (!loader) return;

    var finished = false;
    var started = Date.now();
    var minVisibleMs = 550;
    var maxVisibleMs = 5000;

    function dismiss() {
      if (finished) return;
      finished = true;
      loader.classList.add('is-hiding');
      window.setTimeout(function () {
        loader.remove();
        root.classList.remove('site-is-loading');
      }, 300);
    }

    function scheduleDismiss() {
      var elapsed = Date.now() - started;
      var delay = Math.max(0, minVisibleMs - elapsed);
      window.setTimeout(dismiss, delay);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', scheduleDismiss, { once: true });
    } else {
      scheduleDismiss();
    }

    window.setTimeout(dismiss, maxVisibleMs);
  })();
</script>

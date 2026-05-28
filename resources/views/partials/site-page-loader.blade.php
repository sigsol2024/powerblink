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
  $loaderLogoUrl = $loaderLogoPath !== '' ? \App\Support\VehicleImageUrl::url($loaderLogoPath) : null;
@endphp
<div id="site-page-loader" class="site-page-loader" role="status" aria-live="polite" aria-label="{{ __('Loading') }}">
  <div class="site-page-loader__inner">
    @if ($loaderLogoUrl)
      <img
        src="{{ $loaderLogoUrl }}"
        alt="{{ $brandName }}"
        class="site-page-loader__logo"
        width="220"
        height="80"
        decoding="async"
      />
    @else
      <p class="site-page-loader__brand">{{ $brandName }}</p>
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
    var minVisibleMs = 220;
    var maxVisibleMs = 4500;

    function dismiss() {
      if (finished) return;
      finished = true;
      loader.classList.add('is-hiding');
      window.setTimeout(function () {
        loader.remove();
        root.classList.remove('site-is-loading');
      }, 260);
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

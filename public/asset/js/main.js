(function () {
  'use strict';

  var menuToggle = document.querySelector('[data-mobile-menu-toggle]');
  var menuPanel = document.querySelector('[data-mobile-menu-panel]');
  var menuOverlay = document.querySelector('[data-mobile-menu-overlay]');
  var menuClose = document.querySelector('[data-mobile-menu-close]');

  function closeMobileInventoryAccordion() {
    var panel = document.querySelector('[data-mobile-inventory-panel]');
    var btn = document.querySelector('[data-mobile-inventory-toggle]');
    var chev = document.querySelector('[data-mobile-inventory-chevron]');
    if (panel) panel.classList.add('hidden');
    if (btn) btn.setAttribute('aria-expanded', 'false');
    if (chev) chev.classList.remove('rotate-180');
  }

  function closeMobileFaqAccordion() {
    var panel = document.querySelector('[data-mobile-faq-panel]');
    var btn = document.querySelector('[data-mobile-faq-toggle]');
    var chev = document.querySelector('[data-mobile-faq-chevron]');
    if (panel) panel.classList.add('hidden');
    if (btn) btn.setAttribute('aria-expanded', 'false');
    if (chev) chev.classList.remove('rotate-180');
  }

  function setMenu(open) {
    if (!menuPanel || !menuOverlay) return;
    menuPanel.classList.toggle('is-open', open);
    menuOverlay.classList.toggle('hidden', !open);
    document.body.classList.toggle('mobile-menu-open', open);
    if (!open) {
      closeMobileInventoryAccordion();
      closeMobileFaqAccordion();
    }
  }

  if (menuToggle && menuPanel && menuOverlay) {
    menuToggle.addEventListener('click', function () { setMenu(true); });
    menuOverlay.addEventListener('click', function () { setMenu(false); });
    if (menuClose) {
      menuClose.addEventListener('click', function () { setMenu(false); });
    }
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setMenu(false);
    });
  }

  function bindHeaderScrollState() {
    var header = document.querySelector('[data-site-header]');
    if (!header) return;

    function sync() {
      var scrolled = window.scrollY > 10;
      header.classList.toggle('is-scrolled', scrolled);
    }

    sync();
    window.addEventListener('scroll', sync, { passive: true });
  }

  /** Desktop inventory mega panel: hover + Escape (sigsol-style flyout). */
  function bindHeaderInventoryDropdown() {
    var root = document.querySelector('[data-header-inventory-dropdown]');
    var panel = document.querySelector('[data-header-inventory-panel]');
    if (!root || !panel) return;

    var closeTimer = null;
    var trigger = root.querySelector('[data-header-inventory-trigger]');

    function open() {
      if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
      }
      panel.classList.remove('hidden');
      if (trigger) trigger.setAttribute('aria-expanded', 'true');
    }

    function scheduleClose() {
      if (closeTimer) clearTimeout(closeTimer);
      closeTimer = setTimeout(function () {
        panel.classList.add('hidden');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        closeTimer = null;
      }, 140);
    }

    root.addEventListener('mouseenter', open);
    root.addEventListener('mouseleave', scheduleClose);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        panel.classList.add('hidden');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /** Desktop FAQ mega panel (same hover pattern as Inventory). */
  function bindHeaderFaqDropdown() {
    var root = document.querySelector('[data-header-faq-dropdown]');
    var panel = document.querySelector('[data-header-faq-panel]');
    if (!root || !panel) return;

    var closeTimer = null;
    var trigger = root.querySelector('[data-header-faq-trigger]');

    function open() {
      if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
      }
      panel.classList.remove('hidden');
      if (trigger) trigger.setAttribute('aria-expanded', 'true');
    }

    function scheduleClose() {
      if (closeTimer) clearTimeout(closeTimer);
      closeTimer = setTimeout(function () {
        panel.classList.add('hidden');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
        closeTimer = null;
      }, 140);
    }

    root.addEventListener('mouseenter', open);
    root.addEventListener('mouseleave', scheduleClose);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        panel.classList.add('hidden');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /** Mobile sidebar: collapsible FAQ knowledge base. */
  function bindMobileFaqAccordion() {
    var btn = document.querySelector('[data-mobile-faq-toggle]');
    var panel = document.querySelector('[data-mobile-faq-panel]');
    var chev = document.querySelector('[data-mobile-faq-chevron]');
    if (!btn || !panel) return;

    btn.addEventListener('click', function () {
      panel.classList.toggle('hidden');
      var expanded = !panel.classList.contains('hidden');
      btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      if (chev) chev.classList.toggle('rotate-180', expanded);
    });
  }

  /** Mobile sidebar: collapsible Inventory (closed by default). */
  function bindMobileInventoryAccordion() {
    var btn = document.querySelector('[data-mobile-inventory-toggle]');
    var panel = document.querySelector('[data-mobile-inventory-panel]');
    var chev = document.querySelector('[data-mobile-inventory-chevron]');
    if (!btn || !panel) return;

    btn.addEventListener('click', function () {
      panel.classList.toggle('hidden');
      var expanded = !panel.classList.contains('hidden');
      btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      if (chev) chev.classList.toggle('rotate-180', expanded);
    });
  }

  function bindContactTabs() {
    var tabButtons = document.querySelectorAll('[data-contact-tab]');
    if (!tabButtons.length) return;

    function activateTab(name) {
      tabButtons.forEach(function (tab) {
        var active = tab.getAttribute('data-contact-tab') === name;
        tab.classList.toggle('bg-white', active);
        tab.classList.toggle('text-slate-900', active);
        tab.classList.toggle('border-t-2', active);
        tab.classList.toggle('border-brand_orange', active);
      });

      document.querySelectorAll('[data-contact-panel]').forEach(function (panel) {
        panel.classList.toggle('hidden', panel.getAttribute('data-contact-panel') !== name);
      });
    }

    tabButtons.forEach(function (tab) {
      tab.addEventListener('click', function () {
        activateTab(tab.getAttribute('data-contact-tab'));
      });
    });

    activateTab(tabButtons[0].getAttribute('data-contact-tab'));
  }

  function bindHomeStatsCountUp() {
    var root = document.querySelector('[data-home-stats-root]');
    if (!root) return;
    var els = root.querySelectorAll('[data-count-up]');
    if (!els.length) return;

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    function easeOutCubic(t) {
      return 1 - Math.pow(1 - t, 3);
    }

    function formatStat(n) {
      return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function animateEl(el) {
      var rawT = el.getAttribute('data-target') || '0';
      var target = parseInt(rawT, 10);
      if (isNaN(target) || target < 0) target = 0;
      var from = 0;
      el.textContent = '0';
      var start = performance.now();
      var duration = 1100;
      function frame(now) {
        var p = Math.min(1, (now - start) / duration);
        var t = easeOutCubic(p);
        var v = Math.round(from + (target - from) * t);
        el.textContent = formatStat(v);
        if (p < 1) requestAnimationFrame(frame);
      }
      requestAnimationFrame(frame);
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        io.disconnect();
        els.forEach(function (el) {
          animateEl(el);
        });
      });
    }, { threshold: 0.3, rootMargin: '0px 0px -10% 0px' });
    io.observe(root);
  }

  /**
   * Listing cards: 2D hover/touch zones (row × column) map to gallery images — horizontal and
   * vertical movement both change the photo, similar to Motors interactive listings.
   */
  function bindListingHoverGalleries() {
    var wraps = document.querySelectorAll('[data-vehicle-hover-gallery]');
    if (!wraps.length) return;

    var reduceMotion =
      window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    wraps.forEach(function (wrap) {
      var raw = wrap.getAttribute('data-images') || '[]';
      var urls;
      try {
        urls = JSON.parse(raw);
      } catch (e) {
        return;
      }
      if (!Array.isArray(urls) || urls.length < 2) return;

      var img = wrap.querySelector('[data-vehicle-hover-main]');
      if (!img) return;

      var n = urls.length;
      var dots = wrap.querySelectorAll('[data-vehicle-hover-dot]');
      var current = 0;

      urls.forEach(function (u) {
        var pre = new Image();
        pre.src = u;
      });

      function setIndex(i) {
        i = Math.max(0, Math.min(n - 1, i));
        if (i === current) return;
        current = i;
        img.src = urls[i];
        dots.forEach(function (dot, di) {
          dot.setAttribute('data-active', di === i ? '1' : '0');
        });
      }

      function clientXYFromEvent(e) {
        if (e.touches && e.touches[0]) {
          return { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }
        if (e.changedTouches && e.changedTouches[0]) {
          return { x: e.changedTouches[0].clientX, y: e.changedTouches[0].clientY };
        }
        return { x: e.clientX, y: e.clientY };
      }

      function indexFromPoint(x, y, w, h) {
        var cols = Math.ceil(Math.sqrt(n));
        var rows = Math.ceil(n / cols);
        var cx = Math.min(cols - 1, Math.max(0, Math.floor((x / w) * cols)));
        var cy = Math.min(rows - 1, Math.max(0, Math.floor((y / h) * rows)));
        var idx = cy * cols + cx;
        return Math.min(n - 1, idx);
      }

      function onMove(e) {
        if (reduceMotion) return;
        var rect = wrap.getBoundingClientRect();
        if (rect.width <= 0 || rect.height <= 0) return;
        var p = clientXYFromEvent(e);
        var x = p.x - rect.left;
        var y = p.y - rect.top;
        setIndex(indexFromPoint(x, y, rect.width, rect.height));
      }

      function reset() {
        setIndex(0);
      }

      wrap.addEventListener('mousemove', onMove);
      wrap.addEventListener('mouseleave', reset);
      wrap.addEventListener('touchstart', onMove, { passive: true });
      wrap.addEventListener('touchmove', onMove, { passive: true });
      wrap.addEventListener('touchend', reset);
      wrap.addEventListener('touchcancel', reset);
    });
  }

  /**
   * Simple scroll-snap carousel: About page gallery/testimonials.
   * Markup:
   * - root: [data-simple-carousel]
   * - viewport (overflow hidden): [data-carousel-viewport]
   * - track: [data-carousel-track]
   * - slides: children with [data-carousel-slide]
   * - prev/next buttons: [data-carousel-prev], [data-carousel-next]
   * - dots container: [data-carousel-dots] (dots will be generated)
   */
  function bindSimpleCarousels() {
    var roots = document.querySelectorAll('[data-simple-carousel]');
    if (!roots.length) return;

    function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

    roots.forEach(function (root) {
      var type = root.getAttribute('data-carousel-type') || '';
      var loop = root.getAttribute('data-carousel-loop') === '1';
      var viewport = root.querySelector('[data-carousel-viewport]') || root;
      var track = root.querySelector('[data-carousel-track]');
      if (!track) return;
      var slides = track.querySelectorAll('[data-carousel-slide]');
      if (!slides.length) return;

      var prev = root.querySelector('[data-carousel-prev]');
      var next = root.querySelector('[data-carousel-next]');
      var dotsWrap = root.querySelector('[data-carousel-dots]');
      var index = 0;

      function px(v) { return parseFloat(String(v || '0').replace('px', '')) || 0; }

      function metrics() {
        var first = slides[0];
        var slideW = first.getBoundingClientRect().width || first.offsetWidth || 0;
        var gap = px(window.getComputedStyle(track).gap);
        var viewW = viewport.getBoundingClientRect().width || viewport.offsetWidth || 0;
        
        var perView = 1;
        if (type === 'gallery') {
          if (window.innerWidth >= 1024) perView = 4;
          else if (window.innerWidth >= 640) perView = 3;
          else perView = 1;
        } else if (type === 'testimonials') {
          perView = 1;
        } else if (type === 'gallery-pages') {
          perView = 1;
        } else if (type === 'similar-cars') {
          if (window.innerWidth >= 1280) perView = 4;
          else if (window.innerWidth >= 1024) perView = 3;
          else if (window.innerWidth >= 640) perView = 2;
          else perView = 1;
        }

        var maxIndex = Math.max(0, slides.length - perView);
        return { slideW: slideW, gap: gap, perView: perView, maxIndex: maxIndex };
      }

      function buildDots(count) {
        if (!dotsWrap) return [];
        dotsWrap.innerHTML = '';
        var dots = [];
        for (var i = 0; i < count; i++) {
          var b = document.createElement('button');
          b.type = 'button';
          b.setAttribute('data-index', String(i));
          b.setAttribute('data-active', i === 0 ? '1' : '0');
          b.setAttribute('aria-label', 'Go to slide');
          b.addEventListener('click', function (e) {
            e.preventDefault();
            var di = parseInt(this.getAttribute('data-index') || '0', 10);
            if (!isNaN(di)) goTo(di);
          });
          dotsWrap.appendChild(b);
          dots.push(b);
        }
        return dots;
      }

      var dots = [];

      function setActive(i, maxIndex) {
        if (!loop) {
          if (prev) prev.disabled = i <= 0;
          if (next) next.disabled = i >= maxIndex;
        }
        dots.forEach(function (d, di) {
          d.setAttribute('data-active', di === i ? '1' : '0');
        });
      }

      function applyTransform(i) {
        var m = metrics();
        if (loop) {
          if (i < 0) i = m.maxIndex;
          else if (i > m.maxIndex) i = 0;
        }
        index = clamp(i, 0, m.maxIndex);
        var x = (m.slideW + m.gap) * index;
        track.style.transform = 'translate3d(' + (-x) + 'px,0,0)';
        setActive(index, m.maxIndex);
      }

      function goTo(i) { applyTransform(i); }
      function step(dir) { goTo(index + dir); }

      prev && prev.addEventListener('click', function (e) { e.preventDefault(); step(-1); });
      next && next.addEventListener('click', function (e) { e.preventDefault(); step(1); });

      function rebuild() {
        var m = metrics();
        // For 'gallery' type, dots represent each valid starting slide
        dots = buildDots(m.maxIndex + 1);
        applyTransform(index);
      }

      // Touch swipe
      (function bindSwipe() {
        var startX = 0, startY = 0, active = false, moved = false;
        function onStart(e) {
          if (!e.touches || !e.touches[0]) return;
          active = true; moved = false;
          startX = e.touches[0].clientX; startY = e.touches[0].clientY;
        }
        function onMove(e) {
          if (!active || !e.touches || !e.touches[0]) return;
          var dx = e.touches[0].clientX - startX, dy = e.touches[0].clientY - startY;
          if (Math.abs(dx) > 12 && Math.abs(dx) > Math.abs(dy)) moved = true;
        }
        function onEnd(e) {
          if (!active) return;
          active = false; if (!moved) return;
          var t = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0] : null;
          if (!t) return;
          var dx = t.clientX - startX;
          if (Math.abs(dx) < 40) return;
          step(dx < 0 ? 1 : -1);
        }
        viewport.addEventListener('touchstart', onStart, { passive: true });
        viewport.addEventListener('touchmove', onMove, { passive: true });
        viewport.addEventListener('touchend', onEnd, { passive: true });
        viewport.addEventListener('touchcancel', function () { active = false; }, { passive: true });
      })();

      window.addEventListener('resize', function () { rebuild(); }, { passive: true });
      rebuild();
    });
  }

  function bindVehicleDetailGallery() {
    var roots = document.querySelectorAll('[data-vehicle-detail-gallery]');
    if (!roots.length) return;
    roots.forEach(function (root) {
      var main = root.querySelector('[data-vehicle-detail-main]');
      var viewport = root.querySelector('[data-vehicle-detail-viewport]') || root;
      var thumbs = root.querySelectorAll('[data-vehicle-detail-thumb]');
      var thumbScroll = root.querySelector('[data-vehicle-detail-thumbs-scroll]');
      if (!main) return;

      // Optional v2 elements for mixed media.
      var mainImg = root.querySelector('[data-vehicle-detail-main-img]') || main;
      var mainVideoWrap = root.querySelector('[data-vehicle-detail-main-video]');
      var mainVideoStart = root.querySelector('[data-vehicle-detail-video-start]');
      var mainVideoLoading = root.querySelector('[data-vehicle-detail-video-loading]');

      function safeParseUrls(rawJson) {
        var out = [];
        try {
          out = JSON.parse(rawJson || '[]');
        } catch (_) {
          out = [];
        }
        if (!Array.isArray(out)) out = [];
        out = out
          .map(function (u) { return String(u || '').trim(); })
          .filter(function (u) { return u !== ''; });
        return out;
      }

      function safeParseItems(rawJson) {
        var out = [];
        try {
          out = JSON.parse(rawJson || '[]');
        } catch (_) {
          out = [];
        }
        if (!Array.isArray(out)) out = [];
        return out
          .map(function (it) {
            it = it || {};
            var type = String(it.type || 'image');
            if (type !== 'video') type = 'image';
            if (type === 'image') {
              var src = String(it.src || '').trim();
              if (!src) return null;
              return { type: 'image', src: src };
            }
            // video
            var provider = String(it.provider || 'youtube').trim() || 'youtube';
            var embedUrl = String(it.embedUrl || '').trim();
            var thumbUrl = String(it.thumbUrl || '').trim();
            var externalUrl = String(it.externalUrl || '').trim();
            if (!embedUrl && !externalUrl) return null;
            return { type: 'video', provider: provider, embedUrl: embedUrl, thumbUrl: thumbUrl, externalUrl: externalUrl };
          })
          .filter(function (x) { return !!x; });
      }

      // Primary contract: data-gallery-urls (image URLs array).
      var raw = root.getAttribute('data-gallery-urls') || '[]';
      var urls = safeParseUrls(raw);

      // v2 contract (optional): data-gallery-items (structured mixed media).
      var version = String(root.getAttribute('data-gallery-version') || 'v1').toLowerCase();
      var itemsRaw = root.getAttribute('data-gallery-items') || '[]';
      var items = (version === 'v2') ? safeParseItems(itemsRaw) : [];

      // Fallback: derive URLs from DOM thumbs if JSON missing/broken.
      if (!urls.length && thumbs && thumbs.length) {
        thumbs.forEach(function (t) {
          var u = (t.getAttribute('data-full') || '').trim();
          if (u) urls.push(u);
        });
      }

      // Final fallback: whatever is currently rendered as main src.
      if (!urls.length) {
        var mainSrc = (main.getAttribute('src') || '').trim();
        if (mainSrc) urls = [mainSrc];
      }

      // If v2 failed to parse but v1 has URLs, keep v1.
      if (version === 'v2' && itemsRaw && !items.length) {
        try { console.warn('[vehicle-detail-gallery] v2 items parse failed; falling back to v1 URLs', root); } catch (_) {}
        version = 'v1';
      }

      if (version !== 'v2' && !urls.length) {
        // Never silently die; leave static markup visible.
        try { console.warn('[vehicle-detail-gallery] No usable media items for gallery root', root); } catch (_) {}
        return;
      }

      if (!thumbs || !thumbs.length) {
        // Single-image gallery: keep static image visible, do not error.
        return;
      }

      var idx = 0;
      var preloaded = {};
      var fadeMs = 280;
      var activeVideo = { iframe: null };

      function itemCount() {
        return (version === 'v2' && items.length) ? items.length : urls.length;
      }

      function getItem(i) {
        if (version === 'v2' && items.length) return items[i];
        return { type: 'image', src: urls[i] };
      }

      function preload(i) {
        if (preloaded[i]) return;
        var it = getItem(i);
        if (!it || it.type !== 'image') return;
        var im = new Image();
        im.src = it.src;
        preloaded[i] = true;
      }

      function syncThumbStyles() {
        thumbs.forEach(function (t) {
          var ti = parseInt(t.getAttribute('data-index') || '-1', 10);
          var on = ti === idx;
          t.classList.toggle('is-active', on);
          t.classList.toggle('border-[#ffb129]', on);
          t.classList.toggle('border-transparent', !on);
          t.classList.toggle('opacity-100', on);
          t.classList.toggle('opacity-70', !on);
        });
        if (thumbScroll) {
          var active = root.querySelector('[data-vehicle-detail-thumb].is-active');
          if (active) {
            active.scrollIntoView({ inline: 'nearest', block: 'nearest', behavior: 'smooth' });
          }
        }
      }

      /** Remove iframe only (keep video pane visible — used before embedding a fresh player). */
      function removeVideoIframeOnly() {
        if (activeVideo.iframe && activeVideo.iframe.parentNode) {
          try { activeVideo.iframe.setAttribute('src', ''); } catch (_) {}
          try { activeVideo.iframe.remove(); } catch (_) {}
        }
        activeVideo.iframe = null;
      }

      /** Full teardown when leaving video mode (slide to image). */
      function unloadVideo() {
        removeVideoIframeOnly();
        if (mainVideoWrap) mainVideoWrap.classList.add('hidden');
        if (mainVideoStart) mainVideoStart.classList.remove('hidden');
        if (mainVideoLoading) {
          mainVideoLoading.classList.add('hidden');
          mainVideoLoading.classList.remove('flex');
        }
      }

      function showImage(src) {
        unloadVideo();
        if (mainImg) {
          mainImg.classList.remove('hidden');
          mainImg.setAttribute('src', src);
        } else {
          main.setAttribute('src', src);
        }
      }

      function showVideo(it) {
        // Requires v2 markup; if missing, fall back to external.
        if (!mainVideoWrap) {
          if (it && it.externalUrl) window.open(it.externalUrl, '_blank', 'noopener');
          return;
        }
        removeVideoIframeOnly();
        if (mainVideoLoading) {
          mainVideoLoading.classList.add('hidden');
          mainVideoLoading.classList.remove('flex');
        }
        if (mainImg) mainImg.classList.add('hidden');
        mainVideoWrap.classList.remove('hidden');

        // Show thumbnail overlay until user clicks play.
        if (mainVideoStart) mainVideoStart.classList.remove('hidden');
        if (mainVideoStart && it && it.thumbUrl) {
          var img = mainVideoStart.querySelector('img');
          if (img) img.setAttribute('src', it.thumbUrl);
        }

        function start() {
          if (!it || !it.embedUrl) {
            if (it && it.externalUrl) window.open(it.externalUrl, '_blank', 'noopener');
            return;
          }
          // Single-active-player rule: detach previous iframe; keep wrap visible for the new embed.
          removeVideoIframeOnly();
          if (mainVideoWrap) mainVideoWrap.classList.remove('hidden');
          if (mainVideoLoading) {
            mainVideoLoading.classList.remove('hidden');
            mainVideoLoading.classList.add('flex');
          }
          if (mainVideoStart) mainVideoStart.classList.add('hidden');
          var frame = document.createElement('iframe');
          frame.setAttribute('title', 'Vehicle video');
          frame.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
          frame.setAttribute('allowfullscreen', 'allowfullscreen');
          frame.className = 'h-full w-full';
          frame.addEventListener('load', function () {
            if (mainVideoLoading) {
              mainVideoLoading.classList.add('hidden');
              mainVideoLoading.classList.remove('flex');
            }
          });
          frame.src = it.embedUrl;
          activeVideo.iframe = frame;
          mainVideoWrap.appendChild(frame);
        }

        if (mainVideoStart) {
          mainVideoStart.onclick = function (e) { e && e.preventDefault && e.preventDefault(); start(); };
        }
      }

      function setIndex(nextIdx, force) {
        var count = itemCount();
        var n = ((nextIdx % count) + count) % count;
        if (n === idx && !force) return;
        main.style.opacity = '0';
        window.setTimeout(function () {
          idx = n;
          var it = getItem(idx);
          if (it && it.type === 'video') {
            showVideo(it);
          } else {
            showImage(it ? it.src : urls[idx]);
          }
          syncThumbStyles();
          preload((idx + 1) % count);
          preload((idx - 1 + count) % count);
          var done = function () {
            main.style.opacity = '1';
          };
          if (it && it.type === 'image' && mainImg && mainImg.complete) {
            requestAnimationFrame(done);
          } else {
            if (it && it.type === 'image' && mainImg) {
              mainImg.onload = function () {
                mainImg.onload = null;
                requestAnimationFrame(done);
              };
            } else {
              requestAnimationFrame(done);
            }
          }
        }, Math.round(fadeMs * 0.45));
      }

      thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
          var i = parseInt(thumb.getAttribute('data-index') || '', 10);
          if (!isNaN(i)) setIndex(i, true);
        });
      });

      root.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          setIndex(idx - 1, true);
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          setIndex(idx + 1, true);
        }
      });

      (function bindSwipe() {
        var startX = 0;
        var startY = 0;
        var startT = 0;
        var active = false;
        var moved = false;
        function onStart(e) {
          if (!e.touches || !e.touches[0]) return;
          active = true;
          moved = false;
          startX = e.touches[0].clientX;
          startY = e.touches[0].clientY;
          startT = Date.now();
        }
        function onMove(e) {
          if (!active || !e.touches || !e.touches[0]) return;
          var dx = e.touches[0].clientX - startX;
          var dy = e.touches[0].clientY - startY;
          if (Math.abs(dx) > 10 && Math.abs(dx) > Math.abs(dy) * 1.1) moved = true;
        }
        function onEnd(e) {
          if (!active) return;
          active = false;
          var t = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0] : null;
          if (!t) return;
          var dx = t.clientX - startX;
          var dt = Math.max(1, Date.now() - startT);
          var vx = Math.abs(dx) / dt;
          if (!moved && Math.abs(dx) < 8) return;
          if (Math.abs(dx) < 28 && vx < 0.35) return;
          setIndex(dx < 0 ? idx + 1 : idx - 1, true);
        }
        viewport.addEventListener('touchstart', onStart, { passive: true });
        viewport.addEventListener('touchmove', onMove, { passive: true });
        viewport.addEventListener('touchend', onEnd, { passive: true });
        viewport.addEventListener('touchcancel', function () { active = false; }, { passive: true });
      })();

      // Desktop swipe/drag (Pointer Events). Touch already handled above.
      (function bindPointerDrag() {
        if (!viewport || !viewport.addEventListener) return;
        if (window.PointerEvent == null) return;
        var startX = 0;
        var startY = 0;
        var active = false;
        var moved = false;
        function onDown(e) {
          if (!e || e.pointerType === 'touch') return;
          active = true;
          moved = false;
          startX = e.clientX;
          startY = e.clientY;
          try { viewport.setPointerCapture(e.pointerId); } catch (_) {}
        }
        function onMove(e) {
          if (!active) return;
          var dx = e.clientX - startX;
          var dy = e.clientY - startY;
          if (Math.abs(dx) > 8 && Math.abs(dx) > Math.abs(dy) * 1.1) moved = true;
        }
        function onUp(e) {
          if (!active) return;
          active = false;
          if (!moved) return;
          var dx = e.clientX - startX;
          if (Math.abs(dx) < 34) return;
          setIndex(dx < 0 ? idx + 1 : idx - 1, true);
        }
        viewport.addEventListener('pointerdown', onDown);
        viewport.addEventListener('pointermove', onMove);
        viewport.addEventListener('pointerup', onUp);
        viewport.addEventListener('pointercancel', function () { active = false; });
      })();

      preload(0);
      if (itemCount() > 1) preload(1);
    });
  }

  // Financing calculator removed (plan requirement).

  function bindAccordions() {
    var items = document.querySelectorAll('[data-accordion-item]');
    if (!items.length) return;

    items.forEach(function (item) {
      var btn = item.querySelector('[data-accordion-btn]');
      var content = item.querySelector('[data-accordion-content]');
      if (!btn || !content) return;

      btn.addEventListener('click', function () {
        var isOpen = item.classList.contains('is-active');
        
        // Optional: close other accordions in the same group/parent
        var parent = item.parentElement;
        if (parent) {
          parent.querySelectorAll('[data-accordion-item].is-active').forEach(function (other) {
            if (other !== item) {
              other.classList.remove('is-active');
              var otherContent = other.querySelector('[data-accordion-content]');
              if (otherContent) otherContent.style.maxHeight = null;
            }
          });
        }

        item.classList.toggle('is-active', !isOpen);
        if (!isOpen) {
          content.style.maxHeight = content.scrollHeight + 'px';
        } else {
          content.style.maxHeight = null;
        }
      });
    });
  }

  function bindPhoneReveal() {
    var roots = document.querySelectorAll('[data-phone-reveal]');
    if (!roots.length) return;
    roots.forEach(function (root) {
      var btn = root.querySelector('[data-phone-reveal-btn]');
      var mask = root.querySelector('[data-phone-mask]');
      var full = root.querySelector('[data-phone-full]');
      var iconShow = btn && btn.querySelector('[data-phone-icon-show]');
      var iconHide = btn && btn.querySelector('[data-phone-icon-hide]');
      if (!btn || !mask || !full) return;
      var maskedText = mask.textContent || '';
      if (!root.getAttribute('data-phone-mask-original')) {
        root.setAttribute('data-phone-mask-original', maskedText);
      }
      function syncIcons(revealed) {
        if (iconShow) iconShow.classList.toggle('hidden', revealed);
        if (iconHide) iconHide.classList.toggle('hidden', !revealed);
        btn.setAttribute('aria-pressed', revealed ? 'true' : 'false');
      }
      syncIcons(false);

      btn.addEventListener('click', function () {
        var fullText = (full.textContent || '').trim();
        if (!fullText) return;
        var orig = root.getAttribute('data-phone-mask-original') || '';
        var showing = root.getAttribute('data-phone-reveal-state') === '1';
        if (!showing) {
          mask.textContent = fullText;
          root.setAttribute('data-phone-reveal-state', '1');
          syncIcons(true);
        } else {
          mask.textContent = orig;
          root.setAttribute('data-phone-reveal-state', '0');
          syncIcons(false);
        }
      });
    });
  }

  function bindFavoriteToggles() {
    var forms = document.querySelectorAll('form[data-favorite-toggle]');
    if (!forms.length) return;
    var token = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    forms.forEach(function (form) {
      if (!form || !form.addEventListener) return;
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        var icon = form.querySelector('[data-favorite-icon]');
        var action = form.getAttribute('action') || '';
        if (!action) return;
        if (btn) btn.setAttribute('disabled', 'disabled');
        var body = new FormData();
        var hidden = form.querySelector('input[name="_token"]');
        if (hidden && hidden.value) body.append('_token', hidden.value);
        fetch(action, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': token || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json, text/plain, */*'
          },
          body: body,
        }).then(function (res) {
          // If session/CSRF expired (419), reload to let Laravel reissue tokens via full page refresh.
          if (res.status === 419 || res.status === 401) {
            window.location.reload();
            return null;
          }
          // Toggle is implemented as redirect-back; treat any 2xx/3xx as success and flip icon.
          if (res && (res.ok || (res.status >= 300 && res.status < 400))) {
            if (icon) {
              var filled = icon.getAttribute('data-favorite-icon') === 'filled';
              icon.setAttribute('data-favorite-icon', filled ? 'outline' : 'filled');
              var svgUse = icon.querySelector('[data-favorite-state]');
              if (svgUse) {
                svgUse.setAttribute('data-favorite-state', filled ? 'outline' : 'filled');
              }
            }
          }
          return null;
        }).catch(function () {
          // Fallback: submit normally if fetch fails.
          form.submit();
        }).finally(function () {
          if (btn) btn.removeAttribute('disabled');
        });
      });
    });
  }

  bindAccordions();
  bindContactTabs();
  bindHeaderScrollState();
  bindHeaderInventoryDropdown();
  bindHeaderFaqDropdown();
  bindMobileInventoryAccordion();
  bindMobileFaqAccordion();
  bindHomeStatsCountUp();
  bindListingHoverGalleries();
  bindSimpleCarousels();
  bindVehicleDetailGallery();
  bindPhoneReveal();
  bindFavoriteToggles();

})();
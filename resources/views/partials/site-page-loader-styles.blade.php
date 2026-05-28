<style id="site-page-loader-styles">
  html.site-is-loading {
    overflow: hidden !important;
    background: #ffffff !important;
  }

  html.site-is-loading body {
    overflow: hidden !important;
  }

  /* Hide storefront chrome until loader finishes — avoids logo flashing top-left in header */
  html.site-is-loading body > *:not(#site-page-loader) {
    visibility: hidden !important;
  }

  #site-page-loader.site-page-loader {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    min-height: 100vh !important;
    min-height: 100dvh !important;
    z-index: 2147483646 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
    transition: opacity 280ms ease, visibility 280ms ease;
  }

  #site-page-loader .site-page-loader__inner {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 1.5rem !important;
    box-sizing: border-box !important;
  }

  #site-page-loader .site-page-loader__logo {
    display: block !important;
    margin: 0 auto !important;
    width: auto !important;
    max-width: min(72vw, 280px) !important;
    max-height: 100px !important;
    height: auto !important;
    object-fit: contain !important;
    transform-origin: center center !important;
    animation: site-page-loader-pulse 0.95s ease-in-out infinite !important;
  }

  #site-page-loader .site-page-loader__brand {
    margin: 0 auto !important;
    padding: 0 !important;
    max-width: min(80vw, 22rem) !important;
    font-family: Georgia, 'Times New Roman', serif !important;
    font-size: clamp(1.35rem, 4vw, 1.85rem) !important;
    font-weight: 600 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    text-align: center !important;
    color: #191c1e !important;
    transform-origin: center center !important;
    animation: site-page-loader-pulse 0.95s ease-in-out infinite !important;
  }

  #site-page-loader.site-page-loader.is-hiding {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
  }

  @keyframes site-page-loader-pulse {
    0%,
    100% {
      transform: scale(1);
      opacity: 1;
    }
    50% {
      transform: scale(1.14);
      opacity: 0.55;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    #site-page-loader .site-page-loader__logo,
    #site-page-loader .site-page-loader__brand {
      animation: none !important;
    }
  }
</style>

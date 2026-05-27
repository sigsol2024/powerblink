<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
<script>
  (function () {
    const luxe = {
      colors: {
        'secondary-fixed-dim': '#eabf8d',
        'inverse-on-surface': '#f1f1f1',
        'on-surface': '#1b1b1b',
        'secondary-fixed': '#ffddb7',
        secondary: '#78582f',
        'surface-container-highest': '#e2e2e2',
        'on-primary': '#ffffff',
        'surface-container-high': '#e8e8e8',
        'on-secondary-fixed': '#2a1700',
        'outline-variant': '#cfc4c5',
        'surface-container-low': '#f3f3f3',
        error: '#ba1a1a',
        'on-surface-variant': '#4c4546',
        outline: '#7e7576',
        background: '#f9f9f9',
        primary: '#000000',
        surface: '#f9f9f9',
        'surface-container': '#eeeeee',
        'surface-container-lowest': '#ffffff',
        'custom-accent': '#C19A6B',
      },
      spacing: {
        gutter: '1.5rem',
        'section-py-mobile': '4rem',
        'margin-mobile': '1.25rem',
        'max-container': '1200px',
        'section-py-desktop': '6rem',
      },
      fontFamily: {
        'body-md': ['Hanken Grotesk', 'sans-serif'],
        'button-text': ['Hanken Grotesk', 'sans-serif'],
        'headline-lg-mobile': ['Bodoni Moda', 'serif'],
        'display-lg-mobile': ['Bodoni Moda', 'serif'],
        'display-lg': ['Bodoni Moda', 'serif'],
        'label-caps': ['Hanken Grotesk', 'sans-serif'],
        'headline-md': ['Bodoni Moda', 'serif'],
        'headline-lg': ['Bodoni Moda', 'serif'],
        'display-lg': ['Bodoni Moda', 'serif'],
        'display-lg-mobile': ['Bodoni Moda', 'serif'],
      },
      fontSize: {
        'body-md': ['16px', { lineHeight: '24px', letterSpacing: '0.01em' }],
        'button-text': ['14px', { lineHeight: '20px', letterSpacing: '0.15em', fontWeight: '600' }],
        'headline-lg-mobile': ['32px', { lineHeight: '40px' }],
        'display-lg-mobile': ['48px', { lineHeight: '52px' }],
        'headline-md': ['24px', { lineHeight: '32px' }],
        'headline-lg': ['40px', { lineHeight: '48px' }],
        'label-caps': ['12px', { lineHeight: '16px', letterSpacing: '0.2em', fontWeight: '600' }],
      },
    };
    const cfg = tailwind.config;
    if (cfg && cfg.theme && cfg.theme.extend) {
      Object.assign(cfg.theme.extend.colors || (cfg.theme.extend.colors = {}), luxe.colors);
      Object.assign(cfg.theme.extend.spacing || (cfg.theme.extend.spacing = {}), luxe.spacing);
      Object.assign(cfg.theme.extend.fontFamily || (cfg.theme.extend.fontFamily = {}), luxe.fontFamily);
      Object.assign(cfg.theme.extend.fontSize || (cfg.theme.extend.fontSize = {}), luxe.fontSize);
    }
  })();
</script>
<style>
  .luxe-geometric-bg {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .luxe-store .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
  }
  .luxe-store input:focus,
  .luxe-store select:focus,
  .luxe-store textarea:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: #000 !important;
  }
</style>

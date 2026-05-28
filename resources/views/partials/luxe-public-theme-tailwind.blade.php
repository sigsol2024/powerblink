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

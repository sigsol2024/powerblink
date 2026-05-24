<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400..900;1,400..900&family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
<script id="admin-luxe-tailwind-config">
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          'secondary-fixed-dim': '#eabf8d',
          'inverse-on-surface': '#f1f1f1',
          'on-surface': '#1b1b1b',
          'secondary-fixed': '#ffddb7',
          secondary: '#78582f',
          'surface-container-highest': '#e2e2e2',
          'inverse-primary': '#c6c6c6',
          'on-primary': '#ffffff',
          'surface-container-high': '#e8e8e8',
          'on-secondary-fixed': '#2a1700',
          'tertiary-fixed-dim': '#c6c6c6',
          'on-error': '#ffffff',
          'on-secondary': '#ffffff',
          'on-secondary-fixed-variant': '#5e411a',
          'tertiary-container': '#1b1b1b',
          'on-background': '#1b1b1b',
          'error-container': '#ffdad6',
          'outline-variant': '#cfc4c5',
          'surface-container-low': '#f3f3f3',
          'on-primary-fixed-variant': '#474747',
          error: '#ba1a1a',
          'on-tertiary': '#ffffff',
          'on-tertiary-fixed-variant': '#474747',
          'inverse-surface': '#303030',
          'on-tertiary-fixed': '#1b1b1b',
          'on-surface-variant': '#4c4546',
          'on-tertiary-container': '#848484',
          'surface-dim': '#dadada',
          'on-error-container': '#93000a',
          outline: '#7e7576',
          'surface-variant': '#e2e2e2',
          'primary-fixed': '#e2e2e2',
          'primary-container': '#1b1b1b',
          background: '#f9f9f9',
          primary: '#000000',
          surface: '#f9f9f9',
          'tertiary-fixed': '#e2e2e2',
          'secondary-container': '#fed39f',
          'surface-container': '#eeeeee',
          'surface-bright': '#f9f9f9',
          tertiary: '#000000',
          'on-secondary-container': '#795930',
          'primary-fixed-dim': '#c6c6c6',
          'on-primary-container': '#848484',
          'surface-tint': '#5e5e5e',
          'on-primary-fixed': '#1b1b1b',
          'surface-container-lowest': '#ffffff',
        },
        borderRadius: { DEFAULT: '0.25rem', lg: '0.5rem', xl: '0.75rem', full: '9999px' },
        spacing: {
          gutter: '1.5rem',
          'section-py-mobile': '4rem',
          'margin-mobile': '1.25rem',
          unit: '4px',
          'max-container': '1200px',
          'section-py-desktop': '6rem',
        },
        fontFamily: {
          'body-md': ['Hanken Grotesk', 'sans-serif'],
          'button-text': ['Hanken Grotesk', 'sans-serif'],
          'body-lg': ['Hanken Grotesk', 'sans-serif'],
          'headline-lg-mobile': ['Bodoni Moda', 'serif'],
          'display-lg-mobile': ['Bodoni Moda', 'serif'],
          'display-lg': ['Bodoni Moda', 'serif'],
          'label-caps': ['Hanken Grotesk', 'sans-serif'],
          'headline-md': ['Bodoni Moda', 'serif'],
          'headline-lg': ['Bodoni Moda', 'serif'],
        },
        fontSize: {
          'body-md': ['16px', { lineHeight: '24px', letterSpacing: '0.01em', fontWeight: '400' }],
          'button-text': ['14px', { lineHeight: '20px', letterSpacing: '0.15em', fontWeight: '600' }],
          'body-lg': ['18px', { lineHeight: '28px', letterSpacing: '0em', fontWeight: '400' }],
          'headline-lg-mobile': ['32px', { lineHeight: '40px', letterSpacing: '0em', fontWeight: '500' }],
          'display-lg-mobile': ['48px', { lineHeight: '52px', letterSpacing: '-0.01em', fontWeight: '600' }],
          'display-lg': ['72px', { lineHeight: '80px', letterSpacing: '-0.02em', fontWeight: '600' }],
          'label-caps': ['12px', { lineHeight: '16px', letterSpacing: '0.2em', fontWeight: '600' }],
          'headline-md': ['24px', { lineHeight: '32px', letterSpacing: '0.02em', fontWeight: '500' }],
          'headline-lg': ['40px', { lineHeight: '48px', letterSpacing: '0em', fontWeight: '500' }],
        },
      },
    },
  };
</script>
<style>
  .admin-luxe-root {
    background-color: #f9f9f9;
    font-family: 'Hanken Grotesk', sans-serif;
  }
  .admin-luxe-root .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
    vertical-align: middle;
  }
  .admin-luxe-root .material-symbols-outlined.filled {
    font-variation-settings: 'FILL' 1, 'wght' 300, 'GRAD' 0, 'opsz' 24;
  }
  .luxe-grid-pattern {
    background-image: radial-gradient(#7e7576 0.5px, transparent 0.5px);
    background-size: 24px 24px;
    opacity: 0.03;
  }
  .luxe-pattern-bg {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cfc4c5; }
  .admin-luxe-table tbody tr:hover td { background-color: #f3f3f3; transition: background-color 0.3s ease; }
  .admin-luxe-pagination nav { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 0.5rem; }
  .admin-luxe-pagination nav a,
  .admin-luxe-pagination nav span {
    display: inline-flex; min-width: 2.5rem; height: 2.5rem; align-items: center; justify-content: center;
    border: 1px solid #cfc4c5; padding: 0 0.5rem; font-size: 11px; font-weight: 600;
    letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none; color: #1b1b1b;
  }
  .admin-luxe-pagination nav a:hover { background: #f3f3f3; }
  .admin-luxe-pagination nav span[aria-current="page"] span,
  .admin-luxe-pagination nav span[aria-current="page"] { background: #000; color: #fff; border-color: #000; }
  .admin-luxe-btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    background: #000; color: #fff; padding: 1rem 2rem;
    font-size: 14px; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
  .admin-luxe-btn-primary:hover { transform: scale(1.02); }
  .admin-luxe-btn-primary:active { transform: scale(0.98); }
  .active-status-badge {
    background-color: #C19A6B;
    color: #ffffff;
    border: none;
  }
  .admin-luxe-root input:focus,
  .admin-luxe-root select:focus,
  .admin-luxe-root textarea:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: #000 !important;
  }
  .admin-luxe-root .admin-page-title,
  .admin-luxe-root .font-headline-lg { font-family: 'Bodoni Moda', serif; }
  .admin-luxe-root .admin-content-toolbar { padding: 0 1.25rem 1rem; max-width: 1200px; margin: 0 auto; width: 100%; }
  @media (min-width: 768px) { .admin-luxe-root .admin-content-toolbar { padding-left: 1.5rem; padding-right: 1.5rem; } }
  .admin-luxe-root .admin-btn-primary,
  .admin-luxe-root .admin-luxe-btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
    background: #000; color: #fff; padding: 0.75rem 1.5rem;
    font-size: 12px; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase;
    border: 1px solid #000;
  }
  .admin-luxe-root .admin-btn-primary:hover { opacity: 0.9; }
  .admin-luxe-root .rounded-xl.border-zinc-200,
  .admin-luxe-root .border-zinc-200.bg-white {
    border-color: #cfc4c5 !important; border-radius: 0 !important; background: #ffffff !important;
    box-shadow: none !important;
  }
  .admin-luxe-root .text-zinc-900 { color: #1b1b1b !important; }
  .admin-luxe-root .text-zinc-600,
  .admin-luxe-root .text-zinc-500 { color: #4c4546 !important; }
  .admin-luxe-root .divide-zinc-200 > :not([hidden]) ~ :not([hidden]) { border-color: #cfc4c5; }
  .admin-luxe-root .border-zinc-100 { border-color: #eeeeee !important; }
</style>

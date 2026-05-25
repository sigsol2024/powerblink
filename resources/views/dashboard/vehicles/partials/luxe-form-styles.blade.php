<style>
  /* WooCommerce-style product form: clean labels, boxed inputs, no fashion typography. */
  .luxe-product-form .block.font-medium {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 13px;
    letter-spacing: 0;
    text-transform: none;
    color: #1d2327;
    font-weight: 500;
    margin-bottom: 4px;
  }
  .luxe-product-form input[type="text"],
  .luxe-product-form input[type="number"],
  .luxe-product-form input[type="email"],
  .luxe-product-form input[type="url"],
  .luxe-product-form input[type="search"],
  .luxe-product-form select,
  .luxe-product-form textarea:not(.rounded-md) {
    width: 100%;
    background: #fff;
    border: 1px solid #8c8f94;
    border-radius: 3px;
    box-shadow: none;
    padding: 6px 10px;
    font-size: 14px;
    line-height: 20px;
    color: #1d2327;
  }
  .luxe-product-form input:focus,
  .luxe-product-form select:focus,
  .luxe-product-form textarea:focus {
    outline: 2px solid transparent;
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1;
  }
  /* Section card boxes (WooCommerce metabox feel) */
  .luxe-product-form section {
    background: #fff;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 1rem 1.25rem;
  }
  .luxe-product-form section.rounded-lg { border-color: #dcdcde; border-radius: 4px; }
  .luxe-product-form section h3 {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 13px;
    letter-spacing: 0;
    text-transform: none;
    color: #1d2327;
    font-weight: 600;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f0f0f1;
    margin-bottom: 0.75rem;
  }
  .luxe-product-form .text-on-surface-variant { color: #646970; }
  /* Compact form layout */
  .luxe-product-form > .grid { gap: 1rem !important; }
  .luxe-product-form .space-y-10 > * + * { margin-top: 1rem !important; }
  .luxe-product-form .space-y-6 > * + * { margin-top: 0.75rem !important; }
  .luxe-product-form .gap-12, .luxe-product-form .gap-16, .luxe-product-form .gap-8 { gap: 1rem !important; }
</style>

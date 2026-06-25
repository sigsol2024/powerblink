<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  .active-nav-link {
    color: #006d32;
    font-weight: 700;
    border-bottom: 2px solid #006d32;
    padding-bottom: 0.25rem;
  }
  .cinematic-overlay {
    background: linear-gradient(0deg, rgba(11, 28, 52, 0.95) 0%, rgba(11, 28, 52, 0.4) 50%, rgba(11, 28, 52, 0.2) 100%);
  }
  .hero-gradient {
    background: linear-gradient(to bottom, rgba(11, 28, 52, 0.8), rgba(0, 0, 0, 0.9));
  }
  .card-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .card-hover:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 20px 25px -5px rgba(11, 28, 52, 0.15);
  }
  .fade-up {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
  }
  .fade-up.visible {
    opacity: 1;
    transform: translateY(0);
  }
  .masonry-grid {
    column-count: 2;
    column-gap: 1rem;
  }
  @media (min-width: 768px) {
    .masonry-grid { column-count: 3; }
  }
  @media (min-width: 1024px) {
    .masonry-grid { column-count: 4; }
  }
  .masonry-grid > * {
    break-inside: avoid;
    margin-bottom: 1rem;
  }
  .glass-panel {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
  }
  [x-cloak] { display: none !important; }
  .custom-scrollbar::-webkit-scrollbar { width: 4px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #384761; border-radius: 10px; }
  .glass-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(226, 232, 240, 0.6);
  }
  .stat-card-shadow {
    box-shadow: 0 4px 24px -4px rgba(11, 28, 52, 0.08);
  }
  .progress-line {
    height: 2px;
    flex-grow: 1;
    min-width: 0.5rem;
    transition: background-color 0.3s;
  }
  .input-focus:focus {
    outline: none;
    border-color: #006d32;
    box-shadow: 0 0 0 4px rgba(0, 109, 50, 0.1);
  }
  .pb-admin-table thead {
    background: #f2f4f6;
  }
  .pb-admin-table th {
    padding: 0.75rem 1rem;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #44474d;
    border-bottom: 1px solid #c5c6ce;
    white-space: nowrap;
  }
  .pb-admin-table td {
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #e0e3e5;
    vertical-align: middle;
  }
  .pb-admin-table tbody tr:hover {
    background: #f7f9fb;
  }
  .pb-admin-table a,
  .pb-admin-table button[type="submit"] {
    min-height: 2.75rem;
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
  }
  .pb-touch {
    min-height: 2.75rem;
    min-width: 2.75rem;
  }
  @media (max-width: 640px) {
    .pb-admin-table th,
    .pb-admin-table td {
      padding: 0.75rem 0.875rem;
    }
  }
  html, body { overflow-x: hidden; max-width: 100%; }
  .pb-mobile-safe { max-width: 100%; overflow-x: hidden; }
</style>

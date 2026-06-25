<script>
  (function () {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll('.fade-up').forEach(function (el) { observer.observe(el); });

    const nav = document.getElementById('main-nav');
    if (nav) {
      window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
          nav.classList.add('py-2', 'bg-surface/95');
          nav.classList.remove('py-4', 'bg-surface/90');
        } else {
          nav.classList.add('py-4', 'bg-surface/90');
          nav.classList.remove('py-2', 'bg-surface/95');
        }
      }, { passive: true });
    }
  })();
</script>

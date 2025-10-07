// js/app.js

// Load a page dynamically into #app
async function loadPage(page) {
  try {
    const res = await fetch(`pages/${page}.html`);
    if (!res.ok) throw new Error("Page not found");
    const html = await res.text();

    const app = document.getElementById('app');

    // Optional: fade out old content
    app.classList.remove('show');
    setTimeout(() => {
      app.innerHTML = html;

      // Fade in new content
      app.classList.add('show');

      // Reinitialize animations
      if (typeof AOS !== 'undefined') AOS.refresh();

      // Reinitialize Tiny Slider (if any sliders exist on the page)
      if (typeof tns !== 'undefined') {
        // Example: re-init sliders
        const sliders = document.querySelectorAll('.tiny-slider');
        sliders.forEach(slider => {
          tns({
            container: slider,
            items: 1,
            slideBy: 'page',
            autoplay: true,
            controls: false,
            nav: true,
            autoplayButtonOutput: false
          });
        });
      }

      // Update active navbar link
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.page === page) link.classList.add('active');
      });
    }, 200);

  } catch (err) {
    document.getElementById('app').innerHTML = "<h2 class='text-center mt-5'>404 - Page Not Found</h2>";
    console.error(err);
  }
}

// Handle navbar clicks
document.addEventListener('click', e => {
  const link = e.target.closest('[data-page]');
  if (!link) return;

  e.preventDefault();
  const page = link.dataset.page;
  window.location.hash = page; // update URL hash
  loadPage(page);
});

// Handle back/forward browser navigation
window.addEventListener('hashchange', () => {
  const page = window.location.hash.replace('#', '') || 'home';
  loadPage(page);
});

// Load initial page on DOM content loaded
window.addEventListener('DOMContentLoaded', () => {
  const initialPage = window.location.hash.replace('#', '') || 'home';
  loadPage(initialPage);
});

document.addEventListener("DOMContentLoaded", () => {
    function navigate() {
      const hash = window.location.hash || "#/home";
      const pageId = hash.replace("#/", "") + "-page";
  
      // Hide all pages
      document.querySelectorAll(".page").forEach(page => {
        page.classList.remove("active");
        page.style.display = "none";
      });
  
      // Show the current page
      const activePage = document.getElementById(pageId);
      if (activePage) {
        activePage.classList.add("active");
        activePage.style.display = "block";
        document.title = activePage.dataset.title || "Furni SPA";
      }
    }
  
    window.addEventListener("hashchange", navigate);
    navigate(); // initial load
  });

  const pages = document.querySelectorAll('.spa-page');

  
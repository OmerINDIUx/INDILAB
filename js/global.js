/**
 * Global JavaScript for INDI Lab Website
 * Handles menu loading, translations, SVG loading, and resize logic.
 */

document.addEventListener("DOMContentLoaded", () => {
  loadMenu();
  loadSVGs();
  initResizeHandler();

  // Initialize translations if available
  if (window.applyTranslations) {
    window.applyTranslations();
  }
});

/**
 * Loads the menu from /menu.html and injects it into #menu-placeholder.
 */
function loadMenu() {
  const placeholder = document.getElementById("menu-placeholder");
  if (!placeholder) return;

  fetch("/menu.html")
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          `Error loading menu: ${response.status} ${response.statusText}`
        );
      }
      return response.text();
    })
    .then((data) => {
      placeholder.innerHTML = data;

      // Load menu.js after menu HTML is injected
      const script = document.createElement("script");
      script.src = "/js/demo4/menu.js";
      script.onload = () => console.log("menu.js loaded successfully ✅");
      script.onerror = () =>
        console.error("Error loading /js/demo4/menu.js ❌");
      document.body.appendChild(script);

      // Re-apply translations if needed
      if (window.applyTranslations) {
        window.applyTranslations();
      }
    })
    .catch((error) => {
      console.error("Error loading menu:", error);
    });
}

/**
 * Loads external SVGs into elements with [data-svg] attribute.
 */
function loadSVGs() {
  document.querySelectorAll("[data-svg]").forEach((el) => {
    const svgPath = el.getAttribute("data-svg");
    fetch(svgPath)
      .then((res) => res.text())
      .then((svgContent) => {
        el.innerHTML = svgContent;
      })
      .catch((err) => console.error("Error loading SVG:", err));
  });
}

/**
 * Handles window resize events, reloading the page on desktop to fix layout issues,
 * but skipping this behavior on mobile devices.
 */
function initResizeHandler() {
  function isMobileDevice() {
    const byWidth = window.innerWidth < 768;
    const hasTouch =
      "ontouchstart" in window ||
      navigator.maxTouchPoints > 0 ||
      navigator.msMaxTouchPoints > 0;
    const ua = (
      navigator.userAgent ||
      navigator.vendor ||
      window.opera
    ).toLowerCase();
    const isUA = /android|iphone|ipad|ipod|opera mini|iemobile|mobile/.test(ua);
    return (byWidth && hasTouch) || isUA;
  }

  let resizeTimeout;

  window.addEventListener("resize", () => {
    // Avoid reloading on mobile to prevent UX issues
    if (isMobileDevice()) return;

    clearTimeout(resizeTimeout);

    resizeTimeout = setTimeout(() => {
      // Save current scroll position
      sessionStorage.setItem("scrollPosition", window.scrollY);

      // Reload page
      location.reload();
    }, 300);
  });

  // Restore scroll position on load
  const scrollPos = sessionStorage.getItem("scrollPosition");
  if (scrollPos !== null) {
    window.scrollTo(0, parseInt(scrollPos, 10));
    sessionStorage.removeItem("scrollPosition");
  }
}

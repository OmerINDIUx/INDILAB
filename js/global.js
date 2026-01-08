/**
 * Global JavaScript for INDI Lab Website
 * Handles menu loading, translations, SVG loading, and resize logic.
 */

document.addEventListener("DOMContentLoaded", () => {
  loadMenu();
  loadSVGs();
  initResizeHandler();

  if (window.applyTranslations) {
    window.applyTranslations();
  }

  // Determine root path
  const root = window.rootPath || "";

  // Load Cookie Consent
  const cookieScript = document.createElement("script");
  cookieScript.src = `${root}js/cookie-consent.js`;
  document.body.appendChild(cookieScript);
});

/**
 * Loads the menu from menu.html and injects it into #menu-placeholder.
 */
function loadMenu() {
  const placeholder = document.getElementById("menu-placeholder");
  if (!placeholder) return;

  const root = window.rootPath || "";

  fetch(`${root}menu.html`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          `Error loading menu: ${response.status} ${response.statusText}`
        );
      }
      return response.text();
    })
    .then((data) => {
      // Fix paths in loaded menu HTML (simple replacement for links)
      // Note: This is a simple fix. For more complex apps, use a proper base or absolute paths.
      // However, since we are moving to relative, we might need to adjust links inside menu.html dynamically?
      // For now, let's just load it. The links in menu.html are likely absolute "/index.html".
      // If we change them to relative, we need to adjust them based on where we are.
      // But let's first fix the LOADING of the menu itself.
      placeholder.innerHTML = data;

      // When loading menu.html content, if it contains links like href="/index.html",
      // and we are capable of relative paths, we might want to replace them?
      // Actually, if we stick to removing "/" from global.js, it fixes the "loading" part.

      const script = document.createElement("script");
      script.src = `${root}js/demo4/menu.js`;
      script.onload = () => console.log("menu.js loaded successfully ✅");
      script.onerror = () => console.error("Error loading js/demo4/menu.js ❌");
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

    // Reloading on resize is generally bad practice.
    // We removed the location.reload() call here to prevent the page
    // from refreshing (and re-triggering the cookie popup) on every resize.
    // Layout adjustments should be handled via CSS or specific JS updates.
  });

  // Restore scroll position on load
  const scrollPos = sessionStorage.getItem("scrollPosition");
  if (scrollPos !== null) {
    window.scrollTo(0, parseInt(scrollPos, 10));
    sessionStorage.removeItem("scrollPosition");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const glossaryItems = document.querySelectorAll(".glossary-item");

  glossaryItems.forEach((item) => {
    item.addEventListener("click", () => {
      item.classList.toggle("active");
    });
  });

  // Inline Definition (Popup) Logic
  const definitionSpans = document.querySelectorAll(".span-definicion");
  definitionSpans.forEach((span) => {
    span.addEventListener("click", (e) => {
      e.stopPropagation();
      // Close other open definitions if desired (optional)
      definitionSpans.forEach((s) => {
        if (s !== span) s.classList.remove("active");
      });
      span.classList.toggle("active");
    });
  });

  // Close popup when clicking elsewhere
  document.addEventListener("click", (e) => {
    definitionSpans.forEach((span) => {
      if (!span.contains(e.target)) {
        span.classList.remove("active");
      }
    });
  });

  // Mobile Glossary Pagination
  initMobileGlossaryPagination();
  window.addEventListener(
    "resize",
    debounce(() => {
      initMobileGlossaryPagination();
    }, 200)
  );
});

function initMobileGlossaryPagination() {
  const isMobile = window.innerWidth <= 768;
  const items = document.querySelectorAll(".Glossary-grid .glossary-item");
  const ITEMS_PER_PAGE = 4;

  // Cleanup: Remove existing button
  const existingBtn = document.querySelector(".glossary-view-more-btn");
  if (existingBtn) existingBtn.remove();

  // Reset: Show all items first (removes class hidden-mobile)
  items.forEach((item) => item.classList.remove("hidden-mobile"));

  // Only proceed if mobile
  if (!isMobile) return;

  // Hide items beyond the limit
  let hiddenCount = 0;
  items.forEach((item, index) => {
    if (index >= ITEMS_PER_PAGE) {
      item.classList.add("hidden-mobile");
      hiddenCount++;
    }
  });

  // If we hid any items, add the "Ver más" button
  if (hiddenCount > 0) {
    const grid = document.querySelector(".Glossary-grid");
    const btn = document.createElement("div");
    btn.className = "glossary-view-more-btn";
    btn.textContent = "Ver más"; // Consider internationalization if needed, but text is static here

    btn.addEventListener("click", () => {
      // Check if we are in "Back to Top" mode
      if (btn.classList.contains("back-to-top")) {
        // Scroll to title
        const title = document.querySelector(".glossary-title");
        if (title) {
          title.scrollIntoView({ behavior: "smooth", block: "start" });
        }

        // Reset Logic: Hide items again after a short delay or immediately
        // Here we reset immediately to give the "started over" feel
        items.forEach((item, index) => {
          if (index >= ITEMS_PER_PAGE) {
            item.classList.add("hidden-mobile");
          }
        });

        // Reset button state
        btn.textContent = "Ver más";
        btn.classList.remove("back-to-top");
        return;
      }

      // Find currently hidden items
      const hiddenItems = Array.from(
        document.querySelectorAll(".glossary-item.hidden-mobile")
      );

      // Reveal next batch
      for (let i = 0; i < ITEMS_PER_PAGE && i < hiddenItems.length; i++) {
        hiddenItems[i].classList.remove("hidden-mobile");
      }

      // If no more hidden items, switch to "Back to top"
      if (
        document.querySelectorAll(".glossary-item.hidden-mobile").length === 0
      ) {
        btn.textContent = "Comprimir";
        btn.classList.add("back-to-top");
      }
    });

    grid.appendChild(btn);
  }
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

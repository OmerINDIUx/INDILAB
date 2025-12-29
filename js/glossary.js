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
});

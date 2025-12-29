document.addEventListener("DOMContentLoaded", () => {
  const glossaryItems = document.querySelectorAll(".glossary-item");

  glossaryItems.forEach((item) => {
    item.addEventListener("click", () => {
      item.classList.toggle("active");
    });
  });
});

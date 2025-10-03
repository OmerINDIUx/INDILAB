let currentLang = localStorage.getItem("indi-lang") || "es";
let loadedTranslations = {}; // 👈 cache del último JSON cargado

function applyTranslations() {
  document.querySelectorAll("[data-i18n]").forEach(el => {
    const key = el.getAttribute("data-i18n");
    const translation = loadedTranslations[key] || key;

    if (el.tagName === "IMG") {
      el.alt = translation;
    } else if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
      el.placeholder = translation;
    } else {
      el.innerHTML = translation;
    }
  });
}

// Hacer accesible globalmente
window.applyTranslations = applyTranslations;

function translatePage(lang = "en") {
  const elements = document.querySelectorAll("[data-i18n]");
  if (elements.length === 0) {
    console.warn("⚠️ No hay elementos [data-i18n] en el DOM todavía");
    return;
  }

  gsap.to(elements, {
    opacity: 0,
    scale: 0.95,
    duration: 0.3,
    stagger: 0.02,
    ease: "power1.in",
    onComplete: () => {
      fetch(`/json/${lang}.json`)
        .then(res => {
          if (!res.ok) throw new Error("Archivo JSON no encontrado");
          return res.json();
        })
        .then(json => {
          loadedTranslations = json; // 👈 cachear traducciones
          applyTranslations();

          gsap.fromTo(
            elements,
            { opacity: 0, scale: 0.95 },
            { opacity: 1, scale: 1, duration: 0.5, stagger: 0.02, ease: "back.out(1.4)" }
          );

          currentLang = lang;
          localStorage.setItem("indi-lang", lang); // persistir idioma
          updateButtonLabel();
        })
        .catch((err) => console.error("Error cargando idioma:", err));
    },
  });
}

function updateButtonLabel() {
  const btn = document.getElementById("lang-toggle");
  if (!btn) return;
  btn.textContent = currentLang === "es" ? "EN" : "ES";
}

window.addEventListener("DOMContentLoaded", () => {
  translatePage(currentLang);

  gsap.from("[data-i18n]", {
    opacity: 0,
    y: 20,
    duration: 0.5,
    stagger: 0.03,
    ease: "power2.out",
  });

  // Observar si se inyecta el menú
  const observer = new MutationObserver(() => {
    const btn = document.getElementById("lang-toggle");
    if (btn && !btn.dataset.listenerAdded) {
      btn.dataset.listenerAdded = "true";
      updateButtonLabel();

      btn.addEventListener("click", () => {
        const nextLang = currentLang === "es" ? "en" : "es";
        gsap.to(btn, {
          scale: 0.8,
          duration: 0.15,
          onComplete: () => {
            translatePage(nextLang);
            gsap.to(btn, { scale: 1, duration: 0.15, ease: "back.out(2)" });
          },
        });
      });
    }
  });

  const placeholder = document.getElementById("menu-placeholder");
  if (placeholder) {
    observer.observe(placeholder, { childList: true, subtree: true });
  }
});

// --- Exponer funciones globales ---
window.INDI = window.INDI || {};
window.INDI.translatePage = translatePage;
window.INDI.getCurrentLang = () => currentLang;

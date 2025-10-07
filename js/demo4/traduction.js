// =============================
// 🌐 Sistema de traducción global (corrigido)
// =============================

// 1️⃣ Variables globales unificadas
window.loadedTranslations = window.loadedTranslations || {};
window.currentLang = localStorage.getItem("indi-lang") || "es";

// 2️⃣ Aplicar traducciones en el DOM
function applyTranslations() {
  document.querySelectorAll("[data-i18n]").forEach(el => {
    const key = el.getAttribute("data-i18n");
    const translation = window.loadedTranslations[key] || key;

    if (el.tagName === "IMG") {
      el.alt = translation;
    } else if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
      el.placeholder = translation;
    } else {
      el.innerHTML = translation;
    }
  });
  console.log("🌍 Traducciones aplicadas:", window.currentLang);
}
window.applyTranslations = applyTranslations;

// 3️⃣ Cargar archivo de idioma
function translatePage(lang = "en") {
  console.log(`🌐 Cargando /json/${lang}.json ...`);

  fetch(`/json/${lang}.json`)
    .then(res => {
      if (!res.ok) throw new Error(`No se encontró /json/${lang}.json`);
      return res.json();
    })
    .then(json => {
      // ⚙️ Guardar referencia global
      window.loadedTranslations = json;
      window.currentLang = lang;
      localStorage.setItem("indi-lang", lang);

      // Aplicar al DOM
      applyTranslations();

      // 🔔 Avisar a scripts dependientes (carousel, etc.)
      window.dispatchEvent(new Event("translationsLoaded"));

      // Animación visual
      gsap.fromTo("[data-i18n]", { opacity: 0 }, { opacity: 1, duration: 0.3, stagger: 0.02 });
      updateButtonLabel();
    })
    .catch(err => console.error("❌ Error cargando idioma:", err));
}
window.INDI = window.INDI || {};
window.INDI.translatePage = translatePage;

// 4️⃣ Actualizar botón de idioma
function updateButtonLabel() {
  const btn = document.getElementById("lang-toggle");
  if (!btn) return;
  btn.textContent = window.currentLang === "es" ? "EN" : "ES";
}
window.updateButtonLabel = updateButtonLabel;

// 5️⃣ Asignar funcionalidad al botón
function setupLangButton() {
  const btn = document.getElementById("lang-toggle");
  if (!btn) return;
  if (btn.dataset.listenerAdded === "true") return;

  btn.dataset.listenerAdded = "true";
  updateButtonLabel();

  btn.addEventListener("click", () => {
    const nextLang = window.currentLang === "es" ? "en" : "es";
    gsap.to(btn, {
      scale: 0.8,
      duration: 0.15,
      onComplete: () => {
        translatePage(nextLang);
        gsap.to(btn, { scale: 1, duration: 0.15, ease: "back.out(2)" });
      },
    });
  });

  console.log("🌐 Botón de idioma listo y enlazado");
}

// 6️⃣ Observar menú dinámico y DOM
document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 DOM listo, idioma actual:", window.currentLang);

  // Cargar idioma actual
  translatePage(window.currentLang);

  // Vigilar cambios (por carga dinámica del menú)
  const observer = new MutationObserver(() => setupLangButton());
  observer.observe(document.body, { childList: true, subtree: true });

  // Intento inmediato
  setupLangButton();
});

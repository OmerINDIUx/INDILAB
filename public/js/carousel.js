// =============================
// 🎠 Carrusel + Buscador + i18n sincronizado
// =============================
"use strict";

// Refs (se asignan al iniciar)
let images, phraseEl, searchInput, suggestionEl, blogItems, noResults;

// Estado
let current = 0;
let suggestionIdx = 0;
let charIdx = 0;
let typing = null;
let isTyping = false;
let carouselTimer = null;
let eventsBound = false;

// ===== Utiles i18n (usan lo que cargó traduction.js) =====
function getPhrases() {
  const p = window.loadedTranslations?.phrases || [];
  console.log("🗣️ getPhrases() →", p.length, "frases");
  return p;
}
function getSuggestions() {
  const s = window.loadedTranslations?.suggestions || [];
  console.log("💡 getSuggestions() →", s.length, "sugerencias");
  return s;
}

// ===== Carrusel de imágenes + texto =====
function showSlide(idx) {
  if (!images.length) return;

  images.forEach((img, i) => (img.style.opacity = i === idx ? "1" : "0"));

  const phrases = getPhrases();
  if (phraseEl) {
    if (phrases.length) {
      const safe = idx % phrases.length;
      phraseEl.textContent = phrases[safe] || "";
      console.log("🎞️ Imagen", idx, "→ frase:", phrases[safe]);
    } else {
      phraseEl.textContent = "";
      console.log("🎞️ Imagen", idx, "sin frase (aún no cargan)");
    }
  }
}

function resetCarousel() {
  if (carouselTimer) clearInterval(carouselTimer);
  current = 0;
}

function startCarousel() {
  if (!images.length) {
    console.warn("🚫 No se inicia carrusel (sin imágenes)");
    return;
  }
  showSlide(0);
  carouselTimer = setInterval(() => {
    current = (current + 1) % images.length;
    showSlide(current);
  }, 4000);

  const phrases = getPhrases();
  console.log("✅ Carrusel iniciado con", images.length, "imágenes y", phrases.length, "frases");
}

// ===== Typing de sugerencias =====
function typeSuggestion() {
  const suggestions = getSuggestions();
  if (!suggestions.length || !suggestionEl) return console.warn("⚠️ No hay sugerencias o falta el elemento");

  isTyping = true;
  const text = suggestions[suggestionIdx] || "";
  if (charIdx <= text.length) {
    suggestionEl.textContent = text.substring(0, charIdx);
    charIdx++;
    typing = setTimeout(typeSuggestion, 100);
  } else {
    typing = setTimeout(eraseSuggestion, 1500);
  }
}

function eraseSuggestion() {
  const suggestions = getSuggestions();
  if (!suggestions.length || !suggestionEl) return;

  const text = suggestions[suggestionIdx] || "";
  if (charIdx >= 0) {
    suggestionEl.textContent = text.substring(0, charIdx);
    charIdx--;
    typing = setTimeout(eraseSuggestion, 50);
  } else {
    suggestionIdx = (suggestionIdx + 1) % suggestions.length;
    typing = setTimeout(typeSuggestion, 500);
  }
}

function resetTypingLoop() {
  clearTimeout(typing);
  typing = null;
  suggestionIdx = 0;
  charIdx = 0;
  isTyping = false;
  if (suggestionEl) {
    suggestionEl.textContent = "";
    // Asegura que arranque visible
    suggestionEl.style.display = "inline";
  }
}

function startTypingLoop() {
  const s = getSuggestions();
  if (!suggestionEl || !s.length) {
    console.warn("🚫 No se inicia typing loop (sin sugerencias o sin elemento)");
    return;
  }
  resetTypingLoop();
  console.log("✅ Typing loop iniciado con", s.length, "sugerencias");
  typeSuggestion();
}

// ===== Filtro de blog =====
function filterBlog(query) {
  if (!blogItems?.length || !noResults) return;
  const lower = (query || "").toLowerCase();
  let found = false;
  blogItems.forEach(item => {
    const text = item.textContent.toLowerCase();
    if (text.includes(lower)) {
      item.style.display = "block";
      found = true;
    } else {
      item.style.display = "none";
    }
  });
  noResults.style.display = found ? "none" : "block";
}

// ===== Cachear DOM (después de que exista) =====
function cacheDom() {
  images       = document.querySelectorAll(".carousel-img");
  phraseEl     = document.getElementById("carousel-phrase");
  searchInput  = document.getElementById("carousel-search");
  suggestionEl = document.getElementById("carousel-suggestion");
  blogItems    = document.querySelectorAll(".blog-item1");
  noResults    = document.getElementById("noResults");

  // 🔸 FIX CLAVE: necesitas placeholder para que :placeholder-shown funcione
  if (searchInput && !searchInput.hasAttribute("placeholder")) {
    searchInput.setAttribute("placeholder", " "); // un espacio basta
  }
}

// ===== Eventos de búsqueda (se enlazan cuando el DOM ya existe) =====
function bindEventsOnce() {
  if (eventsBound) return;
  if (!(searchInput && suggestionEl)) return;

  searchInput.addEventListener("input", () => {
    const value = searchInput.value.trim();
    if (value) {
      clearTimeout(typing);
      suggestionEl.style.display = "none";
      isTyping = false;
    } else {
      suggestionEl.style.display = "inline";
      if (!isTyping) startTypingLoop();
    }
    filterBlog(value);
  });

  suggestionEl.addEventListener("click", () => {
    searchInput.value = suggestionEl.textContent;
    suggestionEl.style.display = "none";
    filterBlog(searchInput.value);
  });

  eventsBound = true;
}

// ===== Inicialización tras traducciones =====
function iniciarUI() {
  cacheDom();          // re-selecciona elementos cuando ya existen
  bindEventsOnce();    // engancha listeners una sola vez

  resetCarousel();
  startCarousel();

  // Arranca suggestions desde el inicio, visibles
  resetTypingLoop();
  startTypingLoop();
}

// ===== Orquestación de arranque =====
document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 DOM listo, preparando carrusel...");

  // Si ya hay traducciones cargadas, arranca
  if (window.loadedTranslations?.phrases?.length) {
    console.log("✅ Traducciones ya disponibles al iniciar");
    iniciarUI();
    return;
  }

  // Espera a que traduccion.js avise
  console.log("⏳ Esperando evento translationsLoaded...");
  window.addEventListener("translationsLoaded", () => {
    console.log("✅ Evento translationsLoaded detectado");
    iniciarUI();
  }, { once: true });

  // Fallback por si el evento no llega (seguridad)
  setTimeout(() => {
    if (!window.loadedTranslations?.phrases?.length) {
      console.warn("⚠️ No se recibieron traducciones; iniciando con texto vacío");
      iniciarUI();
    }
  }, 2000);
});

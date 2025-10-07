// =============================
// 🎠 Carrusel + Buscador + i18n sincronizado
// =============================
// === carousel.js ===

// Caché local para las traducciones del carrusel
let carouselTranslations = null;

// Carga las traducciones solo una vez
async function loadCarouselTranslations() {
  if (carouselTranslations) {
    // ✅ Ya cargadas: devuelve el caché
    console.log("⚡ [i18n-carousel] Usando traducciones cacheadas");
    return carouselTranslations;
  }

  console.log("📥 [i18n-carousel] Cargando traducciones...");
  try {
    const res = await fetch('/data/traductions.json');
    carouselTranslations = await res.json();
    console.log("✅ [i18n-carousel] Traducciones cargadas y cacheadas");
  } catch (err) {
    console.error("❌ Error al cargar traducciones del carrusel:", err);
    carouselTranslations = {}; // fallback vacío
  }

  return carouselTranslations;
}

// Función que obtiene la frase traducida desde caché
async function getCarouselPhrase(key) {
  const translations = await loadCarouselTranslations();
  return translations[key] || key; // usa el key como fallback
}

// Ejemplo de uso en el cambio de frases del carrusel
async function updateCarouselText(nextKey) {
  const phrase = await getCarouselPhrase(nextKey);
  const element = document.querySelector(".carousel-text");
  if (element) {
    element.textContent = phrase;
  }
  console.log(`💬 [carousel] Frase mostrada: ${phrase}`);
}

// Simulación del cambio de frase cada 3s
let phrases = ["Materials and processes", "Eco-friendly", "digital tools"];
let index = 0;
setInterval(() => {
  index = (index + 1) % phrases.length;
  updateCarouselText(phrases[index]);
}, 3000);

console.log("Idioma actual:", localStorage.getItem("indi-lang"));
console.log("loadedTranslations =", window.loadedTranslations);

const images       = document.querySelectorAll(".carousel-img");
const phraseEl     = document.getElementById("carousel-phrase");
const searchInput  = document.getElementById("carousel-search");
const suggestionEl = document.getElementById("carousel-suggestion");
const blogItems    = document.querySelectorAll(".blog-item1");
const noResults    = document.getElementById("noResults");

let current = 0;
let suggestionIdx = 0;
let charIdx = 0;
let typing;
let isTyping = false;
let carouselTimer = null;

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

// === Carousel ===
function showSlide(idx) {
  const phrases = getPhrases();
  if (!phrases.length) return;
  images.forEach((img, i) => (img.style.opacity = i === idx ? "1" : "0"));
  phraseEl.textContent = phrases[idx] || "";
  console.log("🎞️ Mostrando frase", idx, "→", phrases[idx]);
}

function resetCarousel() {
  if (carouselTimer) clearInterval(carouselTimer);
  current = 0;
}

function startCarousel() {
  const phrases = getPhrases();
  if (!images.length || !phrases.length) {
    console.warn("🚫 No se inicia carrusel (faltan imágenes o frases)");
    return;
  }
  showSlide(0);
  carouselTimer = setInterval(() => {
    current = (current + 1) % phrases.length;
    showSlide(current);
  }, 4000);
  console.log("✅ Carrusel iniciado con", phrases.length, "frases");
}

// === Typing Loop ===
function typeSuggestion() {
  const suggestions = getSuggestions();
  if (!suggestions.length) return console.warn("⚠️ No hay sugerencias cargadas");
  isTyping = true;
  const currentText = suggestions[suggestionIdx];
  if (charIdx <= currentText.length) {
    suggestionEl.textContent = currentText.substring(0, charIdx);
    charIdx++;
    typing = setTimeout(typeSuggestion, 100);
  } else {
    typing = setTimeout(eraseSuggestion, 1500);
  }
}

function eraseSuggestion() {
  const suggestions = getSuggestions();
  const currentText = suggestions[suggestionIdx];
  if (charIdx >= 0) {
    suggestionEl.textContent = currentText.substring(0, charIdx);
    charIdx--;
    typing = setTimeout(eraseSuggestion, 50);
  } else {
    suggestionIdx = (suggestionIdx + 1) % suggestions.length;
    typing = setTimeout(typeSuggestion, 500);
  }
}

function resetTypingLoop() {
  clearTimeout(typing);
  suggestionIdx = 0;
  charIdx = 0;
  isTyping = false;
  if (suggestionEl) suggestionEl.textContent = "";
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

// === Filtro de blog ===
function filterBlog(query) {
  const lower = query.toLowerCase();
  let found = false;
  blogItems.forEach(item => {
    const text = item.textContent.toLowerCase();
    if (text.includes(lower)) {
      item.style.display = "block";
      found = true;
    } else item.style.display = "none";
  });
  noResults.style.display = found ? "none" : "block";
}

// === Eventos de búsqueda ===
if (searchInput && suggestionEl) {
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
}

// === Esperar traducciones ===
document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 DOM listo, esperando traducciones...");

  const iniciarUI = () => {
    console.log("🟢 Traducciones detectadas, iniciando carrusel y sugerencias");
    resetCarousel();   
    startCarousel();
    resetTypingLoop(); 
    startTypingLoop();
  };

  if (window.loadedTranslations?.phrases?.length > 0) {
    iniciarUI();
  } else {
    window.addEventListener("translationsLoaded", iniciarUI, { once: true });
    console.log("⏳ Esperando evento translationsLoaded...");
  }
});

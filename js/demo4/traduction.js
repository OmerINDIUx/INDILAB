let currentLang = "en"; // idioma por defecto al iniciar

function translatePage(lang = "en") {
  const elements = document.querySelectorAll("[data-i18n]");
  if (elements.length === 0) {
    console.warn("⚠️ No hay elementos [data-i18n] en el DOM todavía");
    return;
  }

  // Fade out
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
        .then(translations => {
          elements.forEach(el => {
            const key = el.getAttribute("data-i18n");
            const translation = translations[key] || key;

            if (el.tagName === "IMG") {
              el.alt = translation;
            } else if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
              el.placeholder = translation;
            } else {
              el.innerHTML = translation;
            }
          });

          // Fade in
          gsap.fromTo(
            elements,
            { opacity: 0, scale: 0.95 },
            {
              opacity: 1,
              scale: 1,
              duration: 0.5,
              stagger: 0.02,
              ease: "back.out(1.4)",
            }
          );

          currentLang = lang;
        })
        .catch((err) => console.error("Error cargando idioma:", err));
    },
  });
}

// Cambio de idioma con animación del botón
document.getElementById("lang-toggle")?.addEventListener("click", () => {
  const btn = document.getElementById("lang-toggle");
  const nextLang = currentLang === "en" ? "es" : "en";

  gsap.to(btn, {
    scale: 0.8,
    duration: 0.15,
    onComplete: () => {
      btn.textContent = nextLang.toUpperCase();
      translatePage(nextLang);

      gsap.to(btn, {
        scale: 1,
        duration: 0.15,
        ease: "back.out(2)",
      });
    },
  });
});

// 🚀 Ahora esperamos a que el menú esté cargado antes de traducir/animar
window.addEventListener("DOMContentLoaded", () => {
  fetch('/menu.html')
    .then(response => {
      if (!response.ok) {
        throw new Error(`Error al cargar el menú: ${response.status} ${response.statusText}`);
      }
      return response.text();
    })
    .then(data => {
      const placeholder = document.getElementById('menu-placeholder');
      if (!placeholder) throw new Error("Elemento con id 'menu-placeholder' no encontrado en el DOM");
      placeholder.innerHTML = data;

      // Animación inicial cuando ya existen los [data-i18n]
      gsap.from("[data-i18n]", {
        opacity: 0,
        y: 20,
        duration: 0.5,
        stagger: 0.03,
        ease: "power2.out",
      });

      // Traducción inicial
      translatePage(currentLang);

      // Cargar el JS del menú después de insertarlo
      const script = document.createElement('script');
      script.src = "/js/demo4/menu.js";
      script.onload = () => console.log("menu.js cargado correctamente ✅");
      script.onerror = () => console.error("Error al cargar /js/demo4/menu.js ❌");
      document.body.appendChild(script);
    })
    .catch(error => {
      console.error("Ocurrió un error al cargar el menú:", error);
    });
});

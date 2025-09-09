let currentLang = 'en'; // idioma por defecto al iniciar

function translatePage(lang = 'en') {
  const elements = document.querySelectorAll('[data-i18n]');

  // Fade out
  gsap.to(elements, {
    opacity: 0,
    scale: 0.95,
    duration: 0.3,
    stagger: 0.02,
    ease: "power1.in",
    onComplete: () => {
      fetch(`./json/${lang}.json`)
        .then(res => res.json())
        .then(translations => {
          elements.forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = translations[key] || key;

            if (el.tagName === 'IMG') {
              el.alt = translation;
            } else if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
              el.placeholder = translation;
            } else {
              el.innerHTML = translation;
            }
          });

          // Fade in
          gsap.fromTo(elements,
            { opacity: 0, scale: 0.95 },
            {
              opacity: 1,
              scale: 1,
              duration: 0.5,
              stagger: 0.02,
              ease: 'back.out(1.4)'
            }
          );

          // Actualiza el idioma actual
          currentLang = lang;
        })
        .catch(err => console.error("Error cargando idioma:", err));
    }
  });
}

// Cambio de idioma con animación del botón
document.getElementById('lang-toggle')?.addEventListener('click', () => {
  const btn = document.getElementById('lang-toggle');
  const nextLang = currentLang === 'en' ? 'es' : 'en';

  gsap.to(btn, {
    scale: 0.8,
    duration: 0.15,
    onComplete: () => {
      btn.textContent = nextLang.toUpperCase(); // Mostrar a qué idioma va a cambiar
      translatePage(nextLang); // Traducir al nuevo idioma

      gsap.to(btn, {
        scale: 1,
        duration: 0.15,
        ease: "back.out(2)"
      });
    }
  });
});

// Animación inicial y carga en inglés
window.addEventListener('DOMContentLoaded', () => {
  gsap.from('[data-i18n]', {
    opacity: 0,
    y: 20,
    duration: 0.5,
    stagger: 0.03,
    ease: "power2.out"
  });

  translatePage(currentLang); // usa 'en' por defecto
});

(() => {
  try {
    console.log("✅ Script menu.js inicializado...");

    // Verificar GSAP
    if (typeof gsap === "undefined") {
      console.error("❌ Error: GSAP no está cargado. Revisa la importación del script.");
      return; // ✅ ahora sí se puede usar porque estamos dentro de una función
    }
    console.log("✅ GSAP detectado correctamente.");

    // Obtener elementos del DOM
    const menuToggle = document.getElementById('menu-toggle');
    const fullscreenMenu = document.getElementById('fullscreen-menu');
    const closeButton = document.getElementById('close-menu');
    const menuLinks = fullscreenMenu?.querySelectorAll('.menu__item') || [];

    if (!fullscreenMenu) {
      console.error("❌ Error: No se encontró el elemento #fullscreen-menu en el DOM.");
      return;
    }
    console.log("✅ #fullscreen-menu encontrado en el DOM.");

    let menuOpen = false;

    function openMenu() {
      try {
        if (menuOpen) return;
        menuOpen = true;
        fullscreenMenu.classList.add('show');

        gsap.set(fullscreenMenu, {
          transformOrigin: "top left",
          transformPerspective: 1200
        });

        gsap.fromTo(
          fullscreenMenu,
          { rotateZ: 90, rotateY: -90, opacity: 1 },
          { rotateZ: 0, rotateY: 0, opacity: 1, duration: 1.5, ease: "power4.out" }
        );

        console.log("✅ Menú abierto correctamente.");
      } catch (err) {
        console.error("❌ Error al abrir el menú:", err);
      }
    }

    function closeMenu() {
      try {
        if (!menuOpen) return;
        menuOpen = false;
        gsap.to(fullscreenMenu, {
          rotateZ: 90,
          rotateY: -90,
          opacity: 0,
          transformOrigin: "top left",
          transformPerspective: 1200,
          duration: 0.6,
          ease: "power4.in",
          onComplete: () => {
            fullscreenMenu.classList.remove('show');
            console.log("✅ Menú cerrado correctamente.");
          }
        });
      } catch (err) {
        console.error("❌ Error al cerrar el menú:", err);
      }
    }

    // Listeners
    if (menuToggle) {
      menuToggle.addEventListener('click', () => {
        if (!menuOpen) openMenu();
      });
      console.log("✅ Listener agregado a #menu-toggle.");
    } else {
      console.warn("⚠️ Advertencia: No se encontró #menu-toggle en el DOM.");
    }

    if (closeButton) {
      closeButton.addEventListener('click', closeMenu);
      console.log("✅ Listener agregado a #close-menu.");
    } else {
      console.warn("⚠️ Advertencia: No se encontró #close-menu en el DOM.");
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menuOpen) closeMenu();
    });
    console.log("✅ Listener agregado para tecla ESC.");

    if (menuLinks.length > 0) {
      menuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          try {
            e.preventDefault();
            closeMenu();
            const href = link.getAttribute('href');

            if (href && href.startsWith('#')) {
              setTimeout(() => {
                const target = document.querySelector(href);
                if (target) {
                  target.scrollIntoView({ behavior: 'smooth' });
                  console.log(`✅ Scroll hacia ${href} realizado correctamente.`);
                } else {
                  console.warn(`⚠️ Advertencia: No se encontró el destino ${href} en el DOM.`);
                }
              }, 700);
            } else if (href) {
              console.log(`➡️ Redirigiendo a: ${href}`);
              window.location.href = href;
            } else {
              console.warn("⚠️ Advertencia: Link sin 'href' detectado.");
            }
          } catch (err) {
            console.error("❌ Error al manejar el click de un enlace del menú:", err);
          }
        });
      });
      console.log(`✅ ${menuLinks.length} enlaces del menú listos.`);
    } else {
      console.warn("⚠️ Advertencia: No se encontraron enlaces con clase .menu__item.");
    }

    console.log("🎉 Menú inicializado correctamente.");

  } catch (error) {
    console.error("❌ Error crítico en la inicialización del menú:", error);
  }
})();

(() => {
  try {
    console.log("✅ Script menu.js inicializado...");

    if (typeof gsap === "undefined") {
      console.error("❌ Error: GSAP no está cargado. Revisa la importación del script.");
      return;
    }
    console.log("✅ GSAP detectado correctamente.");

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

    // ✅ openMenu mejorada (con animación de los links)
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

        // 🔹 Animación en cascada para los enlaces
        gsap.fromTo(
          menuLinks,
          { opacity: 0, y: -10, paddingTop: 0, paddingBottom: 0 },
          {
            opacity: 1,
            y: 0,
            paddingTop: "1.5rem",
            paddingBottom: "1.5rem",
            duration: 0.6,
            ease: "power3.out",
            stagger: 0.1,
            delay: 0.3
          }
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

        gsap.to(menuLinks, {
          opacity: 0,
          y: -10,
          paddingTop: 0,
          paddingBottom: 0,
          duration: 0.3,
          ease: "power2.in",
          stagger: 0.05
        });

        gsap.to(fullscreenMenu, {
          rotateZ: 90,
          rotateY: -90,
          opacity: 0,
          transformOrigin: "top left",
          transformPerspective: 1200,
          duration: 0.6,
          ease: "power4.in",
          delay: 0.2,
          onComplete: () => {
            fullscreenMenu.classList.remove('show');
            console.log("✅ Menú cerrado correctamente.");
          }
        });
      } catch (err) {
        console.error("❌ Error al cerrar el menú:", err);
      }
    }

    // Listeners de botones
    if (menuToggle) {
      menuToggle.addEventListener('click', () => {
        if (!menuOpen) openMenu();
      });
      console.log("✅ Listener agregado a #menu-toggle.");
    }

    if (closeButton) {
      closeButton.addEventListener('click', closeMenu);
      console.log("✅ Listener agregado a #close-menu.");
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && menuOpen) closeMenu();
    });

    if (menuLinks.length > 0) {
      menuLinks.forEach((link, index) => {
        // Click para cerrar y navegar
        link.addEventListener('click', (e) => {
          e.preventDefault();
          closeMenu();
          const href = link.getAttribute('href');

          if (href && href.startsWith('#')) {
            setTimeout(() => {
              const target = document.querySelector(href);
              if (target) target.scrollIntoView({ behavior: 'smooth' });
            }, 700);
          } else if (href) {
            window.location.href = href;
          }
        });

        // 🔹 Hover interactivo
        link.addEventListener("mouseenter", () => {
          menuLinks.forEach((other, i) => {
            if (i > index) {
              gsap.to(other, {
                y: 10,
                duration: 0.25,
                ease: "power2.out"
              });
            }
          });
          // 🔹 Escalar ligeramente el link activo
          gsap.to(link, {
            scale: 1.05,
            duration: 0.2,
            ease: "power2.out"
          });
        });

        link.addEventListener("mouseleave", () => {
          menuLinks.forEach((other) => {
            gsap.to(other, {
              y: 0,
              duration: 0.25,
              ease: "power2.inOut"
            });
          });
          // 🔹 Regresar a escala normal
          gsap.to(link, {
            scale: 1,
            duration: 0.2,
            ease: "power2.inOut"
          });
        });
      });

      console.log(`✅ ${menuLinks.length} enlaces del menú listos.`);
    }

    console.log("🎉 Menú inicializado correctamente.");
  } catch (error) {
    console.error("❌ Error crítico en la inicialización del menú:", error);
  }
})();

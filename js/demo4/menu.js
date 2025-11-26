(() => {
  try {
    console.log("✅ Script menu.js inicializado...");

    // --- Verificación de GSAP ---
    if (typeof gsap === "undefined") {
      console.error("❌ GSAP no está cargado. Revisa la importación del script.");
      return;
    }
    console.log("✅ GSAP detectado correctamente.");

    // --- Elementos principales ---
    const menuToggle = document.getElementById("menu-toggle");
    const fullscreenMenu = document.getElementById("fullscreen-menu");
    const closeButton = document.getElementById("close-menu");
    const menuLinks = fullscreenMenu?.querySelectorAll(".menu__item") || [];

    if (!fullscreenMenu) {
      console.error("❌ No se encontró #fullscreen-menu en el DOM.");
      return;
    }

    console.log("✅ #fullscreen-menu encontrado en el DOM.");
    let menuOpen = false;

    // --- Timeline principal (control centralizado de animaciones) ---
    const menuTimeline = gsap.timeline({
      paused: true,
      defaults: { ease: "power4.out" },
      onStart: () => fullscreenMenu.classList.add("show"),
      onReverseComplete: () => {
        fullscreenMenu.classList.remove("show");
        document.body.classList.remove("menu-open");
        console.log("✅ Menú cerrado y clase removida.");
      },
    });

    // --- Animación del contenedor ---
    menuTimeline.fromTo(
      fullscreenMenu,
      { rotateZ: 90, rotateY: -90, opacity: 0, transformOrigin: "top left" },
      { rotateZ: 0, rotateY: 0, opacity: 1, duration: 1.2 }
    );

    // --- Animación de los enlaces ---
    if (menuLinks.length > 0) {
      menuTimeline.fromTo(
        menuLinks,
        { opacity: 0, y: -10, paddingTop: 0, paddingBottom: 0 },
        {
          opacity: 1,
          y: 0,
          paddingTop: "1.5rem",
          paddingBottom: "1.5rem",
          duration: 0.6,
          stagger: 0.08,
          ease: "power3.out",
        },
        "-=0.8" // empieza antes de terminar la animación del menú
      );
    }

    // --- Funciones principales ---
    function openMenu() {
      if (menuOpen) return;
      menuOpen = true;
      document.body.classList.add("menu-open");
      menuTimeline.play();
      console.log("✅ Menú abierto correctamente.");
    }

    function closeMenu() {
      if (!menuOpen) return;
      menuOpen = false;
      menuTimeline.reverse();
    }

    // --- Eventos ---
    if (menuToggle) {
      menuToggle.addEventListener("click", () => (menuOpen ? closeMenu() : openMenu()));
      console.log("✅ Listener agregado a #menu-toggle.");
    }

    if (closeButton) {
      closeButton.addEventListener("click", closeMenu);
      console.log("✅ Listener agregado a #close-menu.");
    }

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && menuOpen) closeMenu();
    });

    // --- Links del menú (click + hover) ---
    menuLinks.forEach((link, index) => {
      // 🔹 Cerrar menú y navegar
      link.addEventListener("click", (e) => {
        e.preventDefault();
        closeMenu();
        const href = link.getAttribute("href");
        if (!href) return;

        setTimeout(() => {
          if (href.startsWith("#")) {
            const target = document.querySelector(href);
            if (target) target.scrollIntoView({ behavior: "smooth" });
          } else {
            window.location.href = href;
          }
        }, 700);
      });

      // 🔹 Efectos hover
      link.addEventListener("mouseenter", () => {
        menuLinks.forEach((other, i) => {
          if (i > index) gsap.to(other, { y: 10, duration: 0.25, ease: "power2.out" });
        });
        gsap.to(link, { scale: 1.05, duration: 0.2, ease: "power2.out" });
      });

      link.addEventListener("mouseleave", () => {
        menuLinks.forEach((other) =>
          gsap.to(other, { y: 0, duration: 0.25, ease: "power2.inOut" })
        );
        gsap.to(link, { scale: 1, duration: 0.2, ease: "power2.inOut" });
      });
    });

    console.log(`✅ ${menuLinks.length} enlaces del menú listos.`);
    console.log("🎉 Menú inicializado correctamente.");
  } catch (error) {
    console.error("❌ Error crítico en la inicialización del menú:", error);
  }
})();

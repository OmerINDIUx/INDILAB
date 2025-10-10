// ==================================================
// ✨ ANIMACIÓN DE ESQUINAS CON GSAP + SCROLLTRIGGER
// ==================================================
window.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const bottomLeft = document.querySelector(".corner-bottom-left");
  const topRight = document.querySelector(".corner-top-right");

  // ==================================================
  // 📱 BREAKPOINTS
  // ==================================================
  const breakpoints = {
    largeDesktop: "(min-width: 1441px)",
    desktop: "(min-width: 1081px) and (max-width: 1440px)",
    laptop: "(min-width: 901px) and (max-width: 1080px)",
    tablet: "(min-width: 769px) and (max-width: 900px)",
    mobileLarge: "(min-width: 481px) and (max-width: 768px)",
    mobileSmall: "(max-width: 480px)",
  };

  // ==================================================
  // 🧩 COORDENADAS BASE (escala relativa)
  // ==================================================
  const cornerEndpoints = {
    largeDesktop: {
      bottomLeft: { x: - 0.2, y: 0.58 },
      topRight: { x: 0.25, y: 0.37 },
      centerOffset: { x: -0.28, y: 0 },
    },
    desktop: {
      bottomLeft: { x: - 0.3, y: 0.58 },
      topRight: { x: 0.21, y: 0.37 },
      centerOffset: { x: -0.26, y: 0 },
    },
    laptop: {
      bottomLeft: { x: -0.05, y: 0.85 },
      topRight: { x: 0.68, y: 0.50 },
      centerOffset: { x: -0.24, y: 0 },
    },
    tablet: {
      bottomLeft: { x: -0.10, y: 0.85 },
      topRight: { x: 0.60, y: 0.50 },
      centerOffset: { x: -0.22, y: 0 },
    },
    mobileLarge: {
      bottomLeft: { x: 0.05, y: 0.90 },
      topRight: { x: 0.55, y: 0.45 },
      centerOffset: { x: -0.12, y: 0 },
    },
    mobileSmall: {
      bottomLeft: { x: 0.02, y: 0.90 },
      topRight: { x: 0.48, y: 0.45 },
      centerOffset: { x: -0.18, y: 0 },
    },
  };

  // ==================================================
  // 📐 CALCULAR POSICIONES REALES
  // ==================================================
  function getCornerPositions(context) {
    const w = window.innerWidth;
    const h = window.innerHeight;

    const activeKey = Object.keys(cornerEndpoints).find((key) => context.conditions[key]);
    const cfg = cornerEndpoints[activeKey];

    return {
      bottomLeft: {
        x: w * cfg.bottomLeft.x,
        y: h * cfg.bottomLeft.y,
      },
      topRight: {
        x: w * cfg.topRight.x,
        y: h * cfg.topRight.y,
      },
      center: {
        x: w * (0.5 + cfg.centerOffset.x),
        y: h * 0.5 + cfg.centerOffset.y,
      },
    };
  }

  // ==================================================
  // 🎬 ANIMACIÓN PRINCIPAL
  // ==================================================
  function animateCorners(context) {
    const pos = getCornerPositions(context);

    // Posición inicial → centro
    gsap.set([bottomLeft, topRight], {
      position: "absolute",
      x: pos.center.x,
      y: pos.center.y,
      opacity: 0,
    });

    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: "#brand",
        start: "bottom center",
        end: "bottom top",
        scrub: true,
        invalidateOnRefresh: true,
        // markers: true,
        onRefresh: () => {
          const newPos = getCornerPositions(context);
          gsap.set([bottomLeft, topRight], {
            x: newPos.center.x,
            y: newPos.center.y,
          });
        },
        onLeaveBack: () => {
          const newPos = getCornerPositions(context);
          gsap.to([bottomLeft, topRight], {
            x: newPos.center.x,
            y: newPos.center.y,
            opacity: 0,
            duration: 0.6,
            ease: "power2.out",
          });
        },
      },
    });

    tl.to(bottomLeft, {
      x: pos.bottomLeft.x,
      y: pos.bottomLeft.y,
      opacity: 1,
      ease: "power3.out",
    }).to(
      topRight,
      {
        x: pos.topRight.x,
        y: pos.topRight.y,
        opacity: 1,
        ease: "power3.out",
      },
      "<"
    );

    return tl;
  }

  // ==================================================
  // ⚙️ MATCHMEDIA RESPONSIVO
  // ==================================================
  const mm = gsap.matchMedia();

  function initMatchMedia() {
    mm.add(breakpoints, (context) => {
      const tl = animateCorners(context);
      ScrollTrigger.refresh();
      return () => tl.kill(); // Limpieza cuando cambia el breakpoint
    });
  }

  initMatchMedia();

  // ==================================================
  // 🔄 REFRESH AL RESIZE (con debounce)
  // ==================================================
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      mm.kill();
      ScrollTrigger.getAll().forEach((st) => st.kill());
      initMatchMedia();
    }, 300);
  });
});

window.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const bottomLeft = document.querySelector(".corner-bottom-left");
  const topRight = document.querySelector(".corner-top-right");
  const content = document.querySelector(".content--intro");

  // ==================================================
  // 🧩 CONFIGURACIONES POR BREAKPOINT
  // ==================================================
  const cornerEndpoints = {
    largeDesktop: {
      bottomLeft: { x: 46, y: 650 },
      topRight: { x: 675, y: 400 },
      centerOffset: { x: -400, y: 0 },
    },
    desktop: {
      bottomLeft: { x: 46, y: 650 },
      topRight: { x: 675, y: 400 },
      centerOffset: { x: -380, y: 0 },
    },
    laptop: {
      bottomLeft: { x: -230, y: 610 },
      topRight: { x: 600, y: 445 },
      centerOffset: { x: -360, y: 0 },
    },
    tablet: {
      bottomLeft: { x: -190, y: 610 },
      topRight: { x: 520, y: 445 },
      centerOffset: { x: -340, y: 0 },
    },
    mobileLarge: {
      bottomLeft: { x: -5, y: 640 },
      topRight: { x: 430, y: 390 },
      centerOffset: { x: -150, y: 0 },
    },
    mobileSmall: {
      bottomLeft: { x: -55, y: 640 },
      topRight: { x: 335, y: 400 },
      centerOffset: { x: -260, y: 0 },
    },
  };

  // ==================================================
  // 📐 FUNCIÓN PARA CALCULAR POSICIONES REALES
  // ==================================================
  function getCornerPositions(context) {
    const w = window.innerWidth;
    const h = window.innerHeight;

    // Detecta el breakpoint activo
    const activeKey = Object.keys(cornerEndpoints).find((key) => context.conditions[key]);
    const cfg = cornerEndpoints[activeKey];

    // Calcula coordenadas absolutas con offsets proporcionales al viewport
    return {
      bottomLeft: {
        x: (-w / 4) + cfg.bottomLeft.x,
        y: cfg.bottomLeft.y,
      },
      topRight: {
        x: (-w / 4) + cfg.topRight.x,
        y: cfg.topRight.y,
      },
      center: {
        x: w / 4 + cfg.centerOffset.x,
        y: h / 4 + cfg.centerOffset.y,
      },
    };
  }

  // ==================================================
  // 🎬 FUNCIÓN DE ANIMACIÓN
  // ==================================================
  function animateCorners(context) {
    const pos = getCornerPositions(context);

    // Posición inicial → centro de la pantalla
    gsap.set([bottomLeft, topRight], {
      position: "absolute",
      x: pos.center.x,
      y: pos.center.y,
      opacity: 0,
    });

    // Timeline con ScrollTrigger
    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: "#brand",
        start: "bottom center",
        end: "bottom top",
        scrub: true,
        // markers: true,
        onLeaveBack: () => {
          // Retorno suave al centro al volver hacia arriba
          gsap.to([bottomLeft, topRight], {
            x: pos.center.x,
            y: pos.center.y,
            opacity: 0,
            duration: 0.6,
            ease: "power2.out",
          });
        },
      },
    });

    // Animación simultánea de las esquinas
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
  // 📱 MATCHMEDIA RESPONSIVO
  // ==================================================
  const mm = gsap.matchMedia();

  mm.add(
    {
      largeDesktop: "(min-width: 1441px)",
      desktop: "(min-width: 1081px) and (max-width: 1440px)",
      laptop: "(min-width: 901px) and (max-width: 1080px)",
      tablet: "(min-width: 769px) and (max-width: 900px)",
      mobileLarge: "(min-width: 481px) and (max-width: 768px)",
      mobileSmall: "(max-width: 480px)",
    },
    (context) => {
      animateCorners(context);
    }
  );

  // ==================================================
  // 🔄 REACCIÓN AL RESIZE (con debounce)
  // ==================================================
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      ScrollTrigger.getAll().forEach((st) => st.kill());
      mm.revert();
      mm.refresh();
    }, 300);
  });
});

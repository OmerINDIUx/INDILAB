// ==================================================
// ✨ ESQUINAS AUTOCALCULADAS — HALF & FULL SCREEN
// ==================================================
window.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const bottomLeft = document.querySelector(".corner-bottom-left");
  const topRight = document.querySelector(".corner-top-right");
  const content = document.querySelector(".content--intro");

  let tl;

  // -------------------------------------------------------------------
  // 📐 Obtener posiciones según modo (half-screen o full-screen)
  // -------------------------------------------------------------------
function getPositions() {
  const w = window.innerWidth;
  const h = window.innerHeight;
  const isHalf = w >= 1080;

  if (isHalf) {
    // === HALF SCREEN (≥1080px) ===
    return {
      mode: "half",
      center: { x: w / 2, y: h / 2 }, // Punto inicial visible
      bottomLeft: { x: -w/6, y:  h /3 + h/4 +20 },
      topRight: { x:w / 6 -10, y: h /3 + 10}
    };
  }

  // === FULL SCREEN (≤1079px) ===
  return {
    mode: "full",
    center: { x: w / 2 - 25, y: h / 2 - 25 },
    bottomLeft: { x: -w / 2 + 20, y: h / 2 + 50 },
    topRight: { x: w / 3 - 20, y: h / 3 + 50 }
  };
}


  // -------------------------------------------------------------------
  // 🎬 Crear la animación según el modo activo
  // -------------------------------------------------------------------
  function animateCorners() {
    const pos = getPositions();

    gsap.set([bottomLeft, topRight], {
      position: "absolute",
      x: pos.center.x,
      y: pos.center.y,
      opacity: 0,
    });

    const triggerElement = pos.mode === "half" ? ".content--intro" : "#brand";

    const timeline = gsap.timeline({
      scrollTrigger: {
        trigger: triggerElement,
        start: "top center",
        end: "bottom bottom",
        scrub: true,
      },
    });

    timeline
      .to(bottomLeft, {
        x: pos.bottomLeft.x,
        y: pos.bottomLeft.y,
        opacity: 1,
        ease: "power3.out",
      })
      .to(
        topRight,
        {
          x: pos.topRight.x,
          y: pos.topRight.y,
          opacity: 1,
          ease: "power3.out",
        },
        "<"
      );

    return timeline;
  }

  // -------------------------------------------------------------------
  // 🚀 Inicializar (con pequeño delay para asegurar tamaños)
  // -------------------------------------------------------------------
  setTimeout(() => {
    tl = animateCorners();
  }, 300);

  // -------------------------------------------------------------------
  // 🔄 Recalcular en resize (sin parpadeos)
  // -------------------------------------------------------------------
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      if (tl) tl.kill();
      ScrollTrigger.getAll().forEach((st) => st.kill());
      tl = animateCorners();
    }, 200);
  });
});

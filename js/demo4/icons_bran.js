window.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const bottomLeft = document.querySelector(".corner-bottom-left");
  const topRight   = document.querySelector(".corner-top-right");

  // 🔧 Detectar tamaño de pantalla y ajustar posiciones
  function getCornerPositions() {
    const w = window.innerWidth;

    if (w <= 768) {
      // 📱 Móviles
      return {
        bottomLeft: { left: "0%",  top: "80%" },
        topRight:   { left: "83%", top: "20%" },
        scale: 0.18
      };
    } else if (w <= 1024) {
      // 📱 Tablets
      return {
        bottomLeft: { left: "2%",  top: "65%" },
        topRight:   { left: "88%", top: "28%" },
        scale: 0.22
      };
    } else {
      // 🖥️ Desktop
      return {
        bottomLeft: { left: "0%",  top: "60%" },
        topRight:   { left: "85%", top: "35%" },
        scale: 0.25
      };
    }
  }

  // Obtener posiciones iniciales según pantalla
  let positions = getCornerPositions();

  // Posición inicial en el centro (antes de animar)
  gsap.set([bottomLeft, topRight], {
    xPercent: -50,
    yPercent: -50,
    left: "50%",
    top: "50%",
    position: "absolute",
    opacity: 0,
    scale: positions.scale
  });

  // Timeline animado
  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: "#map",
      start: "top center",
      toggleActions: "play none none reverse"
    }
  });

  tl.to(bottomLeft, {
    duration: 1.5,
    left: positions.bottomLeft.left,
    top: positions.bottomLeft.top,
    xPercent: 0,
    yPercent: -50,
    opacity: 1,
    ease: "power3.out"
  })
  .to(topRight, {
    duration: 1.5,
    left: positions.topRight.left,
    top: positions.topRight.top,
    xPercent: -50,
    yPercent: 0,
    opacity: 1,
    ease: "power3.out"
  }, "<");

  // 🔄 Recalcular al cambiar el tamaño de la ventana
  window.addEventListener("resize", () => {
    positions = getCornerPositions();
    gsap.set([bottomLeft, topRight], { scale: positions.scale });
    // Opcional: actualizar timeline si quieres que cambie en tiempo real
    tl.invalidate().restart(); // reinicia animación con nuevas posiciones
  });
});

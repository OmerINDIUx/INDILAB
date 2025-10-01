window.addEventListener("DOMContentLoaded", () => {
  gsap.registerPlugin(ScrollTrigger);

  const bottomLeft = document.querySelector(".corner-bottom-left");
  const topRight   = document.querySelector(".corner-top-right");
        const content = document.querySelector(".content--intro");


  // 🔧 Detectar tamaño de pantalla y devolver coordenadas absolutas
  function getCornerPositions() {
    const w = window.innerWidth;
    const h = window.innerHeight;

    return {
      bottomLeft: { x: (- w / 4) + 165,  y:  585 },    // un poco fuera abajo/izquierda
      topRight:   { x: (- w / 4) + 793,  y: + 330 },     // un poco fuera arriba/derecha
      center:     { x: w / 4 - 400, y: h / 4 } // centro exacto menos offset
    };
  }

  function animateCorners() {
    const pos = getCornerPositions();

    // Posición inicial → centro de la pantalla
    gsap.set([bottomLeft, topRight], {
      position: "absolute",
      x: pos.center.x,
      y: pos.center.y,
      opacity: 0
    });

    // Timeline con scroll
    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: "#brand",   // 👈 ajusta al contenedor que quieras
        start: "bottom center",
        end: "bottom top",
        scrub: true,
        // markers: true
      }
    });
      tl.to(bottomLeft, {
          x: pos.bottomLeft.x,
          y: pos.bottomLeft.y,
          opacity: 1,
          ease: "power3.out"
        }).to(topRight, {
          x: pos.topRight.x,
          y: pos.topRight.y,
          opacity: 1,
          ease: "power3.out"
        }, "<");

        return tl;
      }


  // Inicializar
  animateCorners();

  // 🔄 Recalcular al cambiar tamaño
  window.addEventListener("resize", () => {
    animateCorners();
  });
});

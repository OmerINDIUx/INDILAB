document.addEventListener("DOMContentLoaded", async () => {
  const [squareText, rectText] = await Promise.all([
    fetch("../../svg/Principal_Brand.svg").then((r) => r.text()),
    fetch("../../svg/Terciary_Brand.svg").then((r) => r.text()),
  ]);

  const squareContainer = document.getElementById("svg-square");
  const rectContainer = document.getElementById("svg-rect");

  squareContainer.innerHTML = squareText;
  rectContainer.innerHTML = rectText;

  const squareSvg = squareContainer.querySelector("svg");
  const rectSvg = rectContainer.querySelector("svg");

  const ids = ["INDI", "LAB", "keyTop", "keyButton"];
  const squareGroup = squareSvg.querySelector("#IndiGrup");
  const rectGroup = rectSvg.querySelector("#IndiGrup");

  function setupResponsiveAnimation() {
    const wrapper = document.getElementById("svg-wrapper");
    const wrapperRect = wrapper.getBoundingClientRect();

    // 🔧 FACTORES DINÁMICOS según pantalla
    // 🔧 Escala inicial del logo cuadrado según tamaño de pantalla
    const scaleDesktop =
      window.innerWidth > 1024
        ? 0.25 // Si es DESKTOP → escala al 25% de su tamaño original
        : window.innerWidth > 768
        ? 0.18 // Si es TABLET → escala al 18%
        : 0.5; // Si es MÓVIL → escala al 50% (lo hace más grande en pantallas chicas)

    // 🔧 Escala inicial del logo rectangular (objetivo de la animación)
    const scaleRect =
      window.innerWidth > 1024
        ? 0.15 // Desktop → 15%
        : window.innerWidth > 768
        ? 0.12 // Tablet → 12%
        : 0.09; // Móvil → 9%

    // 📍 Posiciones iniciales del grupo destino (rectángulo)
    const targetX = wrapperRect.width * 0;
    // → Mueve el rectángulo un 2% del ancho hacia la derecha

    const targetY = wrapperRect.height * 0;
    // → Mueve el rectángulo 200% hacia abajo (probablemente para posicionarlo muy abajo al inicio)

    // 📍 Posición vertical inicial del grupo cuadrado (squareGroup)
    const yInitial =
      window.innerWidth < 768
        ? "25%" // En MÓVIL → empieza un 15% más abajo
        : "5%"; // En DESKTOP/TABLET → empieza solo un 5% más abajo

    // ✅ Establecer posición y escala inicial del logo cuadrado
    gsap.set(squareGroup, {
      scale: scaleDesktop,
      y: yInitial,
      transformOrigin: "top center",
      // → Aplica transformaciones tomando como referencia la parte superior central
    });

    // ✅ Establecer posición y escala inicial del logo rectangular
    gsap.set(rectGroup, {
      scale: scaleRect,
      x: targetX,
      y: targetY,
      transformOrigin: "top left",
      // → Usa esquina superior izquierda como punto de referencia
    });

    // 📏 Ajustes de animación (posición final del cuadrado)

    // xx → DESPLAZAMIENTO HORIZONTAL
    const xx =
      -wrapperRect.width *
      (window.innerWidth > 1024
        ? 0.235 // Desktop → lo mueve bastante a la izquierda
        : window.innerWidth > 768
        ? 0.25 // Tablet → lo mueve un poco menos
        : 1); // Móvil → lo mueve todavía menos (para que no se salga de vista)

    // yx → DESPLAZAMIENTO VERTICAL
    const yx =
      wrapperRect.height *
      (window.innerWidth > 1024
        ? -.001 // Desktop → baja 1% del alto
        : window.innerWidth > 768
        ? 0.03 // Tablet → baja un poco más (3%)
        : -0.01); // Móvil → baja aún más (6%) para dejar espacio visual

    const xscale =
      window.innerWidth > 1024
        ? 0.040 // Desktop: escala final muy pequeña
        : window.innerWidth > 768
        ? 0.06 // Tablet: un poco más grande
        : 0.2; // Móvil: aún más grande para compensar pantallas pequeñas

    gsap.registerPlugin(ScrollTrigger);
    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: "#svg-wrapper",
        start: "top top",
        end: "center top",
        scrub: 1,
        markers: false,
      },
    });

    ids.forEach((id) => {
      const fromEl = squareSvg.querySelector(`#${id}`);
      const toEl = rectSvg.querySelector(`#${id}`);
      if (!fromEl || !toEl) return;

      const fromBox = fromEl.getBBox();
      const toBox = toEl.getBBox();

      const dx = toBox.x + toBox.width / 2 - (fromBox.x + fromBox.width / 2);
      const dy = toBox.y + toBox.height / 2 - (fromBox.y + fromBox.height / 2);

      const scaleX = toBox.width / fromBox.width;
      const scaleY = toBox.height / fromBox.height;
      const scale = Math.min(scaleX, scaleY);

      gsap.set(fromEl, { transformOrigin: "50% 50%" });

      tl.to(
        fromEl,
        {
          x: dx,
          y: dy,
          scale: scale,
          ease: "power2.inOut",
        },
        0
      );
    });

    tl.to(
      squareGroup,
      {
        x: xx,
        y: yx,
        scale: xscale,
        duration: 0.5,
        transformOrigin: "top left",
        ease: "power2.out",
      },
      0
    );

    const keyButton = squareSvg.querySelector("#keyButton");
    if (keyButton) {
      tl.to(
        keyButton,
        {
          opacity: 0,
          duration: 0.5,
          ease: "power2.out",
        },
        0
      );
    }
  }

  setupResponsiveAnimation();
  window.addEventListener("resize", () => {
    ScrollTrigger.getAll().forEach((st) => st.kill()); // reinicia scrollTrigger
    setupResponsiveAnimation(); // recalcula todo
  });
});

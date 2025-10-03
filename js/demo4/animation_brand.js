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

    // 🔧 Escala inicial del logo cuadrado según tamaño de pantalla
    const scaleDesktop =
      window.innerWidth > 1024 ? 0.2 : window.innerWidth > 768 ? 0.35 : 0.5;

    // 🔧 Escala inicial del logo rectangular
    const scaleRect =
      window.innerWidth > 1024 ? 0.15 : window.innerWidth > 768 ? 0.15 : 0.09;

    // ✅ Establecer posición y escala inicial del logo cuadrado
    gsap.set(squareGroup, {
      scale: scaleDesktop,
      y: window.innerWidth < 768 ? "25%" : "15%",
      transformOrigin: "top center",
    });

    // ✅ Establecer posición y escala inicial del logo rectangular
    gsap.set(rectGroup, {
      scale: scaleRect,
      x: 0,
      y: 0,
      transformOrigin: "top left",
    });

    // 📍 Posición final ABSOLUTA en el viewport
    let finalX, finalY;

    if (window.innerWidth > 1024) {
      // Desktop grande
      finalX = -455;
      finalY = 10;
    } else if (window.innerWidth > 768) {
      // Tablet
      finalX = -395;
      finalY = 20;
    } else {
      // Móvil
      finalX = -332;
      finalY = -20;
    }

    // Ajuste para que sea relativo al wrapper
    const targetX = finalX - wrapperRect.left;
    const targetY = finalY - wrapperRect.top;

    // Escala final según dispositivo
    const xscale =
      window.innerWidth > 1024 ? 0.04 : window.innerWidth > 768 ? 0.05 : 0.1;

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

    // 🔄 Animación de morphing de cada parte
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

    // 🎯 Movimiento absoluto del grupo
    tl.to(
      squareGroup,
      {
        x: targetX,
        y: targetY,
        scale: xscale,
        duration: 0.5,
        transformOrigin: "top left",
        ease: "power2.out",
      },
      0
    );

    // 🔄 Fade out del keyButton
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

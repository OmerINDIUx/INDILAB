document.addEventListener("DOMContentLoaded", async () => {
  const [squareText, rectText] = await Promise.all([
    fetch("../../svg/indi-lab_Square_Animate.svg").then((r) => r.text()),
    fetch("../../svg/indi-lab_Vertical_Animate.svg").then((r) => r.text()),
  ]);

  const squareContainer = document.getElementById("svg-square");
  const rectContainer = document.getElementById("svg-rect");

  squareContainer.innerHTML = squareText;
  rectContainer.innerHTML = rectText;

  const squareSvg = squareContainer.querySelector("svg");
  const rectSvg = rectContainer.querySelector("svg");

  // IDs que queremos animar
  const ids = ["INDI", "LAB", "keyTop", "keyButton"];

  // Grupos completos
  const squareGroup = squareSvg.querySelector("#IndiGrup");
  const rectGroup = rectSvg.querySelector("#IndiGrup");

  function startAnimValues() {
    if (window.innerWidth <= 770) {
      return { ys: "5%", sscale: 0.2, transformOriginS: "top center" };
    } else if (window.innerWidth <= 1024) {
      return { ys: "5%", sscale: 0.2, transformOriginS: "top center" };
    } else if (window.innerWidth <= 1008) {
      return { ys: "5%", sscale: 0.2, transformOriginS: "top center" };
    } else {
      return { ys: "5%", sscale: 0.25, transformOriginS: "top center" };
    }
  }

  const { transformOriginS, ys, sscale } = startAnimValues();

  gsap.set(squareGroup, {
    scale: sscale,
    y: ys,
    transformOrigin: transformOriginS,
  });

  // Calcular posiciones de rectGroup con respecto al wrapper
  const wrapper = document.getElementById("svg-wrapper");
  const wrapperRect = wrapper.getBoundingClientRect();
  const targetX = wrapperRect.width * 0.015; // 1.5%
  const targetY = wrapperRect.height * 0.02; // 2%

  // Posición final del rectángulo
  gsap.set(rectGroup, {
    scale: 0.15,
    x: targetX,
    y: targetY,
    transformOrigin: "top left",
  });

  // 📌 Función para valores responsive
  function getAnimValues() {
    if (window.innerWidth <= 770) {
      return { yx: "10%", xx: "-10%", xscale: 0.12 };
    } else if (window.innerWidth <= 1024) {
      return { yx: "1%", xx: "-15%", xscale: 0.1 };

          } else if (window.innerWidth <= 1008) {
      return { yx: "1%", xx: "-15%", xscale: "40px" };
    } else {
      return { yx: "1%", xx: "-37.5%", xscale: 0.046};
    }
  }



  // Timeline con scroll
  gsap.registerPlugin(ScrollTrigger);

  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: "#svg-wrapper",
      start: "top top",
      end: "center top",
      scrub: 1,
      markers: true,
    },
  });

  // Morph de cada elemento
  ids.forEach((id) => {
    const fromEl = squareSvg.querySelector(`#${id}`);
    const toEl = rectSvg.querySelector(`#${id}`);
    if (!fromEl || !toEl) return;

    const fromBox = fromEl.getBBox();
    const toBox = toEl.getBBox();

    // Diferencias de posición
    const dx = toBox.x + toBox.width / 2 - (fromBox.x + fromBox.width / 2);
    const dy = toBox.y + toBox.height / 2 - (fromBox.y + fromBox.height / 2);

    // Escala relativa
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
  const { xx, yx, xscale } = getAnimValues();

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
        opacity: 0, // se desvanece
        duration: 0.5,
        ease: "power2.out",
      },
      0
    );
  }
});

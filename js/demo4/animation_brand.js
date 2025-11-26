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
  const squareGroup = squareSvg.querySelector("#IndiGrup");
  const rectGroup = rectSvg.querySelector("#IndiGrup");
  const ids = ["INDI", "LAB", "keyTop", "keyButton"];

  gsap.registerPlugin(ScrollTrigger);

  // ==================================================
  // 🧩 CONFIGURACIONES POR BREAKPOINT
  // ==================================================
  const endpoints = {
    largeDesktop: {
      scaleSquare: 0.18,
      scaleRect: 0.14,
      yOffset: "-28%",
      xscale: 0.04,
      finalX: -509,
      finalY: -515,
    },
    desktop: {
      scaleSquare: 0.2,
      scaleRect: 0.15,
      yOffset: "-25%",
      xscale: 0.055,
      finalX: -503,
      finalY: -510,
    },
    laptop: {
      scaleSquare: 0.25,
      scaleRect: 0.15,
      yOffset: "-20%",
      xscale: 0.075,
      finalX: -485,
      finalY: -508,
    },
    tablet: {
      scaleSquare: 0.35,
      scaleRect: 0.15,
      yOffset: "-15%",
      xscale: 0.075,
      finalX: -485,
      finalY: -505,
    },
    mobileLarge: {
      scaleSquare: 0.45,
      scaleRect: 0.1,
      yOffset: "-8%",
      xscale: 0.09,
      finalX: -475,
      finalY: -490,
    },
    mobileSmall: {
      scaleSquare: 0.55,
      scaleRect: 0.08,
      yOffset: "-4%",
      xscale: 0.13,
      finalX: -450,
      finalY: -460,
    },
  };

  // ==================================================
  // ⚙️ ESTADO INICIAL
  // ==================================================
  function setInitialState(context, squareGroup, rectGroup) {
    const activeKey = Object.keys(endpoints).find((key) => context.conditions[key]);
    const { scaleSquare, scaleRect, yOffset } = endpoints[activeKey];

    gsap.set(squareGroup, {
      scale: scaleSquare,
      y: yOffset,
      x: "0%",
      transformOrigin: "center center",
    });

    gsap.set(rectGroup, {
      scale: scaleRect,
      x: 0,
      y: 0,
      transformOrigin: "center center",
    });
  }

  // ==================================================
  // 🧠 ANIMACIÓN RESPONSIVA
  // ==================================================
  function setupAnimation(context) {
    const wrapper = document.getElementById("svg-wrapper");
    const wrapperRect = wrapper.getBoundingClientRect();

    // Reestablece el estado inicial antes de animar
    setInitialState(context, squareGroup, rectGroup);

    // Toma las configuraciones activas
    const activeKey = Object.keys(endpoints).find((key) => context.conditions[key]);
    const { xscale, finalX, finalY } = endpoints[activeKey];

    // Ajuste relativo al wrapper
    const targetX = finalX - wrapperRect.left;
    const targetY = finalY - wrapperRect.top;

    // 🎬 Timeline con ScrollTrigger
    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: "#svg-wrapper",
        start: "top top",
        end: "center top",
        scrub: 1,
        markers: false,
        onLeaveBack: () => {
          // 🔄 Retorno suave al estado inicial
          setInitialState(context, squareGroup, rectGroup);
          gsap.to(squareGroup, { duration: 0.6, ease: "power2.out" });
        },
      },
    });

    // 🔄 Morph de cada parte del logo
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

    // 🎯 Movimiento del grupo general
    tl.to(
      squareGroup,
      {
        x: targetX,
        y: targetY,
        scale: xscale,
        duration: 0.6,
        ease: "power2.out",
      },
      0
    );

    // 🔅 Fade del keyButton
    const keyButton = squareSvg.querySelector("#keyButton");
    if (keyButton) {
      tl.to(
        keyButton,
        {
          opacity: 0,
          duration: 0.4,
          ease: "power2.out",
        },
        0
      );
    }
  }

  // ==================================================
  // 📱 MATCHMEDIA RESPONSIVO
  // ==================================================
  const mm = gsap.matchMedia();

  mm.add(
    {
      largeDesktop: "(min-width: 1441px)",
      desktop: "(min-width: 1025px) and (max-width: 1440px)",
      laptop: "(min-width: 901px) and (max-width: 1024px)",
      tablet: "(min-width: 769px) and (max-width: 900px)",
      mobileLarge: "(min-width: 481px) and (max-width: 768px)",
      mobileSmall: "(max-width: 480px)",
    },
    (context) => {
      setupAnimation(context);
    }
  );

  // ==================================================
  // 🔄 REACCIÓN AL RESIZE (con debounce)
  // ==================================================
  // let resizeTimeout;
  // window.addEventListener("resize", () => {
  //   clearTimeout(resizeTimeout);
  //   resizeTimeout = setTimeout(() => {
  //     ScrollTrigger.getAll().forEach((st) => st.kill());
  //     mm.revert();
  //     mm.refresh();
  //   }, 300);
  // });
});
document.addEventListener("DOMContentLoaded", () => {
  const loaderOverlay = document.getElementById("loader-overlay");
  const loadingText = document.getElementById("text_loading");
  const complmentLoader = document.getElementById("complment_loader");
  const loadingDots = document.getElementById("loading");
  const slider = document.getElementById("LoadSlider");

  const frases = [
    "the sum of countless small decisions, interactions, and exchanges.",
    "spaces where public life thrives in the unpredictable rhythms of the street. ",
    "social products: created, contested, and transformed by those who inhabit them.",
    "living laboratories for cooperation, conflict, and the negotiation of difference.",
    "places where complexity is not a problem to solve, but a resource to embrace.",
    "both fragile and resilient, capable of absorbing change while retaining their identity.",
    "a mirror of our collective values, ambitions, and inequalities.",
  ];
  loadingText.textContent = frases[Math.floor(Math.random() * frases.length)];

  let dotCount = 0;
  const dotInterval = setInterval(() => {
    dotCount = (dotCount + 1) % 4;
    loadingDots.textContent = "." + ".".repeat(dotCount);
  }, 500);

  let percent = 0;
  const sliderInterval = setInterval(() => {
    if (slider && percent < 100) {
      percent += 1;
      slider.style.width = percent + "%";
    } else {
      clearInterval(sliderInterval);
    }
  }, 30);

  const tl = gsap.timeline();

  const safeTo = (selector, props) => {
    if (document.querySelector(selector)) {
      tl.to(selector, props);
    }
  };

  // ✅ Funciones para calcular offsets dinámicos
  const getCornerOffsets = () => ({
    topRight: {
      x: window.innerWidth * 0.5 - window.innerWidth * .15, // relativo al ancho
      y: -(window.innerHeight * 0.5 - window.innerHeight * 0.45),
    },
    bottomLeft: {
      x: -(window.innerWidth * 0.5 - window.innerWidth * 0.15),
      y: window.innerHeight * 0.5 - window.innerHeight * 0.45,
    },
  });

  function runCornerAnimation() {
    const offsets = getCornerOffsets();

    const cornerTimeline = gsap.timeline();
    cornerTimeline
      .to(".top-right1", {
        x: offsets.topRight.x,
        y: offsets.topRight.y,
        scale: 0.8,
        opacity: 1,
        duration: 1,
        ease: "power2.out",
      })
      .to(
        ".bottom-left1",
        {
          x: offsets.bottomLeft.x,
          y: offsets.bottomLeft.y,
          scale: 0.8,
          opacity: 1,
          duration: 1,
          ease: "power2.out",
        },
        "<"
      )
      .from("#complment_loader", { opacity: 0, y: 20, duration: 0.05 })
      .from("#text_loading", { opacity: 0, y: 20, duration: 1 });

    return cornerTimeline;
  }

  tl.add(runCornerAnimation());

  // 🔄 Recalcular posiciones si la ventana se redimensiona
  window.addEventListener("resize", () => {
    gsap.killTweensOf(".top-right1, .bottom-left1");
    runCornerAnimation();
  });

  // Animación de salida
  const animateExit = () => {
    const exitTimeline = gsap.timeline({
      onComplete: () => {
        loaderOverlay.style.display = "none";
        document.body.classList.remove("loading");
      },
    });

    exitTimeline
      .to(["#text_loading", "#loading", "#complment_loader"], {
        opacity: 0,
        y: -20,
        duration: 0.6,
        ease: "power2.in",
        stagger: 0.1,
      })
      .to(
        ".top-right1",
        {
          x: window.innerWidth, // salir fuera de pantalla
          y: -window.innerHeight,
          scale: 1.2,
          opacity: 0,
          duration: 0.8,
          ease: "power2.in",
        },
        0
      )
      .to(
        ".bottom-left1",
        {
          x: -window.innerWidth,
          y: window.innerHeight,
          scale: 1.2,
          opacity: 0,
          duration: 0.8,
          ease: "power2.in",
        },
        0
      );
  };

  const loadFont = () => {
    const observer = new FontFaceObserver("Work Sans", { weight: 600 });
    return observer.load(null, 5000);
  };

  Promise.allSettled([
    loadFont().catch(() =>
      console.warn("⚠️ No se cargó power_grotesk a tiempo")
    ),
    new Promise((resolve) => tl.eventCallback("onComplete", resolve)),
  ]).then(() => {
    clearInterval(dotInterval);
    animateExit();
  });
});

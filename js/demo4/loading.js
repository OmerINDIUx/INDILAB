document.addEventListener("DOMContentLoaded", () => {
  const loaderOverlay = document.getElementById("loader-overlay");
  const loadingText = document.getElementById("text_loading");
  const complmentLoader = document.getElementById("complment_loader");

  const loadingDots = document.getElementById("loading");
  const slider = document.getElementById("LoadSlider");

  // Frases aleatorias
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

  // Animación de puntos "Cargando..."
  let dotCount = 0;
  const dotInterval = setInterval(() => {
    dotCount = (dotCount + 1) % 4;
    loadingDots.textContent = "Cargando" + ".".repeat(dotCount);
  }, 500);

  // Simulación de barra de carga
  let percent = 0;
  const sliderInterval = setInterval(() => {
    if (slider && percent < 100) {
      percent += 1;
      slider.style.width = percent + "%";
    } else {
      clearInterval(sliderInterval);
    }
  }, 30);

  // GSAP timelines
  const tl = gsap.timeline();

  const safeTo = (selector, props) => {
    if (document.querySelector(selector)) {
      tl.to(selector, props);
    }
  };

  // Animaciones opcionales de elementos si existen
  safeTo("#elemento1", { opacity: 1, duration: 1 });
  safeTo("#elemento2", { x: 100, duration: 1 });
  safeTo("#elemento3", { scale: 1.2, duration: 1 });

  // Animación de íconos de esquina y texto
  const cornerTimeline = gsap.timeline();

  cornerTimeline
    .to(".top-right1", {
      x: window.innerWidth / 2 - 450, // parte desde el centro en X
      y: -(window.innerHeight / 2 - 380), // parte desde el centro en Y invertido
      scale: 0.8,
      opacity: 1,
      duration: 1,
      ease: "power2.out",
    })
    .to(
      ".bottom-left1",
      {
        x: -(window.innerHeight / 2 - 50), // parte desde el centro en X
        y: window.innerHeight / 2 - 380, // parte desde el centro en Y invertido
        scale: 0.8,
        opacity: 1,
        duration: 1,
        ease: "power2.out",
      },
      "<"
    )

    .from("#complment_loader", {
      opacity: 0,
      y: 20,
      duration: 0.05,
      ease: "power2.out",
    })

    .from("#text_loading", {
      opacity: 0,
      y: 20,
      duration: 1,
      ease: "power2.out",
    });

  tl.add(cornerTimeline);

  // Esperar a que termine animación y fuentes
  const loadFont = () => {
    const observer = new FontFaceObserver("power_grotesk", {
      weight: 400,
      style: "normal",
    });
    return observer.load(null, 5000);
  };

  Promise.allSettled([
    loadFont().catch(() =>
      console.warn("⚠️ No se cargó power_grotesk a tiempo")
    ),
    new Promise((resolve) => tl.eventCallback("onComplete", resolve)),
  ]).then(() => {
    clearInterval(dotInterval);
    gsap.to(loaderOverlay, {
      opacity: 0,
      duration: 0.5,
      onComplete: () => {
        loaderOverlay.style.display = "none";
        document.body.classList.remove("loading");
      },
    });
  });

  Promise.allSettled([
    loadFont().catch(() =>
      console.warn("⚠️ No se cargó power_grotesk a tiempo")
    ),
    new Promise((resolve) => tl.eventCallback("onComplete", resolve)),
  ]).then(() => {
    clearInterval(dotInterval);

    // ✨ Animación de salida
    const exitTimeline = gsap.timeline({
      onComplete: () => {
        gsap.to(loaderOverlay, {
          opacity: 0,
          duration: 0.5,
          onComplete: () => {
            loaderOverlay.style.display = "none";
            document.body.classList.remove("loading");
          },
        });
      },
    });

    // Texto se desvanece
    exitTimeline.to(
      "#text_loading",
      {
        opacity: 0,
        y: -20,
        duration: 0.6,
        ease: "power2.in",
      },
      0
    );

    exitTimeline.to(
      "#loading",
      {
        opacity: 0,
        y: -20,
        duration: 0.6,
        ease: "power2.in",
      },
      0
    );
    exitTimeline.to(
      "#complment_loader",
      {
        opacity: 0,
        y: -20,
        duration: 0.6,
        ease: "power2.in",
      },
      0
    );

    // Íconos se expanden hacia afuera y se desvanecen
    exitTimeline.to(
      ".top-right1",
      {
        x: window.innerWidth, // sale más a la derecha
        y: -window.innerHeight, // sube más
        scale: 1.2,
        opacity: 0,
        duration: 0.8,
        ease: "power2.in",
      },
      0
    );

    exitTimeline.to(
      ".bottom-left1",
      {
        x: -window.innerWidth, // se va más a la izquierda
        y: window.innerHeight, // baja más
        scale: 1.2,
        opacity: 0,
        duration: 0.8,
        ease: "power2.in",
      },
      0
    );
  });
});

/**
 * Page-specific JavaScript for Who We Are page
 * Handles GSAP animations and color randomization.
 */

document.addEventListener("DOMContentLoaded", () => {
  initCornerAnimation();
  initListAnimation();
  initHeroAnimation();
  initColorRandomization();
});

function initCornerAnimation() {
  gsap.registerPlugin(ScrollTrigger);

  const bottomLeft = document.querySelector(".corner-bottom-left");
  const topRight = document.querySelector(".corner-top-right");
  const content = document.querySelector(".content-text");

  if (!bottomLeft || !topRight || !content) return;

  let tl;

  function getPositions() {
    const w = content.offsetWidth;
    const h = content.offsetHeight;

    // ⏳ breakpoint: móvil 500px
    const isMobile = window.innerWidth <= 500;

    if (isMobile) {
      return {
        bottomLeft: { x: w / 10 - 10, y: h / 10 - 10 },
        topRight: { x: w - 20, y: -h - 3 },
        center: { x: w / 2 - 20, y: h / 2 - 20 },
      };
    }

    // ⏹ posiciones normales
    return {
      bottomLeft: { x: -50, y: -h + 95 },
      topRight: { x: +w, y: -h - 65 },
      center: { x: w / 2 - 25, y: h / 2 - 25 },
    };
  }

  function animateCorners() {
    const pos = getPositions();

    gsap.set([bottomLeft, topRight], {
      position: "absolute",
      x: pos.center.x,
      y: pos.center.y,
      opacity: 0,
    });

    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: ".statement",
        start: "top center",
        end: "bottom bottom",
        scrub: true,
      },
    });

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

  // Fade-in del texto
  gsap.fromTo(
    ".content-text",
    { opacity: 0.01 },
    {
      opacity: 1,
      scrollTrigger: {
        trigger: ".statement",
        start: "top center",
        end: "bottom bottom",
        scrub: true,
      },
    }
  );

  // iniciar después del delay
  setTimeout(() => {
    tl = animateCorners();
  }, 2000);

  // recalcular al hacer resize
  window.addEventListener("resize", () => {
    if (tl) {
      tl.kill();
      tl = animateCorners();
    }
  });
}

function initListAnimation() {
  gsap.registerPlugin(ScrollTrigger);

  // Selecciona las frases dentro del contenedor .list
  const phrases = gsap.utils.toArray(".list .phrase");

  if (phrases.length === 0) return;

  // Configuración inicial
  gsap.set(phrases, { opacity: 0, y: 60, filter: "blur(6px)" });

  // La primera frase debe ser visible al inicio
  if (phrases[0])
    gsap.set(phrases[0], { opacity: 1, y: 0, filter: "blur(0px)" });

  // Duraciones de animación
  const D_IN = 0.6, // entrada
    D_HOLD = 0.3, // espera antes de que salga
    D_OUT = 0.6; // salida

  // Timeline principal controlado por el scroll
  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: ".list",
      start: "top top",
      end: "+=" + phrases.length * window.innerHeight, // espacio por frase
      scrub: 0.85,
      pin: true,
      anticipatePin: 1,
      snap: 1 / (phrases.length - 1), // snap perfecto por frase
    },
  });

  // Construcción de la secuencia
  phrases.forEach((phrase, i) => {
    if (i === 0) {
      // La primera ya está visible, solo hacemos hold
      tl.to({}, { duration: D_HOLD });
    } else {
      // Animación de entrada (fade + blur + slide)
      tl.fromTo(
        phrase,
        { opacity: 0, y: 60, filter: "blur(6px)" },
        {
          opacity: 1,
          y: 0,
          filter: "blur(0px)",
          duration: D_IN,
          ease: "power3.out",
          immediateRender: false, // ✅ FIX para permitir reverse
        }
      );
    }

    // Animación de salida (fade + slide up + blur)
    if (i < phrases.length - 1) {
      tl.to(
        phrase,
        {
          opacity: 0,
          y: -60,
          filter: "blur(6px)",
          duration: D_OUT,
          ease: "power3.in",
          immediateRender: false, // ✅ NECESARIO para scroll up
        },
        "+=0.25" // pequeño hold para ritmo
      );
    }
  });

  // Refrescar el trigger cuando cambie el viewport
  addEventListener("resize", () => ScrollTrigger.refresh());
  addEventListener("beforeunload", () => {
    ScrollTrigger.getAll().forEach((t) => t.kill());
    tl.kill();
  });
}

function initHeroAnimation() {
  gsap.registerPlugin(ScrollTrigger);

  // Animación inicial hero
  gsap.from(".hero-text h2", {
    opacity: 0,
    y: 50,
    duration: 1.5,
    ease: "power3.out",
  });

  gsap.from(".hero img", {
    scale: 1.2,
    duration: 3,
    ease: "power2.out",
  });

  // Animación especial del logo
  gsap.from(".intro img", {
    scrollTrigger: {
      trigger: ".intro img",
      start: "top 80%",
    },
    opacity: 0, // aparece suavemente
    y: 30, // sube un poco desde abajo
    scale: 0.95, // pequeño escalado para profundidad
    duration: 1.5, // tiempo de animación
    ease: "power2.out", // movimiento elegante y natural
  });
}

function initColorRandomization() {
  // 🎨 Lista de colores posibles
  const colors = ["#18B2E8", "#F86230", "#A51C5B", "#9244D6"];
  let lastColor = null; // guardamos el último color usado

  // Seleccionamos todos los <span> y los <h2 class="white">
  const elements = document.querySelectorAll(".pdp, h2.white");

  elements.forEach((el) => {
    let randomColor;
    // Evita repetir el mismo color que el anterior
    do {
      randomColor = colors[Math.floor(Math.random() * colors.length)];
    } while (randomColor === lastColor);

    el.style.color = randomColor;
    lastColor = randomColor;
  });
}

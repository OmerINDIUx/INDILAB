import { HighlightEffect as HighlightEffect3 } from "./effect-3/highlightEffect.js";

(() => {
  const { gsap } = window;
  const ScrollTrigger = window.ScrollTrigger;
  const Flip = window.Flip;
  const Lenis = window.Lenis;
  const Splitting = window.Splitting;
  const FontFaceObserver = window.FontFaceObserver;

  gsap.registerPlugin(ScrollTrigger, Flip);

  const preloadImages = (selector = "img") => {
    return new Promise((resolve) => {
      const elements = document.querySelectorAll(selector);
      const validSrcElements = Array.from(elements).filter((el) => {
        const src = el.getAttribute("src");
        return src && !src.includes("undefined");
      });

      if (validSrcElements.length === 0) {
        resolve();
        return;
      }

      let loaded = 0;

      validSrcElements.forEach((el) => {
        const img = new Image();
        img.onload = () => {
          loaded++;
          if (loaded === validSrcElements.length) resolve();
        };
        img.onerror = () => {
          console.warn("Error cargando imagen:", el.src);
          loaded++;
          if (loaded === validSrcElements.length) resolve();
        };
        img.src = el.src;
      });
    });
  };

  const preloadPowerGrotesk = () => {
    const font = new FontFaceObserver("power_grotesk", {
      weight: 400,
      style: "normal",
    });
    return font
      .load(null, 5000)
      .then(() => document.documentElement.classList.add("fonts-loaded"))
      .catch(() => console.warn("⚠️ No se cargó power_grotesk a tiempo"));
  };

  const initSmooth = () => {
    const lenis = new Lenis({ lerp: 0.2, smoothWheel: true });
    lenis.on("scroll", () => ScrollTrigger.update());
    const raf = (t) => {
      lenis.raf(t);
      requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);
  };

  const animationEffects = [
    {
      name: "Zoom+Fade",
      effect: (el) =>
        gsap
          .timeline({
            scrollTrigger: {
              trigger: el,
              start: "top top",
              end: "+=100%",
              scrub: true,
            },
          })
          .to(el, { scale: 2, opacity: 0, ease: "power2.inOut" }, 0),
    },
    {
      name: "Slide Left",
      effect: (el) =>
        gsap
          .timeline({
            scrollTrigger: {
              trigger: el,
              start: "top top",
              end: "+=100%",
              scrub: true,
            },
          })
          .to(el, { x: "-100vw", opacity: 0, ease: "expo.inOut" }, 0),
    },
    {
      name: "Zoom In + Blur",
      effect: (el) =>
        gsap
          .timeline({
            scrollTrigger: {
              trigger: el,
              start: "top top",
              end: "+=100%",
              scrub: true,
            },
          })
          .to(el, {
            opacity: 0,
            scale: 1.5,
            filter: "blur(5px)",
            ease: "power3.out",
          }, 0),
    },
    {
      name: "Fade + Scale Down",
      effect: (el) =>
        gsap
          .timeline({
            scrollTrigger: {
              trigger: el,
              start: "top top",
              end: "+=100%",
              scrub: true,
            },
          })
          .to(el, { opacity: 0, scale: 0.2, ease: "power1.out" }, 0),
    },
    {
      name: "Wobble + Fade",
      effect: (el) =>
        gsap
          .timeline({
            scrollTrigger: {
              trigger: el,
              start: "top top",
              end: "+=100%",
              scrub: true,
            },
          })
          .to(el, {
            x: () => Math.random() * 30 - 15,
            rotation: () => Math.random() * 10 - 5,
            opacity: 0,
            ease: "none",
          }, 0),
    },
    



{
  name: "Rotate + Zoom + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      rotation: 360,
      scale: 2,
      opacity: 0, // fade
      ease: "power2.inOut",
    }, 0),
},
{
  name: "Slide Up + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      y: "-100vh",
      opacity: 0, // fade
      ease: "sine.out",
    }, 0),
},

{
  name: "Twist + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      rotation: 45,
      scale: 0.6,
      opacity: 0, // fade
      ease: "power1.inOut",
    }, 0),
},
{
  name: "Slide Right + Blur + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      x: "100vw",
      filter: "blur(8px)",
      opacity: 0, // fade
      ease: "expo.inOut",
    }, 0),
},
{
  name: "Elastic Pulse + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      scale: 1.4,
      opacity: 0, // fade
      ease: "elastic.in(1, 0.3)",
    }, 0),
},
{
  name: "Zoom Rotate + Blur + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      scale: 3,
      rotation: 90,
      filter: "blur(6px)",
      opacity: 0, // fade
      ease: "power4.in",
    }, 0),
},
{
  name: "Horizontal Shrink + Fade",
  effect: (el) =>
    gsap.timeline({
      scrollTrigger: {
        trigger: el,
        start: "top top",
        end: "+=100%",
        scrub: true,
      },
    }).to(el, {
      scaleX: 0,
      opacity: 0, // fade
      ease: "expo.in",
    }, 0),
}




  ];

  const initScrollAnimations = () => {
  const elements = [...document.querySelectorAll(".content--sticky")];
  const total = elements.length;

  // Barajar efectos de forma aleatoria
  const shuffledEffects = animationEffects
    .slice()
    .sort(() => Math.random() - 0.5);

  elements.forEach((el, i) => {
    // Si hay más elementos que efectos, se vuelve a barajar y continúa
    const effectObj =
      shuffledEffects[i % shuffledEffects.length]; // asigna sin repetir hasta agotar efectos
    console.log(`🌀 Elemento ${i + 1}: efecto aplicado -> ${effectObj.name}`);
    effectObj.effect(el, i, total);
  });
};

  const initHighlight = () => {
    document.querySelectorAll(".hx").forEach((el) => {
      if (!el.classList.contains("hx-11")) el.dataset.splitting = "";
    });
    Splitting();
    document.querySelectorAll(".hx-3").forEach((el) => {
      new HighlightEffect3(el);
    });
  };

  // Inicialización principal
  (async () => {
    try {
      await Promise.all([
        preloadImages(".content__img img, .content__img image"), // asegúrate que solo busca imágenes reales
        preloadPowerGrotesk(),
      ]);
      document.body.classList.remove("loading");
      initSmooth();
      initHighlight();
      initScrollAnimations();
    } catch (err) {
      console.error("Error al inicializar:", err);
    }
  })();
})();

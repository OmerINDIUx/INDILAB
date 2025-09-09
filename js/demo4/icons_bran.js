window.addEventListener("DOMContentLoaded", () => {
    gsap.registerPlugin(ScrollTrigger);

    const bottomLeft = document.querySelector(".corner-bottom-left");
    const topRight   = document.querySelector(".corner-top-right");

    // Posición inicial (en el centro)
    gsap.set([bottomLeft, topRight], {
      xPercent: -50,
      yPercent: -50,
      left: "50%",
      top: "50%",
      position: "absolute",
      opacity: 0,
      scale: 0.25
    });

    // Timeline para animar ambos corners
    const tl = gsap.timeline({
      scrollTrigger: {
        trigger: "#map",   // 🔥 Se dispara al llegar a la sección #map
        start: "top center", // empieza cuando la parte superior de #map llega al centro de la pantalla
        toggleActions: "play none none reverse"
      }
    });

    tl.to(bottomLeft, {
      duration: 1.5,
      left: "0%",
      top: "60%",
      xPercent: 0,
      yPercent: -50,
      opacity: 1,
      ease: "power3.out"
    })
    .to(topRight, {
      duration: 1.5,
      left: "90%",
      top: "30%",
      xPercent: -50,
      yPercent: 0,
      opacity: 1,
      ease: "power3.out"
    }, "<"); // "<" = que empiece al mismo tiempo que la anterior
  });
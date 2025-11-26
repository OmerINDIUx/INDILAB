document.addEventListener("DOMContentLoaded", () => {
  if (typeof gsap === "undefined") {
    console.error("❌ GSAP no está cargado.");
    return;
  }

  try {
    gsap.registerPlugin(ScrollTrigger, TextPlugin);
  } catch (e) {
    console.error("❌ No se pudieron registrar los plugins:", e);
    return;
  }

  const texts = gsap.utils.toArray(".reveal-text");

  if (!texts.length) {
    console.warn("⚠️ No se encontraron elementos con la clase .reveal-text");
    return;
  }

  texts.forEach((textEl, i) => {
    let fullText = textEl.innerText.trim();
    if (!fullText) return;
    textEl.innerText = "";

    let tl = gsap.timeline({
      scrollTrigger: {
        trigger: `.tittle:nth-of-type(${i + 1})`,
        start: "top center",
        end: "bottom center",
        scrub: true,
        markers: false,
      }
    });

    tl.fromTo(textEl, { opacity: 0, y: 20 }, { opacity: 1, y: 0 })
      .to(textEl, { text: fullText, ease: "none" })
      .to({}, { duration: 0.5 }) // 👈 Pausa de medio segundo
      .to(textEl, { opacity: 0 });
  });
});

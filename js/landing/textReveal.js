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
        trigger: textEl.closest("section") || textEl,
        start: "top 80%",
        end: "bottom 20%",
        scrub: true,   // 👈 ahora todo el timeline se liga al scroll
        markers: false,
      }
    });

    // 1. Fade in
    tl.fromTo(textEl, { opacity: 0, y: 20 }, { opacity: 1, y: 0 });

    // 2. Escritura progresiva
    tl.to(textEl, { text: fullText, ease: "none" });

    // 3. Fade out
    tl.to(textEl, { opacity: 0 });
  });
});

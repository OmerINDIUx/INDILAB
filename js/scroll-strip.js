// Encapsulate to avoid global pollution
(function () {
  function init() {
    // Ensure GSAP and ScrollTrigger are registered
    if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
      gsap.registerPlugin(ScrollTrigger);
    } else {
      console.warn("GSAP or ScrollTrigger not found for scroll-strip.js");
      return;
    }

    const phraseSection = document.querySelector("#phrase-section");

    function initScrollStrip(sectionId) {
      const runSection = document.querySelector(sectionId);
      if (!runSection) return;

      const rail = runSection.querySelector(".blog-scroll-strip__rail");
      // Scope to rail to ensure we only get cards in this section
      const cards = gsap.utils.toArray(".blog-scroll-card", rail);

      if (!rail || !cards.length) return;

      function distributeRandomPositions() {
        const railRect = rail.getBoundingClientRect();
        const railWidth = railRect.width || rail.offsetWidth;
        const railHeight = railRect.height || rail.offsetHeight;

        cards.forEach((card) => {
          // Get actual card width, fallback to 360 if not rendered yet
          const cardWidth = card.offsetWidth || 360;
          const x = gsap.utils.random(0, Math.max(railWidth - cardWidth, 0));
          const y = gsap.utils.random(0, railHeight * 0.7);
          gsap.set(card, { x, y });
        });

        // Refresh ScrollTrigger after positioning to ensure start/end values are correct
        ScrollTrigger.refresh();
      }

      // 1) Position randomly immediately
      distributeRandomPositions();

      // Position again on load to account for images loading
      if (document.readyState === "complete") {
        distributeRandomPositions();
      } else {
        window.addEventListener("load", distributeRandomPositions);
      }

      window.addEventListener("resize", () => {
        // Debounce if needed, but direct call is usually fine for this
        distributeRandomPositions();
      });

      // 2) Animate each card
      cards.forEach((card) => {
        const fromY = gsap.utils.random(10, 40);
        const travel = gsap.utils.random(60, 140);
        const toY = fromY - travel;

        gsap.fromTo(
          card,
          { yPercent: fromY },
          {
            yPercent: toY,
            ease: "none",
            scrollTrigger: {
              trigger: runSection,
              start: "top bottom",
              end: "bottom top",
              scrub: true,
            },
          }
        );
      });
    }

    // Initialize both strips
    initScrollStrip("#run1");
    initScrollStrip("#run2");

    // 3) Pin text
    const pinTrigger = document.querySelector("#run1");
    const endTrigger = document.querySelector("#run2");

    // Ensure elements exist. Lowered breakpoint to 320px for mobile support
    if (window.innerWidth > 320 && pinTrigger && phraseSection) {
      let scrollTriggerConfig = {
        trigger: phraseSection,
        start: "top top",
        pin: true,
        pinSpacing: false,
      };

      // If we have a second strip (#run2), pin until it hits the top.
      if (endTrigger) {
        scrollTriggerConfig.endTrigger = endTrigger;
        scrollTriggerConfig.end = "top top";
      }
      // If we ONLY have one strip (#run1), pin until the BOTTOM of that strip passes the visual window
      // (meaning the user has scrolled past all the images)
      else {
        scrollTriggerConfig.endTrigger = pinTrigger; // The strip itself
        scrollTriggerConfig.end = "bottom top";
      }

      ScrollTrigger.create(scrollTriggerConfig);

      // Set z-index lower than the scroll strip (12) but ensuring visibility
      gsap.set(phraseSection, { zIndex: 9 });
    }

    // Final refresh to ensure all positions are correct
    ScrollTrigger.refresh();
  }

  // Run Init
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

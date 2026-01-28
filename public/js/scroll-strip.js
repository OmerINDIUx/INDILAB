// Encapsulate to avoid global pollution
(function () {
    function init() {
        // Ensure GSAP and ScrollTrigger are registered
        if (
            typeof gsap !== "undefined" &&
            typeof ScrollTrigger !== "undefined"
        ) {
            gsap.registerPlugin(ScrollTrigger);
        } else {
            console.warn("GSAP or ScrollTrigger not found for scroll-strip.js");
            return;
        }

        function initScrollStrip(runSection) {
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
                    const x = gsap.utils.random(
                        0,
                        Math.max(railWidth - cardWidth, 0),
                    );
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
                    },
                );
            });
        }

        // 1. Initialize all Scroll Strips (Rails)
        document.querySelectorAll(".blog-scroll-strip").forEach((section) => {
            initScrollStrip(section);
        });

        // 2. Initialize Pinning Logic for Phrase Sections
        // Finds the previous and next scroll strips relative to the phrase section.
        document
            .querySelectorAll(".blog-scroll-strip-phrase-section")
            .forEach((phraseSection) => {
                // Find all adjacent strips (before or after) to determine pinning range
                let prevStrip = phraseSection.previousElementSibling;
                while (
                    prevStrip &&
                    !prevStrip.classList.contains("blog-scroll-strip")
                ) {
                    prevStrip = prevStrip.previousElementSibling;
                }

                let nextStrip = phraseSection.nextElementSibling;
                let lastStrip = null;
                let tempNext = nextStrip;
                while (tempNext) {
                    if (tempNext.classList.contains("blog-scroll-strip")) {
                        lastStrip = tempNext;
                    } else if (
                        !tempNext.classList.contains(
                            "blog-scroll-strip-phrase-section",
                        )
                    ) {
                        break;
                    }
                    tempNext = tempNext.nextElementSibling;
                }

                if (window.innerWidth > 320) {
                    let scrollTriggerConfig = {
                        trigger: phraseSection,
                        start: "top top",
                        pin: true,
                        pinSpacing: false,
                    };

                    // CASE 1: Phrase is BEFORE Rails (Reference order)
                    if (lastStrip) {
                        scrollTriggerConfig.endTrigger = lastStrip;
                        scrollTriggerConfig.end = "bottom top";
                    }
                    // CASE 2: Phrase is AFTER Rails
                    else if (prevStrip) {
                        scrollTriggerConfig.endTrigger = prevStrip;
                        scrollTriggerConfig.end = "bottom top";
                    } else {
                        scrollTriggerConfig.end = "+=100%";
                    }

                    ScrollTrigger.create(scrollTriggerConfig);

                    // Set z-index lower than the scroll strip (12) but ensuring visibility
                    gsap.set(phraseSection, { zIndex: 9 });
                }
            });

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

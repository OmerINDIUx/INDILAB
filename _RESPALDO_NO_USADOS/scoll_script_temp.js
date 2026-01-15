      <script>
        gsap.registerPlugin(ScrollTrigger);

        const phraseSection = document.querySelector("#phrase-section");

        function initScrollStrip(sectionId) {
          const runSection = document.querySelector(sectionId);
          if (!runSection) return;

          const rail = runSection.querySelector(".blog-scroll-strip__rail");
          const cards = gsap.utils.toArray(".blog-scroll-card", rail);

          if (!rail || !cards.length) return;

          function distributeRandomPositions() {
            const railRect = rail.getBoundingClientRect();
            const railWidth = railRect.width || rail.offsetWidth;
            const railHeight = railRect.height || rail.offsetHeight;

            cards.forEach((card) => {
              const cardWidth = card.offsetWidth || 360;
              const x = gsap.utils.random(
                0,
                Math.max(railWidth - cardWidth, 0)
              );
              const y = gsap.utils.random(0, railHeight * 0.7);
              gsap.set(card, { x, y });
            });

            ScrollTrigger.refresh();
          }

          distributeRandomPositions();
          window.addEventListener("resize", distributeRandomPositions);

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

        window.addEventListener("load", () => {
          initScrollStrip("#run1");
          initScrollStrip("#run2");

          const pinTrigger = document.querySelector("#run1");
          const endTrigger = document.querySelector("#run2");

          if (
            window.innerWidth > 320 &&
            pinTrigger &&
            phraseSection &&
            endTrigger
          ) {
            ScrollTrigger.create({
              trigger: pinTrigger,
              start: "top bottom",
              endTrigger: endTrigger,
              end: "bottom top",
              pin: phraseSection,
              pinSpacing: false,
            });

            gsap.set(phraseSection, { zIndex: 15, position: "relative" });
          }

          ScrollTrigger.refresh();
        });
      </script>

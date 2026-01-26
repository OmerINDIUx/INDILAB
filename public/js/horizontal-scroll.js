document.addEventListener("DOMContentLoaded", () => {
    // Initialize Lenis for smooth scroll if available
    const initSmooth = () => {
        if (typeof Lenis !== "undefined") {
            const lenis = new Lenis({
                lerp: 0.1,
                smoothWheel: true,
            });

            lenis.on("scroll", ScrollTrigger.update);

            gsap.ticker.add((time) => {
                lenis.raf(time * 1000);
            });

            gsap.ticker.lagSmoothing(0);
            console.log("✅ Lenis initialized for Project");
        }
    };

    // Register GSAP Plugins
    if (typeof gsap !== "undefined") {
        gsap.registerPlugin(ScrollTrigger);
    }

    initSmooth();

    // Horizontal Scroll Logic
    const section = document.querySelector(".horizontal-scroll-section");
    const wrapper = document.querySelector(".carousel-wrapper");

    if (section && wrapper) {
        function getScrollAmount() {
            let wrapperWidth = wrapper.scrollWidth;
            return -(wrapperWidth - window.innerWidth);
        }

        gsap.fromTo(
            wrapper,
            { x: 0 },
            {
                x: getScrollAmount,
                ease: "none",
                scrollTrigger: {
                    trigger: section,
                    start: "top top",
                    end: () => `+=${Math.abs(getScrollAmount())}`,
                    pin: true,
                    scrub: 1,
                    invalidateOnRefresh: true,
                },
            },
        );

        // Refresh on window load to ensure images are loaded
        window.addEventListener("load", () => {
            ScrollTrigger.refresh();
        });
    }
});

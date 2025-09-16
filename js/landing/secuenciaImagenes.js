 gsap.registerPlugin(ScrollTrigger);

    const canvas = document.getElementById("image-sequence");
    const context = canvas.getContext("2d");

    const frameStart = 10000;
    const frameEnd = 12595;
    const frameCount = frameEnd - frameStart + 1;
    const currentFrame = index => `img/sequence_ours/Sequence 01_${frameStart + index}.jpg`;

    const images = [];
    const imageSeq = { frame: 0 };

    for (let i = 0; i < frameCount; i++) {
        const img = new Image();
        img.src = currentFrame(i);
        images.push(img);
    }

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        render();
    }
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    function render() {
        const img = images[imageSeq.frame];
        if (img && img.complete) {
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(img, 0, 0, canvas.width, canvas.height);
        }
    }

    // Pin de la secuencia
    ScrollTrigger.create({
        trigger: ".video-scroll-wrapper",
        start: "top top",
        endTrigger: ".text-image-section",
        end: "top top",
        pin: ".video-fullscreen",
        pinSpacing: false, // evita espacio extra
        scrub: false,
        markers: false

    });

    // Animación de frames ligada al scroll
    gsap.to(imageSeq, {
        frame: frameCount - 1,
        snap: "frame",
        ease: "none",
        scrollTrigger: {
            trigger: ".video-scroll-wrapper",
            start: "top top",
            endTrigger: ".TextLarge",
            end: "top top",
            scrub: 1
        },
        onUpdate: render
    });

    // Fade out cuando llega el texto
    gsap.to(".video-fullscreen", {
        opacity: 0,
        ease: "power2.out",
        scrollTrigger: {
            trigger: ".TextLarge",
            start: "top bottom",
            end: "top center",
            scrub: true
        }
    });

    images[0].onload = render;
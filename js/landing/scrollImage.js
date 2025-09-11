gsap.registerPlugin(ScrollTrigger, TextPlugin);

    // Array con todos los contenidos
    const sections = [
        {
            title: "Contexto:",
            text: "México tiene un déficit de vivienda, con alrededor de 8.5 millones de viviendas...",
            img: "img/landing/computer vision.png"
        },
        {
            title: "Contextsso:",
            text: "Malgo del parking.",
            img: "img/landing/parking twin.png"
        },
        {
            title: "Priotitary:",
            text: "Desing del parking.",
            img: "img/landing/paticipatory design.jpg"
        }
    ];

    // Referencias a los elementos
    const titleEl = document.querySelector(".text-column .title");
    const textEl = document.querySelector(".text-column .text-content");
    const imgEl = document.querySelector(".image-column img");

    // ScrollTrigger principal
    gsap.to({}, {
        scrollTrigger: {
            trigger: ".text-image-section",
            start: "top top",
            end: "+=" + (sections.length * window.innerHeight), // duración total
            scrub: 1,
            pin: true,
            onUpdate: self => {
                const progress = self.progress; // 0 a 1
                const index = Math.floor(progress * sections.length);
                const section = sections[Math.min(index, sections.length - 1)];

                // Cambiar título, texto e imagen dinámicamente
                titleEl.innerText = section.title;
                textEl.innerText = section.text;
                imgEl.src = section.img;
            }
        }
    });
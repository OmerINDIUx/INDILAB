/*************************************************************
 * main.js - Script Unificado
 * Contiene todas las funciones y animaciones del sitio
 *************************************************************/

/* ===========================
   1. Animación de Branding (SVG)
   =========================== */
 
// Este archivo ya maneja la animación de los SVG (cuadrado → rectángulo)
// Si quieres, también se puede copiar su contenido aquí en vez de importarlo.

/* ===========================
   2. Mostrar/Ocultar navegación según scroll
   =========================== */
window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    const navArrows = document.querySelector(".navigation-arrows");
    if (navArrows) {
        navArrows.classList.toggle("hidden", scrollY > 500);
    }
});

/* ===========================
   3. Mostrar/Ocultar animación de scroll
   =========================== */
window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    const scrollAnim = document.querySelector(".scroll-animation");
    if (scrollAnim) {
        scrollAnim.classList.toggle("hidden", scrollY > 1050);
    }
});

/* ===========================
   4. Animaciones GSAP de Sección "Brand"
   =========================== */
if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
    gsap.registerPlugin(ScrollTrigger);

    // Animación de contenido izquierdo
    gsap.fromTo("#content-row",
        { opacity: 0, y: 50 },
        {
            opacity: 1,
            y: 0,
            duration: 2,
            ease: "power2.out",
            scrollTrigger: {
                trigger: "#left-column",
                start: "top 25%",
                end: "top 10%",
                toggleActions: "restart none none reset",
            },
        }
    );

    // Animación del mapa / video controller
    gsap.fromTo("#maping-contorler",
        { opacity: 0, y: 50 },
        {
            opacity: 1,
            y: 0,
            duration: 2,
            ease: "power2.out",
            scrollTrigger: {
                trigger: "#left-column",
                start: "top 25%",
                end: "top 10%",
                toggleActions: "restart none none reset",
            },
        }
    );
}



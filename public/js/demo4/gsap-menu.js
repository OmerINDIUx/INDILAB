document.addEventListener("DOMContentLoaded", () => {
  const openBtn = document.querySelector(".hamburger");
  const closeBtn = document.getElementById("close-menu");
  const menu = document.getElementById("fullscreen-menu");
  const menuItems = menu.querySelectorAll(".menu__item, .lang-btn, .social-icons");

  const showMenu = () => {
    // Mostrar el contenedor del menú (activar pointer-events)
    menu.classList.add("show");

    // Animación GSAP
    gsap.fromTo(
      menu,
      { opacity: 0 },
      {
        opacity: 1,
        duration: 0.5,
        ease: "power2.out",
        onStart: () => {
          menu.style.pointerEvents = "all";
        }
      }
    );

    gsap.fromTo(
      menuItems,
      { y: 30, opacity: 0 },
      {
        y: 0,
        opacity: 1,
        duration: 0.6,
        stagger: 0.1,
        ease: "power3.out"
      }
    );
  };

  const hideMenu = () => {
    // Ocultar con animación
    gsap.to(menuItems, {
      y: 30,
      opacity: 0,
      duration: 0.3,
      stagger: 0.05,
      ease: "power2.in"
    });

    gsap.to(menu, {
      opacity: 0,
      duration: 0.4,
      ease: "power2.inOut",
      onComplete: () => {
        menu.classList.remove("show");
        menu.style.pointerEvents = "none";
      }
    });
  };

  // Eventos
  openBtn.addEventListener("click", showMenu);
  closeBtn.addEventListener("click", hideMenu);

  // Cerrar con ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      hideMenu();
    }
  });

  // Cerrar al hacer clic en un enlace del menú
  menuItems.forEach((item) => {
    item.addEventListener("click", () => {
      hideMenu();
    });
  });
});
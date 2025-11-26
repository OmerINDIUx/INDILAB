(function () {
  // ===============================
  // 1) Registro mínimo y seguro
  // ===============================
  if (typeof window.gsap === "undefined" || typeof window.ScrollTrigger === "undefined") {
    console.warn(
      "⚠️ GSAP o ScrollTrigger no están disponibles al cargar revealTextImages.js"
    );
    return; // No hacemos nada para no romper el resto de la página
  }

  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;

  gsap.registerPlugin(ScrollTrigger);

  // ✅ Logs de depuración
  function logSuccess(msg) {
    console.log(`✅ %c${msg}`, "color: green; font-weight: bold;");
  }
  function logInfo(msg) {
    console.log(`ℹ️ %c${msg}`, "color: dodgerblue; font-weight: bold;");
  }
  function logWarn(msg) {
    console.warn(`⚠️ ${msg}`);
  }
  function logError(msg, err) {
    console.error(`💥 ERROR: ${msg}`, err || "");
  }

  // ✅ Datos de las secciones (solo claves, no textos directos)
  const allSectionsData = [
    [
      {
        titleKey: "Technology as a Force for",
        titleKey2: "Human Potential",
        textKey: "text of Human Potential",
        textKey2: "text of Human Potential 2",
        anim: "img/avance_tech.svg",
        type: "arrow",
      },
      {
        titleKey: "Experimentation",
        titleKey2: "The Pathway to Innovation",
        textKey: "text of The Pathway to Innovation",
        anim: "img/experimentation.svg",
        type: "circle",
      },
      {
        titleKey: "Harnessing",
        titleKey2: "Collective Wisdom",
        textKey: "text of the Collective Wisdom",
        textKey2: "text of the Collective Wisdom 2",
        anim: "img/colective.svg",
        type: "circle",
      },
      {
        titleKey: "Public-Private Cooperation",
        titleKey2: "A Shared Responsibility",
        textKey: "text of the A Shared Responsibility",
        anim: "img/colaboration.svg",
        type: "circle",
      },
      {
        titleKey: "A Fluid",
        titleKey2: "and Responsive Agenda",
        textKey: "text of the and Responsive Agenda",
        anim: "img/fluid.svg",
        type: "circle",
      },
      {
        titleKey: "Collaboration",
        titleKey2: "and Partnership",
        textKey: "text of and Partnership",
        anim: "img/collavoration.svg",
        type: "circle",
      },
      {
        titleKey: "An Identity Rooted",
        titleKey2: "in Innovation",
        textKey: "text of the in Innovation",
        anim: "img/innovation.svg",
        type: "circle",
      },
    ],
  ];

  /**
   * 📌 Función para cargar y animar SVGs
   */
  async function loadAndAnimateSVG(container, svgPath, type) {
    if (!container) return console.warn("⚠️ Contenedor no encontrado");

    try {
      const response = await fetch(svgPath);
      if (!response.ok)
        throw new Error(`No se pudo cargar el SVG: ${response.status}`);

      const svgText = await response.text();
      const parser = new DOMParser();
      const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
      const svgElement = svgDoc.documentElement;

      svgElement.removeAttribute("width");
      svgElement.removeAttribute("height");
      svgElement.setAttribute("preserveAspectRatio", "xMidYMid meet");
      svgElement.style.width = "100%";
      svgElement.style.height = "auto";
      svgElement.style.display = "block";

      container.innerHTML = "";
      container.appendChild(svgElement);

      // 🔁 Animaciones (igual que en tu código original)
      if (type === "arrow") {
        const basePath = svgElement.querySelector("#rotCircle");
        if (!basePath) return console.warn("⚠️ No se encontró #rotCircle");

        const clones = [basePath];
        const numClones = 12;

        basePath.style.transformBox = "fill-box";
        basePath.style.transformOrigin = "center";

        for (let i = 1; i < numClones; i++) {
          const clone = basePath.cloneNode(true);
          clone.setAttribute("data-index", i);
          svgElement.appendChild(clone);
          clones.push(clone);
        }

        let angle = 0;
        function animate() {
          angle += 0.4;
          clones.forEach((path, i) => {
            path.style.transform = `rotateY(${angle + (360 / numClones) * i}deg)`;
          });
          requestAnimationFrame(animate);
        }
        animate();
      } else if (type === "circle") {
        const baseCircles = svgElement.querySelectorAll(".circle");
        const numClones = 12;
        const allClones = [];

        baseCircles.forEach((baseCircle, circleIndex) => {
          baseCircle.style.transformBox = "fill-box";
          baseCircle.style.transformOrigin = "center";

          const clones = [baseCircle];
          for (let i = 1; i < numClones; i++) {
            const clone = baseCircle.cloneNode(true);
            clone.setAttribute("data-index", `${circleIndex}-${i}`);
            svgElement.appendChild(clone);
            clones.push(clone);
          }
          allClones.push(clones);
        });

        let angle = 0;
        function animateCircles() {
          angle += 0.4;
          allClones.forEach((clones) =>
            clones.forEach((c, i) => {
              const offset = (360 / numClones) * i;
              c.style.transform = `rotateY(${angle + offset}deg)`;
            })
          );
          requestAnimationFrame(animateCircles);
        }
        animateCircles();
      }
    } catch (err) {
      console.error("💥 Error al cargar o animar el SVG:", err);
    }
  }

  // ✅ ScrollTrigger principal
  document
    .querySelectorAll(".text-image-section2")
    .forEach((sectionEl, sectionIndex) => {
      logInfo(`Inicializando sección ${sectionIndex}`);

      const titleEl = sectionEl.querySelector(".text-column2 .title2");
      const textEl = sectionEl.querySelector(".text-column2 .text-content2");
      const imgContainer = sectionEl.querySelector(
        ".image-column2 .content__img-wrapper2"
      );

      if (!titleEl || !textEl || !imgContainer) {
        return logWarn(`Sección ${sectionIndex}: faltan elementos`);
      }

      const slides = allSectionsData[sectionIndex];
      let currentIndex = -1;

      gsap.to(
        {},
        {
          scrollTrigger: {
            trigger: sectionEl,
            start: "top top",
            end: "+=" + slides.length * window.innerHeight,
            scrub: 1,
            pin: true,
            pinSpacing: true,

            onUpdate: (self) => {
              const index = Math.floor(self.progress * slides.length);
              const data = slides[index];

              if (index === currentIndex || !data) return;
              currentIndex = index;

              const container = sectionEl.querySelector(".container2");

              if (window.innerWidth > 900) {
                container.style.flexDirection =
                  index % 2 !== 0 ? "row-reverse" : "row";
              } else {
                container.style.flexDirection =
                  index % 2 !== 0 ? "column-reverse" : "column";
              }

              gsap.to([titleEl, textEl, imgContainer], {
                opacity: 0,
                y: 30,
                duration: 0.4,
                onComplete: () => {
                  titleEl.innerHTML = `
                    <span data-i18n="${data.titleKey}"></span>
                    ${
                      data.titleKey2
                        ? `<span data-i18n="${data.titleKey2}"></span>`
                        : ""
                    }
                  `;

                  textEl.innerHTML = `
                    <p data-i18n="${data.textKey}"></p>
                    ${
                      data.textKey2
                        ? `<p data-i18n="${data.textKey2}"></p>`
                        : ""
                    }
                  `;

                  imgContainer.innerHTML = "";
                  const animDiv = document.createElement("div");
                  animDiv.classList.add("content__img", "content__img--large");
                  animDiv.id = `svg_anim_${sectionIndex}_${index}`;
                  imgContainer.appendChild(animDiv);
                  loadAndAnimateSVG(animDiv, data.anim, data.type);

                  if (window.INDI?.translatePage) {
                    window.INDI.translatePage(window.INDI.getCurrentLang());
                  }

                  gsap.fromTo(
                    [titleEl, textEl, imgContainer],
                    { opacity: 0, y: 30 },
                    { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }
                  );
                },
              });
            },
          },
        }
      );
    });
})();

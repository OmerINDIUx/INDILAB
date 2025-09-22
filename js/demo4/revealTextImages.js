gsap.registerPlugin(ScrollTrigger, TextPlugin);

// Utilidades para debug
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

// Datos de las secciones
const allSectionsData = [
  [
    {
      title: "Technology as a Force for Human Potential",
      text: "Just as cities are crucibles of innovation, technology can be a catalyst that unlocks and amplifies human potential. When used ethically, it can empower overlooked communities, reveal invisible needs, and fuel just, informed decisions. At INDIlab, technology is a means to a greater purpose: improving urban life, fostering creativity, and sparking progress.",
      anim: "img/avance_tech.svg",
      type: "arrow"
    },
    {
      title: "Experimentation: The Pathway to Innovation ",
      text: "We embrace an experimental mindset, formulating hypotheses, prototyping, testing in real environments, and documenting every outcome. Success lies as much in what we learn from failure as in what we achieve. Each experiment—win or lose—expands our shared urban intelligence.",
      anim: "img/experimentation.svg",
      type: "circle"
    },
    {
      title: "Harnessing Collective Wisdom",
      text: "Cities speak: we listen The knowledge embedded in communities is a resource too vast to ignore. We seek it out through engagement with those who live, work, and create in urban environments. Often, the most valuable insights emerge at the edges of official systems—in markets, grassroots networks, and cultural practices. By integrating these unplanned systems, we design solutions that reflect the authentic character of cities. Public life thrives in the unpredictable rhythms of the street.",
      anim: "img/colective.svg",
      type: "circle"
    }
  ],
  [
    {
      title: "Public-Private Cooperation: A Shared Responsibility",
      text: "Urban challenges demand collaboration. Governments set the frameworks; the private sector brings agility, innovation, and sustainable models. Together, we achieve more than either could alone. The private sector’s role in public challenges isn’t just an ethical choice, it’s a long-term investment in resilience.",
      anim: "img/colaboration.svg",
      type: "circle"
    },
    {
      title: "A Fluid: and Responsive Agenda",
      text: "Our priorities evolve with the city. We track emerging trends and urgent needs, ready to respond with agility. From sustainability to infrastructure, mobility to public space, every innovation we create is rooted in the specific realities of people and place.",
      anim: "img/fluid.svg",
      type: "circle"
    },
    {
      title: "Collaboration and Partnership",
      text: "We unite disciplines and perspectives to tackle complex challenges. From academic labs to startups and civic organizations, our partnerships expand our capacity to innovate. Together, we form networks that test bold ideas and amplify the human experience.",
      anim: "img/collavoration.svg",
      type: "circle"
    },
    {
      title: "An Identity Rooted in Innovation",
      text: "INDIlab aims to be both an action hub and a thought leader in urban experimentation. Building on Grupo INDI’s legacy of transformative engineering, we now apply cutting-edge tools and methods to create impact for our clients and for the communities we serve.",
      anim: "img/innovation.svg",
      type: "circle"
    }
  ]
];

/**
 * Función genérica para cargar y animar SVG
 */
async function loadAndAnimateSVG(container, svgPath, type) {
  if (!container) return console.warn("⚠️ Contenedor no encontrado");

  try {
    const response = await fetch(svgPath);
    if (!response.ok) throw new Error(`No se pudo cargar el SVG: ${response.status}`);

    const svgText = await response.text();
    const parser = new DOMParser();
    const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
    const svgElement = svgDoc.documentElement;

    // 🔧 Forzar que el SVG sea responsive
    svgElement.removeAttribute("width");
    svgElement.removeAttribute("height");
    svgElement.setAttribute("preserveAspectRatio", "xMidYMid meet");
    svgElement.style.width = "100%";
    svgElement.style.height = "auto";
    svgElement.style.display = "block";

    container.innerHTML = "";
    container.appendChild(svgElement);

    // 🔁 Animaciones según tipo
    if (type === "arrow") {
      const basePath = svgElement.querySelector("#rotCircle");
      if (!basePath) return console.warn("⚠️ No se encontró #rotCircle");

      const clones = [basePath];
      const numClones = 12;

      basePath.style.transformBox = "fill-box";
      basePath.style.transformOrigin = "center";
      basePath.style.transformStyle = "preserve-3d";

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

      const line1 = svgElement.querySelector(".arrow1");
      const line2 = svgElement.querySelector(".arrow2");

      function animateArrowLine(line) {
        if (!line) return;
        const length = line.getTotalLength();
        line.style.strokeDasharray = length;
        line.style.strokeDashoffset = length;
        let start = null;
        function step(ts) {
          if (!start) start = ts;
          const progress = (ts - start) / 2000;
          line.style.strokeDashoffset = Math.max(length * (1 - progress), 0);
          if (progress < 1) requestAnimationFrame(step);
          else {
            line.style.strokeDashoffset = length;
            start = null;
            requestAnimationFrame(step);
          }
        }
        requestAnimationFrame(step);
      }

      if (line1) animateArrowLine(line1);
      if (line2) animateArrowLine(line2);
    } else if (type === "circle") {
      const baseCircles = svgElement.querySelectorAll(".circle");
      const numClones = 12;
      const allClones = [];

      baseCircles.forEach((baseCircle, circleIndex) => {
        baseCircle.style.transformBox = "fill-box";
        baseCircle.style.transformOrigin = "center";
        baseCircle.style.transformStyle = "preserve-3d";

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

      const lines = svgElement.querySelectorAll(".line");
      const cubes = svgElement.querySelectorAll(".cube");
      const circles = svgElement.querySelectorAll(".circle");

      cubes.forEach((cube, i) => {
        const line = lines[i % lines.length];
        const length = line.getTotalLength();
        const direction = Math.random() < 0.5 ? 1 : -1;
        let startTime = null;
        const duration = 10000 + Math.random() * 2000;

        function animateCube(timestamp) {
          if (!startTime) startTime = timestamp;
          const elapsed = (timestamp - startTime) % duration;
          const progress = elapsed / duration;
          const pos = direction === 1 ? progress : 1 - progress;
          const point = line.getPointAtLength(pos * length);
          cube.setAttribute("x", point.x - 6);
          cube.setAttribute("y", point.y - 6);

          circles.forEach((circle) => {
            const bbox = circle.getBBox();
            const padding = 5;
            const isNear =
              point.x + 12 > bbox.x - padding &&
              point.x < bbox.x + bbox.width + padding &&
              point.y + 12 > bbox.y - padding &&
              point.y < bbox.y + bbox.height + padding;
            if (isNear && !circle.classList.contains("pulsing")) {
              circle.classList.add("pulsing");
              setTimeout(() => circle.classList.remove("pulsing"), 400);
            }
          });

          requestAnimationFrame(animateCube);
        }
        requestAnimationFrame(animateCube);
      });
    }
  } catch (err) {
    console.error("💥 Error al cargar o animar el SVG:", err);
  }
}

// ScrollTrigger principal
document.querySelectorAll(".text-image-section2").forEach((sectionEl, sectionIndex) => {
  logInfo(`Inicializando sección ${sectionIndex}`);
  const titleEl = sectionEl.querySelector(".text-column2 .title2");
  const textEl = sectionEl.querySelector(".text-column2 .text-content2");
  const imgContainer = sectionEl.querySelector(".image-column2 .content__img-wrapper2");

  if (!titleEl || !textEl || !imgContainer) {
    return logWarn(`Sección ${sectionIndex}: faltan elementos`);
  }

  const slides = allSectionsData[sectionIndex];
  if (!slides || !slides.length) {
    return logWarn(`Sección ${sectionIndex}: no hay slides definidos`);
  }

  let currentIndex = -1;

  gsap.to({}, {
    scrollTrigger: {
      trigger: sectionEl,
      start: "top top",
      end: "+=" + slides.length * window.innerHeight,
      scrub: 1,
      pin: true,
      onUpdate: (self) => {
        const progress = self.progress;
        const index = Math.floor(progress * slides.length);

        if (index !== currentIndex) {
          currentIndex = index;
          const data = slides[index];
          if (!data) return logWarn(`Slide vacío en sección ${sectionIndex}, índice ${index}`);

          logInfo(`Sección ${sectionIndex}, mostrando slide ${index}: ${data.title}`);

          gsap.to([titleEl, textEl, imgContainer], {
            opacity: 0,
            y: 30,
            duration: 0.4,
            onComplete: () => {
              titleEl.innerText = data.title || "";
              textEl.innerText = data.text || "";
              imgContainer.innerHTML = "";

              const animDiv = document.createElement("div");
              animDiv.classList.add("content__img", "content__img--large");
              animDiv.id = `svg_anim_${sectionIndex}_${index}`;
              imgContainer.appendChild(animDiv);

              loadAndAnimateSVG(animDiv, data.anim, data.type);

              gsap.fromTo(
                [titleEl, textEl, imgContainer],
                { opacity: 0, y: 30 },
                { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }
              );
            },
          });
        }
      },
    },
  });
});

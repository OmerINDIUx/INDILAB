gsap.registerPlugin(ScrollTrigger, TextPlugin);

const sections = [
  {
    title: "Computer Vision for Public Life",
    text: "We’re adapting advanced computer vision tools—originally used to analyze user behavior in commercial spaces—to better understand how people interact with and within the public realm. This open-source initiative will deploy computer vision models in selected public spaces to analyze patterns of movement, interaction, and usage. The goal is to generate quantitative insights into urban livability, enabling more human-centered design decisions. The methodology draws from observational urbanism and spatial behavior analytics",
    img: "../../img/landing/computer vision.png"
  },
  {
    title: "Digital Twins for Curb, Parking, and Building Management",
    text: "We’re piloting a digital twin of a high-traffic parking facility in downtown Mexico City to explore this technology’s potential for managing curbs, mobility, and private and public spaces in real time. The site, located beneath one of the city’s major landmarks, faces constant disruption due to protests, events, and high pedestrian and vehicle flows. This project will map curb uses, traffic data, and local regulations, creating a digital layer of spatial intelligence. The model will expand to include the surrounding neighborhood, with open data integration and predictive capabilities. This digital twin bridges private asset management and public space governance on a single platform, enabling coordination between operational control of parking facilities and real-time oversight of curbs, traffic, and public realm dynamics.",
    img: "../../img/landing/parking twin.png"
  },
  {
    title: "Priotitary:",
    text: "Desing del parking.",
    img: "../../img/landing/paticipatory design.jpg"
  }
];

// Referencias
const titleEl = document.querySelector(".text-column .title");
const textEl = document.querySelector(".text-column .text-content");
const imgEl = document.querySelector(".image-column img");

// ScrollTrigger principal
let currentIndex = -1;

gsap.to({}, {
  scrollTrigger: {
    trigger: ".text-image-section",
    start: "top top",
    end: "+=" + (sections.length * window.innerHeight),
    scrub: 1,
    pin: true,
    onUpdate: (self) => {
      const progress = self.progress; // 0 → 1
      const index = Math.floor(progress * sections.length);

      if (index !== currentIndex) {
        currentIndex = index;
        const section = sections[Math.min(index, sections.length - 1)];

        // Animación suave de salida y entrada
        gsap.to([titleEl, textEl, imgEl], {
          opacity: 0,
          y: 30,
          duration: 0.5,
          onComplete: () => {
            // Cambiar contenido
            titleEl.innerText = section.title;
            textEl.innerText = section.text;
            imgEl.src = section.img;

            // Entrada
            gsap.fromTo([titleEl, textEl, imgEl],
              { opacity: 0, y: 30 },
              { opacity: 1, y: 0, duration: 0.8, ease: "power2.out" }
            );
          }
        });
      }
    }
  }
});

gsap.registerPlugin(ScrollTrigger, TextPlugin);

const allSectionsData = [
  // 🔹 Primera línea (texto izq / img der)
  [
    {
      title: "Computer Vision for Public Life",
      text: "We’re adapting advanced computer vision tools—originally used to analyze user behavior in commercial spaces—to better understand how people interact with and within the public realm. This open-source initiative will deploy computer vision models in selected public spaces to analyze patterns of movement, interaction, and usage. The goal is to generate quantitative insights into urban livability, enabling more human-centered design decisions. The methodology draws from observational urbanism and spatial behavior analytics",
      img: "../../img/landing/computer vision.png",
    },
    {
      title: "Digital Twins for Curb, Parking, and Building Management",
      text: "We’re piloting a digital twin of a high-traffic parking facility in downtown Mexico City to explore this technology’s potential for managing curbs, mobility, and private and public spaces in real time. The site, located beneath one of the city’s major landmarks, faces constant disruption due to protests, events, and high pedestrian and vehicle flows. This project will map curb uses, traffic data, and local regulations, creating a digital layer of spatial intelligence. The model will expand to include the surrounding neighborhood, with open data integration and predictive capabilities. This digital twin bridges private asset management and public space governance on a single platform, enabling coordination between operational control of parking facilities and real-time oversight of curbs, traffic, and public realm dynamics.",
      img: "../../img/landing/parking twin.png",
    },
    {
      title: "Participatory Design: Linear Park Underneath Elevated Highway",
      text: "We flip the traditional design process: the people who use public space get a say in how it looks, works, functions, and evolves. Through co-design workshops, user surveys, and embedded planning sessions with impacted population, we are integrating community voices and key stakeholders into early design phases of a linear park. Grounded in existing legal frameworks, this participatory methodology ensures design decisions reflect real-world needs and use cases. The model is modular and scalable, designed for replication across future urban projects. It also aims to establish a co-governance and co-management framework for the park, grounded in urban commons theory.",
      img: "../../img/landing/paticipatory design.jpg",
    },
  ],

  // 🔹 Segunda línea invertida (img izq / texto der)
  [
    {
      title:
        "Urban Mangrove: Bio-responsive infrastructure for an elevated urban highway",
      text: "We’re rethinking urban infrastructure to respond to climate challenges, with hybrid design solutions that blur the lines between nature and engineering.This approach enhances the environmental performance and resilience of new and existing urban elements through tactical design upgrades. It incorporates criteria like thermal regulation, water resilience, solar energy integration, water collection systems, low-maintenance durability, and emergency response mechanisms—built to withstand heatwaves, flooding, shade scarcity, and other climate stressors in dense urban environments.",
      img: "../../img/landing/urban magrove.jpg",
    },
    {
      title: "Urban Heat Islands: Open Innovation Challenge",
      text: "The first INDI Lab’ Urban Open Innovation Challenge invites architecture, urbanism, and design students to develop creative, actionable solutions that address one of the most pressing climate threats facing cities today. As climate change intensifies, urban heat islands (UHI) disproportionately affect vulnerable communities, impacting public health outcomes, and straining urban infrastructure. This challenge not only aims to surface innovative proposals that blend green and gray infrastructure—such as shading systems, vegetated surfaces, and heat-reflective materials—but also serves as a platform to engage the next generation of urban designers in shaping climate-resilient, equitable cities",
      img: "../../img/landing/mapa-calor.png",
    },

    {
      title: "INDI 100: Strategic Foresight ",
      text: "To mark Grupo INDI’s 50th anniversary, we’re launching a long-term foresight program to shape the company’s strategic horizon for the decades ahead. Leveraging methodologies from leading futurists, scenario planning models, and data-driven forecasting tactics, this initiative will track macro trends, emerging technologies, and societal shifts. We’ll combine data-driven research with participatory workshops with leadership and relevant stakeholders to build internal futures-thinking capabilities and align innovation with long-term value.",
      img: "../../img/landing/strategicforesight.jpg",
    },
  ],
];

document
  .querySelectorAll(".text-image-section")
  .forEach((sectionEl, sectionIndex) => {
    const titleEl = sectionEl.querySelector(".text-column .title");
    const textEl = sectionEl.querySelector(".text-column .text-content");
    const imgEl = sectionEl.querySelector(".image-column img");

    const slides = allSectionsData[sectionIndex]; // 👈 se usa el array correcto
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
          onUpdate: (self) => {
            const progress = self.progress;
            const index = Math.floor(progress * slides.length);

            if (index !== currentIndex) {
              currentIndex = index;
              const data = slides[Math.min(index, slides.length - 1)];

              gsap.to([titleEl, textEl, imgEl], {
                opacity: 0,
                y: 30,
                duration: 0.4,
                onComplete: () => {
                  titleEl.innerText = data.title;
                  textEl.innerText = data.text;
                  imgEl.src = data.img;

                  gsap.fromTo(
                    [titleEl, textEl, imgEl],
                    { opacity: 0, y: 30 },
                    { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }
                  );
                },
              });
            }
          },
        },
      }
    );
  });

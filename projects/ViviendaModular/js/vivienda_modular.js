gsap.registerPlugin(ScrollTrigger);

const canvas = document.getElementById("image-sequence");
const context = canvas.getContext("2d");

// 🎞 Configuración de frames
const frameStart = 1000;
const frameEnd = 1689;
const frameCount = frameEnd - frameStart + 1;
const currentFrame = (index) =>
  `images/image_sequences/vivienda_${frameStart + index}.png`;

const images = [];
const imageSeq = { frame: 0 };

// ⬆️ Zoom fijo (no depende de scroll)
const zoom = { scale: 1.1 }; // 1.1 = 10% de zoom, ajusta a gusto

// Pre-cargar imágenes
for (let i = 0; i < frameCount; i++) {
  const img = new Image();
  img.src = currentFrame(i);
  images.push(img);
}

// Ajuste de tamaño del canvas
function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
  render();
}
window.addEventListener("resize", resizeCanvas);
resizeCanvas();

// Función de renderizado aplicando zoom fijo
function render() {
  const img = images[imageSeq.frame];
  if (img && img.complete) {
    context.clearRect(0, 0, canvas.width, canvas.height);

    const scaledWidth = canvas.width * zoom.scale;
    const scaledHeight = canvas.height * zoom.scale;

    // Centrar imagen recortada
    const offsetX = (canvas.width - scaledWidth) / 2;
    const offsetY = (canvas.height - scaledHeight) / 2;

    context.drawImage(img, offsetX, offsetY, scaledWidth, scaledHeight);
  }
}

// 📌 Pin de la secuencia
ScrollTrigger.create({
  trigger: ".video-scroll-wrapper",
  start: "top top",
  endTrigger: ".TextLarge",
  end: "top top",
  pin: ".video-fullscreen",
  pinSpacing: false,
  scrub: false,
  markers: false,
});

// 🎞 Animación de frames ligada al scroll
gsap.to(imageSeq, {
  frame: frameCount - 1,
  snap: "frame",
  ease: "none",
  scrollTrigger: {
    trigger: ".video-scroll-wrapper",
    start: "top top",
    endTrigger: ".TextLarge",
    end: "top top",
    scrub: 1,
  },
  onUpdate: render,
});

// Fade out cuando llega el texto
gsap.to(".video-fullscreen", {
  opacity: 0,
  ease: "power2.out",
  scrollTrigger: {
    trigger: ".TextLarge",
    start: "top bottom",
    end: "top center",
    scrub: true,
  },
});

// Render inicial
images[0].onload = render;

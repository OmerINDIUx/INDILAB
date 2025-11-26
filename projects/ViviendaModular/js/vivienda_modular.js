gsap.registerPlugin(ScrollTrigger);

const canvas = document.getElementById("image-sequence");
const context = canvas.getContext("2d");

// 🎞 Configuración de frames
const frameStart = 100;
const frameEnd = 487;
const frameCount = frameEnd - frameStart + 1;
const currentFrame = (index) =>
  `images/vivienda_modular_sequence/contruccion_vivienda_modular${
    frameStart + index
  }.jpg`;

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
  // usa las dimensiones calculadas por CSS
  canvas.width = canvas.clientWidth;
  canvas.height = canvas.clientHeight;
  render();
}

window.addEventListener("resize", resizeCanvas);
resizeCanvas();

// Función de renderizado aplicando zoom fijo
function render() {
  const img = images[imageSeq.frame];
  if (!img || !img.complete) return;

  const cw = canvas.width;
  const ch = canvas.height;
  const iw = img.width;
  const ih = img.height;

  // ratio de imagen y de canvas
  const imgRatio = iw / ih;
  const canvasRatio = cw / ch;

  // "cover": que llene el canvas sin deformarse
  const baseScale = Math.max(cw / iw, ch / ih);
  const scale = baseScale * zoom.scale;

  const drawWidth = iw * scale;
  const drawHeight = ih * scale;

  // ⬅️ AQUÍ ESTABA EL TEMA
  // Antes: centrado  -> (cw - drawWidth) / 2
  // Ahora: pegado al lado derecho
  const offsetX = (cw - drawWidth) / 2;
  const offsetY = (ch - drawHeight) / 2; // centrada verticalmente

  context.clearRect(0, 0, cw, ch);
  context.drawImage(img, offsetX, offsetY, drawWidth, drawHeight);
}

// 📌 Pin de la secuencia
ScrollTrigger.create({
  trigger: ".video-scroll-wrapper",
  start: "top top",
  endTrigger: ".TextLarge",
  end: "bottom top",
  pin: ".video-fullscreen",
  pinSpacing: false,
  scrub: false,
  // markers: true,
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
  opacity: 0.1,
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

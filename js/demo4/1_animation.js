// Función para animar líneas SVG como si se estuvieran dibujando
function animateArrowLine(line) {
  if (!line) {
    console.warn("⚠️ animateArrowLine recibió un elemento nulo");
    return;
  }

  const length = line.getTotalLength();
  if (!length) {
    console.warn("⚠️ El path no tiene longitud: ", line);
    return;
  }

  line.style.strokeDasharray = length;
  line.style.strokeDashoffset = length;

  let start = null;

  function step(timestamp) {
    if (!start) start = timestamp;
    const progress = (timestamp - start) / 2000; // duración de 2 segundos
    const offset = Math.max(length * (1 - progress), 0);
    line.style.strokeDashoffset = offset;

    if (progress < 1) {
      requestAnimationFrame(step);
    } else {
      line.style.strokeDashoffset = length;
      start = null;
      requestAnimationFrame(step);
    }
  }

  requestAnimationFrame(step);
}

// Función asíncrona que carga y anima un SVG
async function loadAndAnimateSVG() {
  try {
    // 1. Seleccionar contenedor
    const container = document.getElementById('circle_arrow');
    if (!container) {
      console.error("❌ No se encontró el contenedor con id 'circle_arrow'");
      return;
    }

    // 2. Cargar el SVG
    const response = await fetch("img/avance_tech.svg");
    if (!response.ok) throw new Error(`No se pudo cargar el SVG: ${response.status}`);
    const svgText = await response.text();

    // 3. Parsear SVG
    const parser = new DOMParser();
    const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
    const svgElement = svgDoc.documentElement;
    if (!svgElement) {
      console.error("❌ No se pudo parsear el SVG correctamente");
      return;
    }

    // 4. Limpiar contenedor e insertar SVG
    container.innerHTML = "";
    container.appendChild(svgElement);

    // 5. Buscar elemento rotCircle
    const basePath = container.querySelector('#rotCircle');
    if (!basePath) {
      console.warn('⚠️ No se encontró el path con id "rotCircle" en el SVG');
      return;
    }

    basePath.style.transformBox = "fill-box";
    basePath.style.transformOrigin = "center";
    basePath.style.transformStyle = "preserve-3d";
    basePath.classList.add("meridiano");

    // 6. Clonar para efecto circular
    const numClones = 12;
    const clones = [basePath];
    for (let i = 1; i < numClones; i++) {
      const clone = basePath.cloneNode(true);
      clone.classList.add("meridiano");
      clone.setAttribute("data-index", i);
      svgElement.appendChild(clone);
      clones.push(clone);
    }

    // 7. Animación rotación
    let angle = 0;
    function animate() {
      angle += 0.4;
      clones.forEach((path, index) => {
        const offset = (360 / numClones) * index;
        const currentAngle = angle + offset;
        path.style.transform = `rotateY(${currentAngle}deg)`;
      });
      requestAnimationFrame(animate);
    }
    animate();

    // 8. Animar flechas
    const line1 = svgElement.querySelector('.arrow1');
    const line2 = svgElement.querySelector('.arrow2');
    if (line1) animateArrowLine(line1);
    else console.warn("⚠️ No se encontró '.arrow1'");
    if (line2) animateArrowLine(line2);
    else console.warn("⚠️ No se encontró '.arrow2'");

  } catch (err) {
    console.error("💥 Error al cargar o animar el SVG:", err);
  }
}

// Ejecutar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", loadAndAnimateSVG);

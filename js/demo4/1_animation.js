// Función para animar líneas SVG como si se estuvieran dibujando
function animateArrowLine(line) {
  const length = line.getTotalLength();
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
      // Reiniciar animación en loop
      line.style.strokeDashoffset = length;
      start = null;
      requestAnimationFrame(step);
    }
  }

  requestAnimationFrame(step);
}

// Función asíncrona que carga y anima un SVG con efecto 3D y flechas animadas
async function loadAndAnimateSVG() {
  try {
    // 1. Cargar el archivo SVG desde la carpeta img/
    const response = await fetch("img/avance_tech.svg");
    if (!response.ok) throw new Error(`No se pudo cargar el SVG: ${response.status}`);

    // 2. Obtener el texto del SVG
    const svgText = await response.text();

    // 3. Seleccionar el contenedor donde se insertará el SVG
    const container = document.getElementById('circle_arrow');

    // 4. Parsear el texto SVG como un documento XML
    const parser = new DOMParser();
    const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
    const svgElement = svgDoc.documentElement;

    // 5. Limpiar el contenedor y agregar el nuevo SVG
    container.innerHTML = "";
    container.appendChild(svgElement);

    // 6. Seleccionar el elemento dentro del SVG que será animado
    const basePath = container.querySelector('#rotCircle');
    if (!basePath) {
      console.warn('No se encontró el path con id "rotCircle"');
      return;
    }

    // 7. Configurar estilos necesarios para transformaciones 3D
    basePath.style.transformBox = "fill-box";
    basePath.style.transformOrigin = "center";
    basePath.style.transformStyle = "preserve-3d";
    basePath.classList.add("meridiano");

    // 8. Clonar el elemento base varias veces para crear un efecto circular
    const numClones = 12;
    const clones = [basePath];

    for (let i = 1; i < numClones; i++) {
      const clone = basePath.cloneNode(true);
      clone.classList.add("meridiano");
      clone.setAttribute("data-index", i);
      svgElement.appendChild(clone);
      clones.push(clone);
    }

    // 9. Inicializar ángulo de rotación
    let angle = 0;

    // 10. Función de animación en bucle para rotar clones
    function animate() {
      angle += 0.4; // velocidad de rotación

      clones.forEach((path, index) => {
        const offset = (360 / numClones) * index;
        const currentAngle = angle + offset;

        // Solo rotación en eje Y para efecto tipo "meridiano"
        path.style.transform = `
          rotateY(${currentAngle}deg)
        `;
      });

      requestAnimationFrame(animate);
    }

    animate(); // 11. Iniciar animación de rotación

    // 12. Seleccionar y animar las flechas si existen
    const line1 = svgElement.querySelector('.arrow1');
    const line2 = svgElement.querySelector('.arrow2');
    if (line1) animateArrowLine(line1);
    if (line2) animateArrowLine(line2);

  } catch (err) {
    console.error("Error al cargar o animar el SVG:", err);
  }
}

// 13. Ejecutar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", loadAndAnimateSVG);

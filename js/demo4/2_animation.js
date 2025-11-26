async function loadAndAnimateSVG() {
  try {
    const response = await fetch("img/experimentation.svg");
    if (!response.ok) throw new Error(`No se pudo cargar el SVG: ${response.status}`);

    const svgText = await response.text();
    const container = document.getElementById('experimentation');

    const parser = new DOMParser();
    const svgDoc = parser.parseFromString(svgText, "image/svg+xml");
    const svgElement = svgDoc.documentElement;

    container.innerHTML = "";
    container.appendChild(svgElement);

    // 🌐 Animación circular uniforme (NO aleatoria)
    const baseCircles = container.querySelectorAll('.circle');
    const numClones = 12;
    const allClones = [];

    baseCircles.forEach((baseCircle, circleIndex) => {
      baseCircle.style.transformBox = "fill-box";
      baseCircle.style.transformOrigin = "center";
      baseCircle.style.transformStyle = "preserve-3d";
      baseCircle.classList.add("meridiano");

      const clones = [baseCircle];

      for (let i = 1; i < numClones; i++) {
        const clone = baseCircle.cloneNode(true);
        clone.classList.add("meridiano");
        clone.setAttribute("data-index", `${circleIndex}-${i}`);
        svgElement.appendChild(clone);
        clones.push(clone);
      }

      allClones.push(clones);
    });

    let angle = 0;

    function animateCircles() {
      angle += 0.4; // Velocidad constante para todos
      allClones.forEach((clones) => {
        clones.forEach((circle, index) => {
          const offset = (360 / numClones) * index;
          const currentAngle = angle + offset;
          circle.style.transform = `rotateY(${currentAngle}deg)`;
        });
      });
      requestAnimationFrame(animateCircles);
    }

    animateCircles();

    // ➕ Animar los cubos por líneas de forma independiente
    const lines = svgElement.querySelectorAll('.line');
    const cubes = svgElement.querySelectorAll('.cube');
    const circles = svgElement.querySelectorAll('.circle');

    cubes.forEach((cube, index) => {
      const line = lines[index % lines.length];
      const length = line.getTotalLength();
      const direction = Math.random() < 0.5 ? 1 : -1;
      let startTime = null;
      const duration = 10000 + Math.random() * 2000; // Cada cubo tiene su duración aleatoria

      function animateCube(timestamp) {
        if (!startTime) startTime = timestamp;
        const elapsed = (timestamp - startTime) % duration;
        const progress = elapsed / duration;
        const pos = direction === 1 ? progress : 1 - progress;
        const point = line.getPointAtLength(pos * length);

        const x = point.x - 6;
        const y = point.y - 6;
        cube.setAttribute("x", x);
        cube.setAttribute("y", y);

        // Detectar colisión con círculos
        circles.forEach(circle => {
          const bbox = circle.getBBox();
          const padding = 5;

          const isNear =
            x + 12 > bbox.x - padding &&
            x < bbox.x + bbox.width + padding &&
            y + 12 > bbox.y - padding &&
            y < bbox.y + bbox.height + padding;

          if (isNear && !circle.classList.contains("pulsing")) {
            circle.classList.add("pulsing");
            setTimeout(() => circle.classList.remove("pulsing"), 400);
          }
        });

        requestAnimationFrame(animateCube);
      }

      requestAnimationFrame(animateCube);
    });

  } catch (err) {
    console.error("Error al cargar o animar el SVG:", err);
  }
}

document.addEventListener("DOMContentLoaded", loadAndAnimateSVG);

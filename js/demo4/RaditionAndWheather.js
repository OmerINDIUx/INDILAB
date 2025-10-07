/* Coordenadas CDMX ------------------------------------------------------*/
const lat = 19.4326;
const lon = -99.1332;

/* Utilidades de color ---------------------------------------------------*/
function hexToRgb(hex) {
  const [, r, g, b] = hex.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i);
  return [parseInt(r, 16), parseInt(g, 16), parseInt(b, 16)];
}
function rgbToHex([r, g, b]) {
  return (
    "#" +
    [r, g, b].map((v) => v.toString(16).padStart(2, "0")).join("").toUpperCase()
  );
}
function generateScale(startHex, endHex, steps = 7) {
  const start = hexToRgb(startHex);
  const end = hexToRgb(endHex);
  const scale = [];
  for (let i = 0; i < steps; i++) {
    const t = i / (steps - 1);
    const rgb = start.map((s, k) => Math.round(s + (end[k] - s) * t));
    scale.push(rgbToHex(rgb));
  }
  return scale;
}

/* Rangos de color -------------------------------------------------------*/
const RANGES = {
  s_cold: ["#18B2E8"],
  cold: ["#9244D6"],
  mild: ["#FFC043"],
  hot: ["#F86230"],
  s_hot: ["#A51C5B"],
  fallback: ["#18B2E8", "#A51C5B"],
};
const ORDER = ["s_cold", "cold", "mild", "hot", "s_hot"];

function categoryForTemp(t) {
  if (t < 19) return "cold";
  if (t < 28) return "mild";
  if (t < 35) return "hot";
  return "s_hot";
}

function paletteForTemps(nowT, futureT) {
  const catNow = categoryForTemp(nowT);
  const idxNow = ORDER.indexOf(catNow);
  const dir = futureT >= nowT ? 1 : -1;
  const idxEnd = Math.min(Math.max(idxNow + dir, 0), ORDER.length - 1);
  const catEnd = ORDER[idxEnd];
  return generateScale(RANGES[catNow][0], RANGES[catEnd][0]);
}

function applyColors(palette) {
  const demo = document.querySelector(".demo-4");
  if (!demo) {
    console.warn("⚠️ No se encontró .demo-4, no se aplicaron colores.");
    return;
  }
  palette.forEach((color, i) => {
    demo.style.setProperty(`--color-bg-${i + 1}`, color);
  });
}

/* 🎨 Colores para UV ----------------------------------------------------*/
function colorsForUV(uv) {
  if (uv <= 2) return ["#18B2E8", "#01213E"]; // Bajo
  if (uv <= 5) return ["#FFC043", "#F3A712"]; // Moderado
  if (uv <= 7) return ["#F86230", "#C93C00"]; // Alto
  if (uv <= 10) return ["#A51C5B", "#6B1F1F"]; // Muy alto
  return ["#6B1F1F", "#330000"]; // Extremo
}

/* 🔥 Animación en loop del degradado ------------------------------------*/
function startGradientLoop(gradient, baseStart, baseEnd, delay = 0) {
  if (!gradient) return;

  const stops = gradient.querySelectorAll("stop");
  if (stops.length < 2) return;

  let t = delay;
  function loop() {
    t += 0.01;
    const pulse = (Math.sin(t) + 1) / 2;
    const [r1, g1, b1] = hexToRgb(baseStart);
    const [r2, g2, b2] = hexToRgb(baseEnd);

    const midStart = `rgb(${Math.round(r1 + (r2 - r1) * pulse)},${Math.round(
      g1 + (g2 - g1) * pulse
    )},${Math.round(b1 + (b2 - b1) * pulse)})`;
    const midEnd = `rgb(${Math.round(r2 + (r1 - r2) * pulse)},${Math.round(
      g2 + (g1 - g2) * pulse
    )},${Math.round(b2 + (b1 - b2) * pulse)})`;

    stops[0].setAttribute("stop-color", midStart);
    stops[1].setAttribute("stop-color", midEnd);

    gradient._raf = requestAnimationFrame(loop);
  }

  if (gradient._raf) cancelAnimationFrame(gradient._raf);
  loop();
}

/* 🖌️ Aplica gradiente a un SVG específico -----------------------------*/
function applyCornerGradient(svgEl, startColor, endColor, delay = 0) {
  if (!svgEl) return;

  let defs = svgEl.querySelector("defs");
  if (!defs) {
    defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
    svgEl.prepend(defs);
  }

  const uniqueId = "corner-gradient-" + Math.random().toString(36).substr(2, 5);

  let gradient = document.createElementNS(
    "http://www.w3.org/2000/svg",
    "linearGradient"
  );
  gradient.setAttribute("id", uniqueId);
  gradient.setAttribute("x1", "0%");
  gradient.setAttribute("y1", "0%");
  gradient.setAttribute("x2", "0%");
  gradient.setAttribute("y2", "100%");
  gradient.innerHTML = `
    <stop offset="0%" stop-color="${startColor}"/>
    <stop offset="100%" stop-color="${endColor}"/>
  `;
  defs.appendChild(gradient);

  svgEl.querySelectorAll("path").forEach((path) => {
    path.setAttribute("fill", `url(#${uniqueId})`);
  });

  startGradientLoop(gradient, startColor, endColor, delay);
}

/* Carga inline de SVGs de esquinas -------------------------------------*/
document.querySelectorAll(".corner").forEach((container) => {
  const url = container.dataset.svg;
  if (!url) return;
  fetch(url)
    .then((r) => r.text())
    .then((svg) => (container.innerHTML = svg))
    .catch((err) => console.error(`Error cargando ${url}`, err));
});

/* URLs de Open-Meteo ---------------------------------------------------*/
const weatherUrl =
  `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
  `&hourly=temperature_2m&current=apparent_temperature&past_days=2&forecast_days=1`;

const airUrl =
  `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${lat}&longitude=${lon}` +
  `&hourly=pm10,pm2_5,carbon_monoxide,ozone,nitrogen_dioxide,sulphur_dioxide,us_aqi`;

const uvUrl =
  `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
  `&hourly=uv_index&current=uv_index&forecast_days=1`;

/* Fetch paralelo -------------------------------------------------------*/
Promise.all([fetch(weatherUrl), fetch(airUrl), fetch(uvUrl)])
  .then((responses) => Promise.all(responses.map((r) => r.json())))
  .then(([weatherData, airData, uvData]) => {
    /* --- CLIMA (para esquinas) --- */
    const tempNow = weatherData.current.apparent_temperature;
    const temp2h = weatherData.hourly.temperature_2m[8];
    console.log("🌡️ Clima:", tempNow, "°C | +8h:", temp2h, "°C");

    const climatePalette = paletteForTemps(tempNow, temp2h);
    applyColors(climatePalette);

    const climateStart = climatePalette[0];
    const climateEnd = climatePalette[climatePalette.length - 1];

    /* --- CALIDAD DEL AIRE (para mapa) --- */
    const currentAQI = airData.hourly.us_aqi[0];
    console.log("💨 AQI:", currentAQI);

    let startColor, endColor;
    if (currentAQI <= 50) [startColor, endColor] = ["#18B2E8", "#01213E"];
    else if (currentAQI <= 100) [startColor, endColor] = ["#F677A7", "#9244D6"];
    else if (currentAQI <= 150) [startColor, endColor] = ["#FFC043", "#F86230"];
    else [startColor, endColor] = ["#FFB9A1", "#A51C5B"];

    const mainSvg = document.querySelector("svg .colorkey")?.closest("svg");
    if (mainSvg) {
      let defs = mainSvg.querySelector("defs");
      if (!defs) {
        defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
        mainSvg.prepend(defs);
      }
      let gradient = mainSvg.querySelector("#dynamic-air-gradient");
      if (!gradient) {
        gradient = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "linearGradient"
        );
        gradient.setAttribute("id", "dynamic-air-gradient");
        gradient.setAttribute("x1", "0%");
        gradient.setAttribute("y1", "0%");
        gradient.setAttribute("x2", "0%");
        gradient.setAttribute("y2", "100%");
        gradient.innerHTML = `
          <stop offset="0%" stop-color="${startColor}"/>
          <stop offset="100%" stop-color="${endColor}"/>
        `;
        defs.appendChild(gradient);
      }
      document.querySelectorAll("svg .colorkey path").forEach((path) => {
        path.setAttribute("fill", "url(#dynamic-air-gradient)");
      });
      startGradientLoop(gradient, startColor, endColor);
    }

    /* --- RADIACIÓN UV (para esquinas y títulos) --- */
    const currentUV = uvData.current.uv_index;
    console.log("🌞 UV Index:", currentUV);
    const [uvStart, uvEnd] = colorsForUV(currentUV);

    // Aplica gradiente en las esquinas
    document.querySelectorAll(".corner svg").forEach((svgEl, index) => {
      applyCornerGradient(svgEl, uvStart, uvEnd, index * 0.5);
    });

    // Cambia color de los títulos h2
    document.querySelectorAll(".title2").forEach((title) => {
      title.style.color = uvStart;
    });

    /* --- Esquinas de clima (opcional si quieres que convivan) --- */
    // document.querySelectorAll(".corner svg").forEach((svgEl, index) => {
    //   applyCornerGradient(svgEl, climateStart, climateEnd, index * 0.5);
    // });
  })
  .catch((err) => {
    console.error("❌ Error al obtener datos de clima/aire/uv:", err);
    applyColors(generateScale(...RANGES.fallback));
  });

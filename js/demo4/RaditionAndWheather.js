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
    "#" + [r, g, b].map(v => v.toString(16).padStart(2, "0")).join("").toUpperCase()
  );
}
function generateScale(startHex, endHex, steps = 7) {
  const start = hexToRgb(startHex);
  const end   = hexToRgb(endHex);
  const scale = [];
  for (let i = 0; i < steps; i++) {
    const t   = i / (steps - 1);
    const rgb = start.map((s, k) => Math.round(s + (end[k] - s) * t));
    scale.push(rgbToHex(rgb));
  }
  return scale;
}

/* Rangos de color -------------------------------------------------------*/
const RANGES = {
  s_cold : ["#18B2E8"],
  cold   : ["#9244D6"],
  mild   : ["#FFC043"],
  hot    : ["#F86230"],
  s_hot  : ["#A51C5B"],
  fallback: ["#18B2E8", "#A51C5B"], 
};
const ORDER = ["s_cold", "cold", "mild", "hot", "s_hot"];

function categoryForTemp(t){
  if (t < 19)  return "cold";
  if (t < 28)  return "mild";
  if (t < 35)  return "hot";
  return "s_hot";
}

function paletteForTemps(nowT, futureT){
  const catNow = categoryForTemp(nowT);
  const idxNow = ORDER.indexOf(catNow);
  const dir    = futureT >= nowT ? 1 : -1;
  const idxEnd = Math.min(Math.max(idxNow + dir, 0), ORDER.length - 1);
  const catEnd = ORDER[idxEnd];
  return generateScale(RANGES[catNow][0], RANGES[catEnd][0]);
}

function applyColors(palette){
  const demo = document.querySelector(".demo-4");
  if (!demo) {
    console.warn("⚠️ No se encontró .demo-4, no se aplicaron colores.");
    return;
  }
  palette.forEach((color, i) => {
    demo.style.setProperty(`--color-bg-${i + 1}`, color);
  });
}

/* 🔥 Animación en loop del degradado ------------------------------------*/
function startGradientLoop(gradient, baseStart, baseEnd) {
  if (!gradient) return;

  const stops = gradient.querySelectorAll("stop");
  if (stops.length < 2) return;

  let t = 0;
  function loop() {
    t += 0.01; // velocidad
    const pulse = (Math.sin(t) + 1) / 2; // 0 → 1 oscilante
    const [r1, g1, b1] = hexToRgb(baseStart);
    const [r2, g2, b2] = hexToRgb(baseEnd);

    // Genera colores intermedios
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

/* URLs de Open-Meteo ---------------------------------------------------*/
const weatherUrl =
  `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
  `&hourly=temperature_2m&current=apparent_temperature&past_days=2&forecast_days=1`;

const airUrl =
  `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${lat}&longitude=${lon}` +
  `&hourly=pm10,pm2_5,carbon_monoxide,ozone,nitrogen_dioxide,sulphur_dioxide,us_aqi`;

/* Fetch paralelo -------------------------------------------------------*/
Promise.all([fetch(weatherUrl), fetch(airUrl)])
  .then(responses => Promise.all(responses.map(r => r.json())))
  .then(([weatherData, airData]) => {
    /* --- CLIMA --- */
    const tempNow = weatherData.current.apparent_temperature;
    const temp2h = weatherData.hourly.temperature_2m[8];  

    console.log("🌡️ Clima:", tempNow, "°C | +2h:", temp2h, "°C");
    applyColors(paletteForTemps(tempNow, temp2h));

    /* --- CALIDAD DEL AIRE --- */
    const currentAQI = airData.hourly.us_aqi[0];
    console.log("💨 AQI:", currentAQI);

    let startColor, endColor;
    if (currentAQI <= 50) [startColor, endColor] = ["#18B2E8", "#01213E"];
    else if (currentAQI <= 100) [startColor, endColor] = ["#FFC043", "#F86230"];
    else if (currentAQI <= 150) [startColor, endColor] = ["#F677A7", "#9244D6"];
    else [startColor, endColor] = ["#FFB9A1", "#A51C5B"];

    const svg = document.querySelector("svg");
    if (svg) {
      let defs = svg.querySelector("defs");
      if (!defs) {
        defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
        svg.prepend(defs);
      }

      let gradient = svg.querySelector("#dynamic-air-gradient");
      if (!gradient) {
        gradient = document.createElementNS("http://www.w3.org/2000/svg", "linearGradient");
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

      // Aplica el gradiente a todos los elementos con .colorkey
      document.querySelectorAll("svg .colorkey path").forEach(path => {
        path.setAttribute("fill", "url(#dynamic-air-gradient)");
      });

      console.log(`🎨 Degradado dinámico: ${startColor} → ${endColor}`);
      startGradientLoop(gradient, startColor, endColor); // 🔥 animación infinita
    }
  })
  .catch(err => {
    console.error("❌ Error al obtener datos de clima/aire:", err);
    applyColors(generateScale(...RANGES.fallback));
  });

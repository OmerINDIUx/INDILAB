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
  fallback: ["#18B2E8", "#A51C5B"],       // degradado total si falla el fetch
};
const ORDER = ["s_cold", "cold", "mild", "hot", "s_hot"];

/* Categorización de temperatura -----------------------------------------*/
function categoryForTemp(t){
  if (t < 19)  return "cold";
  if (t < 28)  return "mild";
  if (t < 35)  return "hot";
  return "s_hot";
}

/* Genera la paleta según temp actual y a 2 h ----------------------------*/
/* Genera la paleta según temp actual y a 2 h ---------------------------*/
function paletteForTemps(nowT, futureT){
  const catNow = categoryForTemp(nowT);
  const idxNow = ORDER.indexOf(catNow);

  //          ⬇️  si es >= sube | si es < baja
  const dir    = futureT >= nowT ? 1 : -1;

  const idxEnd = Math.min(Math.max(idxNow + dir, 0), ORDER.length - 1);
  const catEnd = ORDER[idxEnd];
  return generateScale(RANGES[catNow][0], RANGES[catEnd][0]);
}


/* Aplica la escala al CSS ----------------------------------------------*/
function applyColors(palette){
  const demo = document.querySelector(".demo-4");
  palette.forEach((color, i) => {
    demo.style.setProperty(`--color-bg-${i + 1}`, color);
  });
}

/* Llamada a Open-Meteo --------------------------------------------------*/
const url =
  `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}` +
  `&hourly=temperature_2m&current=apparent_temperature&past_days=2&forecast_days=1`;


fetch(url)
  .then(r => r.json())
  .then(data => {
    // console.log("Respuesta completa del API:", data); 

    /* temp actual */
    const tempNow = data.current.apparent_temperature;
    // console.log("temperatura actual:", tempNow);
    const temp2h = data.hourly.temperature_2m[8];  
    // console.log("temperatura +2 hrs:", temp2h);

    /* Paleta y aplicación ----------------------------------------------*/
    const palette = paletteForTemps(tempNow, temp2h);
    applyColors(palette);
    // console.log(`Ahora: ${tempNow} °C | +8 h: ${temp2h} °C →`, palette);
  })
  .catch(err => {
    console.error("Error al obtener clima. Paleta por defecto:", err);
    applyColors(generateScale(...RANGES.fallback));
  });


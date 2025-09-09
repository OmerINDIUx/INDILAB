// const targetUrl = 'http://aire.cdmx.gob.mx/default.php';
// const proxyUrl  = 'https://api.allorigins.win/get?url=';

// fetch(proxyUrl + encodeURIComponent(targetUrl))
//   .then(response => response.text())
//   .then(html => {
//     const parser = new DOMParser();
//     const doc = parser.parseFromString(html, 'text/html');

//     // Buscar el div que contiene el índice
//     const contenedor = doc.querySelector('#renglondosdatoscalidadaireahora');
//     if (!contenedor) {
//       console.log('No se encontró el contenedor renglondosdatoscalidadaireahora');
//       return;
//     }

//     // Buscar el strong dentro de ese div con el texto esperado
//     const strongs = contenedor.querySelectorAll('strong');
//     let textoIndice = null;

//     strongs.forEach(el => {
//       if (el.textContent.trim().startsWith('Índice AIRE Y SALUD:')) {
//         textoIndice = el.textContent.trim();
//       }
//     });

//     if (!textoIndice) {
//       console.log('No se encontró texto con "Índice AIRE Y SALUD:"');
//       return;
//     }

//     // Extraer la palabra clave (buena, aceptable, mala, etc)
//     const match = textoIndice.match(/Índice AIRE Y SALUD:\s*(\w+)/i);
//     const palabra = match ? match[1].toLowerCase() : null;

//     // Mapa de colores
//     const colorMap = {
//       buena: '#18B2E8',
//       aceptable: '#9244D6',
//       mala: '#FFC043',
//       muy: '#F86230',             // para "muy mala"
//       extremadamente: '#A51C5B',  // para "extremadamente mala"
//     };

//     // Determinar color según palabra clave, cuidando "muy mala" y "extremadamente mala"
//     let color = '#9244D6'; // color default

//     if (palabra === 'buena') color = colorMap.buena;
//     else if (palabra === 'aceptable') color = colorMap.aceptable;
//     else if (palabra === 'mala') color = colorMap.mala;
//     else if (textoIndice.toLowerCase().includes('muy mala')) color = colorMap['muy'];
//     else if (textoIndice.toLowerCase().includes('extremadamente mala')) color = colorMap['extremadamente'];

//     // Mostrar en consola
//     console.log(`Índice Aire y Salud: "${textoIndice}", Color: ${color}`);

//     // Opcional: actualizar contenido en la página actual (si quieres)
//     const h2 = document.querySelector('.content__title_W');
//     if (h2) {
//       // Mantener un span inicial si existe
//       const span = h2.querySelector('span');
//       h2.innerHTML = '';
//       if(span) h2.appendChild(span);

//       // Crear nuevo span con palabra y color
//       const colorSpan = document.createElement('span');
//       colorSpan.textContent = palabra ? palabra.charAt(0).toUpperCase() + palabra.slice(1) : textoIndice;
//       colorSpan.style.fontWeight = 'bold';
//       colorSpan.style.color = color;

//       h2.appendChild(colorSpan);
//     }
//   })
//   .catch(err => {
//     console.error('Error:', err);
//   });



// aire_cdmx.js

const targetUrl = 'http://aire.cdmx.gob.mx/default.php';
const proxyUrl = 'https://api.allorigins.win/get?url=';

fetch(proxyUrl + encodeURIComponent(targetUrl))
  .then(response => response.text())
  .then(html => {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, "text/html");

    let uvColor = "#18B2E8";
    let aireColor = "#18B2E8";

    // Índice UV
    const uvImg = doc.querySelector("#indiceuvimagen img");
    if (uvImg?.src) {
      const match = uvImg.src.match(/(\d+)\.gif$/);
      const num = match ? +match[1] : null;

      uvColor = {
        0: "#18B2E8", 1: "#18B2E8", 2: "#18B2E8",
        3: "#9244D6", 4: "#9244D6", 5: "#9244D6",
        6: "#f66b58", 7: "#f66b58",
        8: "#FFC043", 9: "#FFC043", 10: "#FFC043", 11: "#FFC043",
        12: "#F86230", 13: "#F86230", 14: "#F86230", 15: "#F86230",
      }[num] || "#18B2E8";

      document.dispatchEvent(new CustomEvent("uvReady", {
        detail: { src: uvImg.src, color: uvColor }
      }));
    }

    // Calidad del aire
    const contenedor = doc.querySelector("#renglondosdatoscalidadaireahora");
    const strong = contenedor && [...contenedor.querySelectorAll("strong")]
      .find(el => el.textContent.trim().toLowerCase().startsWith("índice aire y salud"));

    if (strong) {
      const textoIndice = strong.textContent.trim().toLowerCase();

      if (textoIndice.includes("aceptable")) aireColor = "#9244D6";
      else if (textoIndice.includes("muy mala") || textoIndice.includes("extremadamente mala"))
        aireColor = textoIndice.includes("extremadamente") ? "#A51C5B" : "#F86230";
      else if (textoIndice.includes("mala")) aireColor = "#FFC043";

      // Guardamos color global para evitar perder evento
      window.aireSaludColor = aireColor;

      document.dispatchEvent(new CustomEvent("aireSaludReady", {
        detail: { textoIndice, color: aireColor }
      }));
    }

    // Aplicar gradiente combinado a CSS raíz (opcional)
    const gradiente = `linear-gradient(90deg, ${aireColor}, ${uvColor})`;
    document.documentElement.style.setProperty('--color-highlight-start', gradiente);

    console.log("Gradiente:", gradiente);
  })
  .catch(err => {
    console.error("Error al obtener datos:", err);
  });


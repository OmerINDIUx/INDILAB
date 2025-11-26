(() => {
  const video = document.getElementById("trafficVideo");
  const slider = document.getElementById("hourSlider");
  const hourLabel = document.getElementById("hourLabel");

  // Ruta del video
  video.src = "video/trafico_cdmx.mp4";

  const TOTAL_HOURS = 24;
  const SECONDS_PER_HOUR = 10; // 10s = 1h
  const VIDEO_DURATION = TOTAL_HOURS * SECONDS_PER_HOUR; // 240s = 4min

  // Hora actual en CDMX
  function getHourInCDMX() {
    const hourPart = new Intl.DateTimeFormat("en-US", {
      timeZone: "America/Mexico_City",
      hour12: false,
      hour: "2-digit",
    })
      .formatToParts(new Date())
      .find((p) => p.type === "hour");
    return parseInt(hourPart.value, 10);
  }

  // Formato HH:00
  function formatHour(hour) {
    return `${String(hour).padStart(2, "0")}:00`;
  }

  // Saltar a una hora específica en el video
  function applyHour(hour) {
    video.currentTime = (hour % 24) * SECONDS_PER_HOUR;
    hourLabel.textContent = formatHour(hour);

    gsap.fromTo(
      hourLabel,
      { scale: 1.2, opacity: 0.8 },
      { scale: 1, opacity: 1, duration: 0.3, ease: "back.out(2)" }
    );
  }

  // Actualizar slider mientras el video avanza
  video.addEventListener("timeupdate", () => {
    const currentHour = Math.floor(video.currentTime / SECONDS_PER_HOUR) % 24;
    slider.value = currentHour;
    hourLabel.textContent = formatHour(currentHour);
  });

  // Control manual desde slider
  slider.addEventListener("input", (e) => {
    applyHour(+e.target.value);
  });

  // Inicialización
  document.addEventListener("DOMContentLoaded", () => {
    const horaActual = getHourInCDMX();
    slider.value = horaActual;
    applyHour(horaActual);

    video.loop = true;
    video.play().catch(err => {
      console.warn("Autoplay bloqueado, requiere interacción del usuario:", err);
    });
  });
})();

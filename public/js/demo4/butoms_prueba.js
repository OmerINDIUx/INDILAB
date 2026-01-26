document.addEventListener('DOMContentLoaded', () => {
  const buttons = document.querySelectorAll('.lang-btn, .hamburger');

  if (!buttons.length) {
    console.error("❌ No se encontraron botones .lang-btn o .hamburger en el DOM");
    return;
  }

  console.log(`✅ Se detectaron ${buttons.length} botones para aplicar efecto.`);

  buttons.forEach((btn, i) => {
    console.log(`🔗 Inicializando botón #${i + 1}:`, btn);

    let raf = null;

    function update(e) {
      try {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        console.log(`🖱 [${btn.id || btn.className}] x=${x.toFixed(1)}, y=${y.toFixed(1)}`);

        btn.style.setProperty('--gx', x + 'px');
        btn.style.setProperty('--gy', y + 'px');
      } catch (error) {
        console.error("⚠️ Error en update():", error);
      } finally {
        raf = null;
      }
    }

    btn.addEventListener('pointermove', (e) => {
      if (raf === null) raf = requestAnimationFrame(() => update(e));
    });

    btn.addEventListener('pointerenter', (e) => {
      console.log(`👆 Mouse entró en ${btn.id || btn.className}`);
      const rect = btn.getBoundingClientRect();
      // Si no hay coordenadas (ej. touch), centramos el degradado
      const x = e.clientX ? (e.clientX - rect.left) : rect.width / 2;
      const y = e.clientY ? (e.clientY - rect.top) : rect.height / 2;
      btn.style.setProperty('--gx', x + 'px');
      btn.style.setProperty('--gy', y + 'px');
    });

    btn.addEventListener('pointerleave', () => {
      console.log(`👋 Mouse salió de ${btn.id || btn.className}`);
    });
  });
});

const smoothScrollTo = (targetY, duration = 1000) => {
      const startY = window.scrollY;
      const distance = targetY - startY;
      let startTime = null;

      const step = (timestamp) => {
        if (!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        const percent = Math.min(progress / duration, 1);
        window.scrollTo(0, startY + distance * percent);
        if (percent < 1) requestAnimationFrame(step);
      };

      requestAnimationFrame(step);
    };

    document.querySelectorAll('.menu__item').forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const target = link.getAttribute('data-scrollto');
        if (target === '0') {
          smoothScrollTo(0, 2000); // scroll al top, más lento
        } else {
          const href = link.getAttribute('href');
          const el = document.querySelector(href);
          if (el) {
            const offsetTop = el.getBoundingClientRect().top + window.scrollY;
            smoothScrollTo(offsetTop, 1000); // scroll más lento al ID
          }
        }
      });
    });
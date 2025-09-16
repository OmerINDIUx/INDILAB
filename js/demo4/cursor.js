const cursor = document.querySelector('.custom-cursor');
  const buttons = document.querySelectorAll('button');

  let mouseX = 0, mouseY = 0;
  let currentX = 0, currentY = 0;

  document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
  });

  function animateCursor() {
    currentX += (mouseX - currentX) * 0.15;
    currentY += (mouseY - currentY) * 0.15;
    cursor.style.left = `${currentX}px`;
    cursor.style.top = `${currentY}px`;
    requestAnimationFrame(animateCursor);
  }

  animateCursor();

  buttons.forEach(button => {
    button.addEventListener('mouseenter', () => {
      cursor.classList.add('hover');
    });
    button.addEventListener('mouseleave', () => {
      cursor.classList.remove('hover');
    });
  });

  document.addEventListener('mousedown', () => {
    cursor.classList.add('clicked');
  });

  document.addEventListener('mouseup', () => {
    setTimeout(() => {
      cursor.classList.remove('clicked');
    }, 400);
  });
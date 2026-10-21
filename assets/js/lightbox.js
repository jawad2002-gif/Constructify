console.log('Lightbox script loaded');

(function () {
  const grid = document.getElementById('projectGrid');
  if (!grid) {
    console.log('Lightbox: #projectGrid not found');
    return;
  }

  const CAPTION = 'Fullscreen'; // text shown under the image in the video

  const box = document.createElement('div');
  box.className = 'lb';
  box.innerHTML =
    '<button class="lb-close" aria-label="Close"><i class="bi bi-x-lg"></i></button>' +
    '<button class="lb-nav lb-prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>' +
    '<div class="lb-stage">' +
      '<img class="lb-img" alt="">' +
      '<div class="lb-caption"></div>' +
    '</div>' +
    '<button class="lb-nav lb-next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>';
  document.body.appendChild(box);

  const img = box.querySelector('.lb-img');
  const caption = box.querySelector('.lb-caption');
  let list = [];
  let index = 0;

  const visibleCards = () =>
    Array.from(grid.querySelectorAll('.project-item:not(.hide) .project-card'));

  const imageOf = (card) =>
    card.style.backgroundImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');

  function show() {
    img.classList.remove('in');
    img.src = imageOf(list[index]);
    img.alt = list[index].querySelector('h5').textContent;
    caption.textContent = CAPTION;
    requestAnimationFrame(() => img.classList.add('in'));
    const single = list.length < 2;
    box.querySelector('.lb-prev').style.visibility = single ? 'hidden' : 'visible';
    box.querySelector('.lb-next').style.visibility = single ? 'hidden' : 'visible';
  }

  function open(card) {
    list = visibleCards();
    index = Math.max(0, list.indexOf(card));
    show();
    box.classList.add('open');
    document.body.classList.add('lb-lock');
  }

  function close() {
    box.classList.remove('open');
    document.body.classList.remove('lb-lock');
  }

  function step(n) {
    index = (index + n + list.length) % list.length;
    show();
  }

  // The first round button (expand icon) opens the lightbox. The arrow button does nothing.
  grid.addEventListener('click', (e) => {
    const btn = e.target.closest('.project-actions span');
    if (!btn || btn !== btn.parentElement.firstElementChild) return;
    open(btn.closest('.project-card'));
  });

  box.querySelector('.lb-close').addEventListener('click', close);
  box.querySelector('.lb-prev').addEventListener('click', () => step(-1));
  box.querySelector('.lb-next').addEventListener('click', () => step(1));
  box.addEventListener('click', (e) => {
    if (e.target === box || e.target.classList.contains('lb-stage')) close();
  });
  document.addEventListener('keydown', (e) => {
    if (!box.classList.contains('open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') step(-1);
    if (e.key === 'ArrowRight') step(1);
  });
})();
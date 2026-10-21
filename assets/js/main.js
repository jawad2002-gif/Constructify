// ---------- Back to top button ----------
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
  backToTop.classList.toggle('show', window.scrollY > 300);
});

backToTop.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ---------- Counters count up when they scroll into view ----------
const counters = document.querySelectorAll('.count');

const animate = (el) => {
  const target = +el.dataset.target;
  const step = Math.max(1, Math.ceil(target / 60));
  let n = 0;
  const t = setInterval(() => {
    n = Math.min(target, n + step);
    el.textContent = n;
    if (n >= target) clearInterval(t);
  }, 25);
};

const io = new IntersectionObserver((entries) => {
  entries.forEach((e) => {
    if (e.isIntersecting) {
      animate(e.target);
      io.unobserve(e.target);
    }
  });
}, { threshold: 0.5 });

counters.forEach((c) => io.observe(c));

// ---------- Projects filter ----------
const filterBtns = document.querySelectorAll('.filter-btn');
const projectItems = document.querySelectorAll('.project-item');

filterBtns.forEach((btn) => {
  btn.addEventListener('click', () => {
    filterBtns.forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');

    const filter = btn.dataset.filter;
    projectItems.forEach((item) => {
      const show = filter === 'all' || item.dataset.category === filter;
      item.classList.toggle('hide', !show);
    });
  });
});
function openZoom(imageSrc) {
  const modal = document.getElementById("zoomModal");
  const zoomedImg = document.getElementById("zoomedImg");
  
  zoomedImg.src = imageSrc;
  modal.style.display = "flex";
}

function closeZoom() {
  const modal = document.getElementById("zoomModal");
  modal.style.display = "none";
}
// ---------- Project lightbox (expand button on a project card) ----------
(function () {
  const grid = document.getElementById('projectGrid');
  if (!grid) return;

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
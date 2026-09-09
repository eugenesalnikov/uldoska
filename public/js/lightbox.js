document.addEventListener('DOMContentLoaded', () => {
  const box = document.querySelector('[data-lightbox]');
  if (!box) {

  } else {
    const img = box.querySelector('img');
    const items = [...document.querySelectorAll('[data-gallery] [data-full]')];
    let index = 0;

    const open = (i) => {
      index = i;
      img.src = items[index].dataset.full;
      box.hidden = false;
    };

    const close = () => {
      box.hidden = true;
      img.src = '';
    };

    const syncNav = () => {
      const hide = items.length < 2;
      box.querySelector('[data-lightbox-prev]').classList.toggle('is-hidden', hide);
      box.querySelector('[data-lightbox-next]').classList.toggle('is-hidden', hide);
    };

    syncNav();

    box.addEventListener('click', (e) => {
      if (e.target === box) close();
    });

    items.forEach((item, i) => item.addEventListener('click', () => open(i)));
    box.querySelector('[data-lightbox-close]').addEventListener('click', close);
    box.querySelector('[data-lightbox-prev]').addEventListener('click', () => open((index - 1 + items.length) % items.length));
    box.querySelector('[data-lightbox-next]').addEventListener('click', () => open((index + 1) % items.length));

    document.addEventListener('keydown', (e) => {
      if (box.hidden) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') open((index - 1 + items.length) % items.length);
      if (e.key === 'ArrowRight') open((index + 1) % items.length);
    });
  }
});

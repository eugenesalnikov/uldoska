document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('[data-photos]');
  if (!input) return;

  const maxFile = Number(input.dataset.maxFile) || 5 * 1024 * 1024;
  const maxFiles = Number(input.dataset.maxFiles) || 8;

  input.addEventListener('change', () => {
    const files = [...input.files];
    const dt = new DataTransfer();

    for (const file of files) {
      if (file.size > maxFile) {
        alert(`Файл «${file.name}» больше 5 МБ и не будет добавлен.`);
        continue;
      }

      if (dt.files.length >= maxFiles) {
        continue;
      }

      dt.items.add(file);
    }

    if (files.length > maxFiles) {
      alert(`Можно загрузить не больше ${maxFiles} фото. Лишние не добавлены.`);
    }

    input.files = dt.files;
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('#photos');
  const form = document.querySelector('#sendListingForm');
  if (!input || !form) return;

  const submitBtn = form.querySelector('[type="submit"]');
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const existing = JSON.parse(input.dataset.existing || '[]');

  FilePond.registerPlugin(
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
  );

  const pond = FilePond.create(input, {
    labelIdle: 'Перетащите фото сюда или <span class="filepond--label-action">выберите</span>',
    labelFileProcessing: 'Загрузка',
    labelFileProcessingComplete: 'Загружено',
    labelTapToCancel: 'отменить',
    labelTapToRetry: 'повторить',
    labelTapToUndo: 'отменить',
    labelButtonRemoveItem: 'Удалить',
    labelMaxFileSizeExceeded: 'Файл больше 5 МБ',
    labelMaxTotalFileSizeExceeded: 'Можно загрузить не больше 8 фото',
    allowReorder: true,
    name: 'filepond',
    allowMultiple: true,
    maxFiles: Number(input.dataset.maxFiles),
    maxFileSize: `${input.dataset.maxSize}KB`,
    acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    instantUpload: true,
    credits: false,
    files: existing.map((uuid) => ({
      source: uuid,
      options: {type: 'local'},
    })),
    server: {
      process: {
        url: input.dataset.storeUrl,
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          Accept: 'application/json',
        },
        ondata: (formData) => {
          const file = formData.getAll('filepond').find((item) => item instanceof File);
          formData.delete('filepond');
          if (file) formData.append('filepond', file);
          return formData;
        },
        onload: (response) => {
          const id = String(response).trim();
          appendHidden(id);
          return id;
        },
      },
      revert: (id, load, error) => {
        deletePhoto(id).then((ok) => (ok ? load() : error('Не удалось удалить фото')));
      },
      remove: (source, load, error) => {
        deletePhoto(source).then((ok) => (ok ? load() : error('Не удалось удалить фото')));
      },
      load: (source, load, error) => {
        const controller = new AbortController();

        fetch(`${input.dataset.loadUrl}/${source}/file`, {
          headers: {Accept: 'application/octet-stream'},
          signal: controller.signal,
        })
          .then((response) => {
            if (!response.ok) throw new Error('load failed');
            return response.blob();
          })
          .then((blob) => load(blob))
          .catch(() => error('Не удалось показать фото'));

        return {abort: () => controller.abort()};
      },
    },
  });

  pond.on('reorderfiles', (files) => {
    form.querySelectorAll('[data-photo-id]').forEach((el) => el.remove());
    files.filter((file) => file.serverId).forEach((file) => appendHidden(file.serverId));
  });

  const appendHidden = (id) => {
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'photo_ids[]';
    hidden.value = id;
    hidden.dataset.photoId = id;
    form.appendChild(hidden);
  };

  const deletePhoto = (id) =>
    fetch(`${input.dataset.deleteUrl}/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrf,
        Accept: 'application/json',
      },
    }).then((response) => {
      if (!response.ok) return false;
      document.querySelector(`[data-photo-id="${id}"]`)?.remove();
      return true;
    });

  const syncSubmit = () => {
    const files = pond.getFiles();
    const busy = files.some((file) =>
      [
        FilePond.FileStatus.INIT,
        FilePond.FileStatus.LOADING,
        FilePond.FileStatus.PROCESSING_QUEUED,
        FilePond.FileStatus.PROCESSING,
      ].includes(file.status),
    );
    const failed = files.some((file) =>
      [FilePond.FileStatus.PROCESSING_ERROR, FilePond.FileStatus.LOAD_ERROR].includes(file.status),
    );
    submitBtn.disabled = busy || failed;
  };

  pond.on('addfile', syncSubmit);
  pond.on('processfilestart', syncSubmit);
  pond.on('processfile', syncSubmit);
  pond.on('processfileabort', syncSubmit);
  pond.on('removefile', syncSubmit);
  pond.on('error', syncSubmit);
  pond.on('initfile', syncSubmit);
  syncSubmit();
});

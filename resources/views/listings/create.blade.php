<x-layouts.app
    title="Подать объявление"
    heading="Подать объявление"
    robots="noindex, nofollow"
>

  @push('styles')
    <link href="https://unpkg.com/filepond@^4/dist/filepond.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
          rel="stylesheet">
  @endpush
  @push('scripts')
    <script
        src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script
        src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script src="{{ asset('js/listing-form.js') }}"></script>
  @endpush


  <form class="form" id="sendListingForm" action="{{ route('listings.store') }}" method="post"
        enctype="multipart/form-data">
    @csrf

    @foreach ($pendingPhotos as $photo)
      <input type="hidden" name="photo_ids[]" value="{{ $photo->uuid }}" data-photo-id="{{ $photo->uuid }}">
    @endforeach

    <label>
      Заголовок
      <input type="text" name="title" value="{{ old('title') }}" required maxlength="80">
    </label>

    <label>
      Категория
      <select name="category_id" required>
        <option value="">Выберите</option>
        @foreach ($categories as $category)
          <optgroup label="{{ $category->name }}">
            @foreach ($category->children as $child)
              <option value="{{ $child->id }}" @selected(old('category_id') == $child->id)>
                {{ $child->name }}
              </option>
            @endforeach
          </optgroup>
        @endforeach
      </select>
    </label>

    <label>
      Район
      <select name="district_id">
        <option value="" @selected(old('district_id') === null || old('district_id') === '')>
          Весь Ульяновск
        </option> @foreach ($districts as $district)
          <option value="{{ $district->id }}" @selected(old('district_id') == $district->id)>
            {{ $district->name }}
          </option>
        @endforeach
      </select>
    </label>

    <label>
      Цена
      <input type="text" name="price" value="{{ old('price') }}" inputmode="numeric" placeholder="Пусто – договорная">
    </label>

    <label>
      Текст (не более 4000 символов)
      <textarea name="body" required maxlength="4000">{{ old('body') }}</textarea>
    </label>

    <label>
      Фото (не более 8 штук, не более 5 мб каждое)
      <input
          type="file"
          id="photos"
          name="filepond"
          data-store-url="{{ route('photos.store') }}"
          data-delete-url="{{ url('/photos') }}"
          data-load-url="{{ url('/photos') }}"
          data-max-files="{{ config('uldoska.max_attached_photos_count') }}"
          data-max-size="{{ (int) config('uldoska.max_attached_photo_size') }}"
          data-existing='@json($pendingPhotos->pluck('uuid'))'
      >
    </label>

    <label>
      <input type="checkbox" name="agree" value="1" @checked(old('agree')) required>
      Согласен с
      <a href="{{ route('pages.rules') }}" target="_blank">правилами</a>
      и
      <a href="{{ route('pages.privacy') }}" target="_blank">политикой обработки данных</a> </label>

    <button type="submit" id="sendListingBtn">Отправить на подтверждение</button>
  </form>
  <script
      src="{{ asset('js/submit-btn-disable.js') }}?v={{ filemtime(public_path('js/submit-btn-disable.js')) }}"></script>
</x-layouts.app>

<x-layouts.app
    title="Подать объявление"
    heading="Подать объявление"
    robots="noindex, nofollow"
>
  <form class="form" id="sendListingForm" action="{{ route('listings.store') }}" method="post" enctype="multipart/form-data">
    @csrf

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
          name="photos[]"
          accept="image/*"
          multiple
          data-photos
          data-max-file="5242880"
          data-max-files="8"
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
  <script src="{{ asset('js/listing-photos.js') }}"></script>
  <script src="{{ asset('js/submit-btn-disable.js') }}"></script>
</x-layouts.app>
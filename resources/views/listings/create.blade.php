<x-layouts.app title="Подать объявление">
  <form class="form" action="{{ route('listings.store') }}" method="post" enctype="multipart/form-data">
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
          <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
            {{ $category->name }}
          </option>
        @endforeach
      </select>
    </label>

    <label>
      Район
      <select name="district_id" required>
        <option value="">Выберите</option>
        @foreach ($districts as $district)
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
      Текст
      <textarea name="body" required maxlength="4000">{{ old('body') }}</textarea>
    </label>

    <label>
      Фото
      <input type="file" name="photos[]" accept="image/*" multiple>
    </label>

    <label>
      Телефон
      <input type="tel" name="phone" value="{{ old('phone') }}" required>
    </label>

    <label>
      <input type="checkbox" name="agree" value="1" @checked(old('agree')) required>
      Согласен с <a href="{{ route('pages.rules') }}">правилами</a>
    </label>

    <button type="submit">Отправить</button>
  </form>
</x-layouts.app>
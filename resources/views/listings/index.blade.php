{{-- listings/index.blade.php --}}

<x-layouts.app :title="$title" :heading="false" :category="$selectedCategory">
  <h1>{{ $title }}</h1>

  <form method="get" action="{{ route('listings.go') }}" class="filters">

    <input type="hidden" name="q" value="{{ $q }}">

    <select name="district" onchange="this.form.submit()">
      <option value="">Все районы</option>
      @foreach ($districts as $district)
        <option value="{{ $district->slug }}" @selected($selectedDistrict?->slug === $district->slug)>
          {{ $district->name }}
        </option>
      @endforeach
    </select>

    <select name="category" onchange="this.form.submit()">
      <option value="">Все категории</option>
      @foreach ($categories as $category)
        <optgroup label="{{ $category->name }}">

          <option value="{{ $category->slug }}" @selected($selectedCategory?->id === $category->id)>
            Все категории из раздела "{{ $category->name }}"
          </option>

          @foreach ($category->children as $child)
            <option value="{{ $child->slug }}" @selected($selectedCategory?->id === $child->id)>
              {{ $child->name }}
            </option>
          @endforeach
        </optgroup>
      @endforeach
    </select>

    <button type="submit">Найти</button>

  </form>

  <x-listing-grid :listings="$listings"/>

  {{ $listings->links() }}
</x-layouts.app>

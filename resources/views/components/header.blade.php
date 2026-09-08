@props(['category' => null])

<header class="header">
  <a href="{{ route('home') }}" class="logo">uldoska</a>

  <form class="search" action="{{ route('listings.go') }}" method="get" role="search">

    @isset($category)
      <input type="hidden" name="category" value="{{ $category->slug }}">
    @endisset

    <input type="search" name="q" value="{{ request('q') }}" placeholder="Найти объявление" enterkeyhint="search">
    <button type="submit">Найти</button>
  </form>
</header>

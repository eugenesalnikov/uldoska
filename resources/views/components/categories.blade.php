@props([
    'categories',
    'current' => null,
])

<nav class="categories">
  <a
      href="{{ route('listings.index') }}"
      @class(['is-active' => !$current])
  >Все</a>

  @foreach ($categories as $category)
    <a
        href="{{ route('listings.index', ['category' => $category->slug]) }}"
        @class(['is-active' => $current === $category->slug])
    >{{ $category->name }}</a>

    @foreach ($category->children as $child)
      <a
          href="{{ route('listings.index', ['category' => $child->slug]) }}"
          @class(['is-active' => $current === $child->slug])
      >{{ $child->name }}</a>
    @endforeach
  @endforeach
</nav>

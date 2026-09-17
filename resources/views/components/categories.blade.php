@props([
    'categories',
    'current' => null,
])

<div class="category-groups">
  <h2>Категории</h2>

  @foreach ($categories as $category)
    <section class="category-group">
      <a
          href="{{ route('listings.category', $category) }}"
          @class(['category-group-title', 'is-active' => $current === $category->slug])
      >{{ $category->name }}</a>

      @if ($category->children->isNotEmpty())
        <nav class="categories">
          @foreach ($category->children as $child)
            <a
                href="{{ route('listings.category', $child) }}"
                @class(['is-active' => $current === $child->slug])
            >{{ $child->name }}</a>
          @endforeach
        </nav>
      @endif
    </section>
  @endforeach
</div>

<x-layouts.app
  :heading="$heading"
  :title="$title"
>
  <section class="district">
    <h2>Районы</h2>

    <nav class="categories">
      <a
          href="{{ route('district.all') }}"
          @class(['is-active' => !$selectedDistrict])
      >Все районы</a>

      @foreach ($districts as $district)
        <a
            href="{{ route('home.district', ['district' => $district->slug]) }}"
            @class(['is-active' => $selectedDistrict?->slug === $district->slug])
        >
          {{ $district->name }}
        </a>
      @endforeach
    </nav>

  </section>

  <section>
    <h2>Свежие</h2>
    <x-listing-grid :listings="$fresh"/>
    <p class="feed-link">
      <a class="btn btn-ghost" href="{{ route('listings.index') }}">Открыть ленту</a>
    </p>
  </section>

  <x-categories :categories="$categories"/>

</x-layouts.app>

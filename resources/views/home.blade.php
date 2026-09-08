{{-- home.blade.php --}}
<x-layouts.app title="Uldoska – объявления нашего города">
  <section class="district">
    <h2>Районы</h2>

    <nav class="categories">
      <a
          href="{{ route('district.all') }}"
          @class(['is-active' => !$selectedDistrict])
      >Все</a>

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
    <h3>Свежие</h3>
    <x-listing-grid :listings="$fresh"/>
    <a href="{{ route('listings.index') }}">Все объявления</a>
  </section>

  <x-categories :categories="$categories"/>

</x-layouts.app>

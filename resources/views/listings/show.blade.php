<x-layouts.app :title="$listing->title" :heading="false">
  <article class="listing">
    <div>
      <p class="meta">
        <a href="{{ route('home') }}">uldoska</a>
        ·
        <a href="{{ route('home.district', $listing->district) }}">{{ $listing->district->name }}</a>
        ·
        <a href="{{ route('listings.category', $listing->category) }}">{{ $listing->category->name }}</a>
        ·
        {{ $listing->published_at?->diffForHumans() }}
      </p>

      <h1>{{ $listing->title }}</h1>

      <div class="gallery">
        @forelse ($listing->getMedia('photos') as $photo)
          <img src="{{ $photo->getUrl('show') ?: $photo->getUrl() }}" alt="">
        @empty
          <img src="{{ asset('images/placeholder.svg') }}" alt="">
        @endforelse
      </div>

      <div class="prose">
        {!! nl2br(e($listing->body)) !!}
      </div>
    </div>

    <aside class="panel">
      <p @class(['price', 'is-negotiable' => $listing->price === null])>
        {{ $listing->price_label }}
      </p>
      <p class="meta">{{ $listing->district->name }}</p>

      @if ($listing->phone)
        <p>
          <a class="btn" href="tel:{{ $listing->phone }}">{{ $listing->phone }}</a>
        </p>
      @endif
    </aside>
  </article>
</x-layouts.app>

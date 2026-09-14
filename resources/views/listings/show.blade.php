<x-layouts.app :title="$listing->title" :heading="false" xmlns="http://www.w3.org/1999/html">
  <article class="listing">
    <div>
      <p class="meta">
        <a href="{{ route('home') }}">uldoska</a>
        ·
        <a href="{{ route('home.district', $listing->district) }}">{{ $listing->district->name }}</a>
        ·
        <a href="{{ route('listings.category', $listing->category) }}">{{ $listing->category->name }}</a>
        ·
        <span title="{{ $listing->datePublishedPlainFormat() }}">{{ $listing->datePublishedHumanreadable() }}</span>
      </p>

      <h1>{{ $listing->title }}</h1>

      <div class="gallery" data-gallery>
        @forelse ($listing->getMedia('photos') as $photo)
          <button type="button" class="gallery-item" data-full="{{ $photo->getUrl('show') ?: $photo->getUrl() }}">
            <img src="{{ $photo->getUrl('thumb') ?: $photo->getUrl('show') ?: $photo->getUrl() }}" alt="">
          </button>
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
      <p>
        <a class="btn" href="{{ $listing->telegramInterestLink() }}" target="_blank" rel="noopener">
          Связаться с автором объявления
        </a>
      </p>
    </aside>
  </article>
  <script src="{{ asset('js/lightbox.js') }}"></script>
</x-layouts.app>

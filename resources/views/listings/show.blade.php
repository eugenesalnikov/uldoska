<x-layouts.app :title="$listing->title" :heading="false">
  <article class="listing">
    <div>
      <p class="meta">
        {{ $listing->category_name }}
        · {{ $listing->district_name }}
        · {{ $listing->published_at?->diffForHumans() }}
      </p>

      <h1>{{ $listing->title }}</h1>

      <div class="gallery">
        @forelse ($listing->photos as $photo)
          <img src="{{ $photo }}" alt="">
        @empty
          <img src="{{ asset('images/placeholder.svg') }}" alt="">
        @endforelse
      </div>

      <div class="prose">
        {!! nl2br(e($listing->body)) !!}
      </div>
    </div>

    <aside class="panel">
      <p class="price">{{ $listing->price_label ?: 'Договорная' }}</p>
      <p class="meta">{{ $listing->district_name }}</p>

      @if ($listing->phone)
        <p><a class="btn" href="tel:{{ $listing->phone }}">{{ $listing->phone }}</a></p>
      @endif
    </aside>
  </article>
</x-layouts.app>

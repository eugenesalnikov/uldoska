{{-- components/listing-card.blade.php --}}
@props(['listing'])

<article class="card">
  <a href="{{ route('listings.show', $listing) }}">
    <img
        src="{{ $listing->cover_url ?: asset('images/placeholder.svg') }}"
        alt=""
    >
    <div class="card-body">
      <h3>{{ $listing->title }}</h3>
      <p @class(['price', 'is-negotiable' => $listing->price === null])>
        {{ $listing->price_label }}
      </p>
      <p class="meta">
        {{ $listing->district->name }}
        · {{ $listing->published_at?->diffForHumans() ?? 'черновик' }}
      </p>
    </div>
  </a>
</article>

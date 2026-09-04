{{-- components/listing-card.blade.php --}}
@props(['listing'])

<article class="card">
  <a href="{{ route('listings.show', $listing) }}">
    <img src="{{ $listing->cover_url ?? asset('images/placeholder.svg') }}" alt="">
    <div class="card-body">
      <h3>{{ $listing->title }}</h3>
      <p class="price">{{ $listing->price_label }}</p>
      <p class="meta">{{ $listing->district_name }} · сегодня</p>
    </div>
  </a>
</article>

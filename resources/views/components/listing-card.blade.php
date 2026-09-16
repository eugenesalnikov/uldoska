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
      <p class="meta" title="{{ $listing->datePublishedPlainFormat() }}">
        {{ $listing->district->name ?? 'Весь Ульяновск' }} · {{ $listing->datePublishedHumanreadable()}}
      </p>
    </div>
  </a>
</article>

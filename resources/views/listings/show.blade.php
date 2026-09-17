<x-layouts.app
    :title="$title"
    :heading="$heading"
    :description="$description"
>
  <article class="listing">
    <div>
      <p class="meta">
        <a href="{{ route('home') }}">uldoska</a>
        ·
        @if ($listing->district)
          <a href="{{ route('home.district', $listing->district) }}">{{ $listing->district->name }}</a>
        @else
          <a href="{{ route('home') }}">Весь Ульяновск</a>
        @endif
        ·
        <a href="{{ route('listings.category', $listing->category) }}">{{ $listing->category->name }}</a>
        ·
        <span title="{{ $listing->datePublishedPlainFormat() }}">{{ $listing->datePublishedHumanreadable() }}</span>
      </p>

      @php
        $districtName = $listing->district
            ? $listing->district->name
            : 'Весь Ульяновск';
      @endphp

      <div class="gallery" data-gallery>
        @forelse ($listing->getMedia('photos') as $photo)
          <button type="button" class="gallery-item" data-full="{{ $photo->getUrl('show') ?: $photo->getUrl() }}">
            <img src="{{ $photo->getUrl('thumb') ?: $photo->getUrl('show') ?: $photo->getUrl() }}"
                 alt="{{ $listing->title . ', ' . $districtName . ', ' .  $listing->category->name }}">
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
      <p class="meta">{{ $listing->district->name ?? 'Весь Ульяновск' }}</p>

      <p class="notice">
        Не переводите предоплату до личной встречи и осмотра товара.
      </p>

      <form
          class="contact-form"
          data-href="{{ $listing->telegramInterestLink() }}"
          onsubmit="window.open(this.dataset.href, '_blank', 'noopener'); return false;"
      >
        <label>
          <input type="checkbox" name="agree" value="1" required>
          <span>
                Согласен с
                <a href="{{ route('pages.rules') }}" target="_blank">правилами</a>
            и
      <a href="{{ route('pages.privacy') }}" target="_blank">политикой обработки данных</a>
            </span>
        </label>
        <p>
          <button type="submit" class="btn">
            Связаться с автором объявления
          </button>
        </p>
      </form>
    </aside>
  </article>
  <x-lightbox/>
  <script src="{{ asset('js/lightbox.js') }}"></script>
</x-layouts.app>

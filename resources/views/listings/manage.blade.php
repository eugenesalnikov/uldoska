<x-layouts.app
    title="Управление объявлением"
    heading="Управление объявлением"
    robots="noindex, nofollow"
>
  @if ($listing->isPublished())
    <p>
      <a href="{{ $listing->publicLink() }}" target="_blank" rel="noopener">Публичная ссылка</a>
    </p>
  @endif

  <div class="actions">
    @if ($listing->canExtend())
      <form method="post" action="{{ route('listings.extend', $listing) }}">
        @csrf
        <button type="submit">Продлить</button>
      </form>
    @endif

    @if (!$listing->isRemoved())
      <form method="post" action="{{ route('listings.remove', $listing) }}"
            onsubmit="return confirm('Снять объявление?')">
        @csrf
        <button type="submit" class="btn-ghost">Снять с доски</button>
      </form>
    @endif
  </div>

  <article class="listing">
    <div>
      <p class="meta">
        Статус: {{ $listing->status->label() }}
        @if ($listing->expires_at)
          · до {{ $listing->expires_at->format('d.m.Y H:i') }}
        @endif
      </p>
      <p><strong>{{ $listing->title }}</strong></p>
      <div class="prose">
        {!! nl2br(e($listing->body)) !!}
      </div>
    </div>

    <aside class="panel">
      <p @class(['price', 'is-negotiable' => $listing->price === null])>
        {{ $listing->price_label }}
      </p>
      <p class="meta">{{ $listing->district->name ?? 'Весь Ульяновск' }}</p>
    </aside>
  </article>

</x-layouts.app>

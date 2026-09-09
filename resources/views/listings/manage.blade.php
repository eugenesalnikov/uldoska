<x-layouts.app title="Управление объявлением">

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

  <article class="prose">
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
  </article>
</x-layouts.app>

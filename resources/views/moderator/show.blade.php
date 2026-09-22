@php use App\Enums\ListingRejectionReason; @endphp
<x-layouts.moderator
    :title="$listing->title"
    :heading="$listing->title . ' – модерация'"
>
  <p class="meta">
    {{ $listing->status->label() }}
    · подано {{ $listing->created_at->format('d.m.Y H:i') }}
  </p>

  <article class="listing">
    <div>
      <p class="meta">
        @if ($listing->district)
          <a href="{{ route('home.district', $listing->district) }}">{{ $listing->district->name }}</a>
          ·
        @else
          <a href="{{ route('home') }}">Весь Ульяновск</a> ·
        @endif
        <a href="{{ route('listings.category', $listing->category) }}">{{ $listing->category->name }}</a>
      </p>

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
      <p class="meta">{{ $listing->district->name ?? 'Весь Ульяновск' }}</p>

      <div class="actions">

        @if ($listing->telegram_chat_id)
          @php
            $count = $listing->publishedCountForTelegram();
            $limit = config('uldoska.max_published_listings', 5);
            $limitReached = $count >= $limit;
          @endphp
          <div>
            Опубликованных объявлений у данного ТГ аккаунта:
            <div @class(['limit-reached' => $limitReached])>
              {{ $count }}/{{ $limit }}
            </div>
          </div>
        @endif

        <form
            method="post"
            action="{{ route('moderator.approve', $listing) }}"
            onsubmit="return confirm('Одобрить объявление?')"
        >
          @csrf
          <button type="submit">Одобрить</button>
        </form>
        <form
            method="post"
            action="{{ route('moderator.reject', $listing) }}"
            class="form"
            onsubmit="return confirm('Отклонить объявление?')"
        >
          @csrf

          <label>
            Причина отклонения
            <select name="rejection_reason" required>
              <option value="" disabled selected>Выберите причину</option>
              @foreach (ListingRejectionReason::cases() as $reason)
                <option value="{{ $reason->value }}" @selected(old('rejection_reason') === $reason->value)>
                  {{ $reason->label() }}
                </option>
              @endforeach
            </select>
          </label>

          <label>
            Комментарий автору при отклонении
            <textarea name="rejection_comment" rows="3" maxlength="500">{{ old('rejection_comment') }}</textarea>
          </label>

          <button type="submit" class="btn-ghost">Отклонить</button>
        </form>
      </div>
    </aside>
  </article>
  <x-lightbox/>
  <script src="{{ asset('js/lightbox.js') }}?v={{ filemtime(public_path('js/lightbox.js')) }}"></script>
</x-layouts.moderator>

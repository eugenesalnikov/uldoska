@php use App\Enums\ListingRejectionReason; @endphp
<x-layouts.moderator :title="$listing->title">
  <p class="meta">
    {{ $listing->status->label() }}
    · подано {{ $listing->created_at->format('d.m.Y H:i') }}
    · {{ $listing->phone }}
  </p>

  <article class="listing">
    <div>
      <p class="meta">
        <a href="{{ route('home.district', $listing->district) }}">{{ $listing->district->name }}</a>
        ·
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
      <p class="meta">{{ $listing->district->name }}</p>
      <p><a class="btn" href="tel:{{ $listing->phone }}">{{ $listing->phone }}</a></p>

      <div class="actions">

        @if ($listing->telegram_chat_id)
          @php
            $count = $listing->publishedCountForTelegram();
            $limit = config('uldoska.max_active_listings', 5);
            $limitReached = $count >= $limit;
          @endphp
          <div>
            Опубликованных объявлений у данного ТГ аккаунта:
            <div @class(['limit-reached' => $limitReached])>
              {{ $listing->publishedCountForTelegram() }}/{{ config('uldoska.max_active_listings', 5) }}
            </div>
          </div>
        @endif

        <form
            method="post"
            action="{{ route('moderator.publish', $listing) }}"
            onsubmit="return confirm('Опубликовать объявление?')"
        >
          @csrf
          <button type="submit">Опубликовать</button>
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
  <script src="{{ asset('js/lightbox.js') }}"></script>
</x-layouts.moderator>

<x-layouts.moderator title="Панель модерации">
  @if ($listings->isEmpty())
    <p>Пусто.</p>
  @else
    <table class="table">
      <thead>
      <tr>
        <th>Дата</th>
        <th>Заголовок</th>
        <th>Район</th>
        <th>Категория</th>
        <th>Цена</th>
        <th>Опубликованных у данного ТГ аккаунта</th>
      </tr>
      </thead>
      <tbody>

      @php
        $limit = config('uldoska.max_active_listings', 5);
      @endphp

      @foreach ($listings as $listing)
        <tr>
          <td>{{ $listing->created_at->format('d.m H:i') }}</td>
          <td>
            <a href="{{ route('moderator.show', $listing) }}">
              {{ $listing->title }}
            </a>
          </td>
          <td>{{ $listing->district->name }}</td>
          <td>{{ $listing->category->name }}</td>
          <td>{{ $listing->price_label }}</td>
          @php
            $count = $listing->published_on_telegram_count;
            $limitReached = $listing->telegram_chat_id && $count >= $limit;
          @endphp
          <td @class(['limit-reached' => $limitReached])>
            @if ($listing->telegram_chat_id)
              {{ $listing->published_on_telegram_count }} / {{ config('uldoska.max_active_listings', 5) }}
            @else
              —
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  @endif
</x-layouts.moderator>

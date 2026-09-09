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
      </tr>
      </thead>
      <tbody>
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
        </tr>
      @endforeach
      </tbody>
    </table>
  @endif
</x-layouts.moderator>

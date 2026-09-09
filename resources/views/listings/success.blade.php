<x-layouts.app title="Объявление отправлено">
  <article class="prose">
    <p>Объявление «{{ $listing->title }}» сохранено и пока не в ленте.</p>

    <p>Чтобы его опубликовать, подтвердите, что вы реальный человек:</p>

    <p>
      <a href="{{ $listing->telegramUrl() }}" target="_blank" rel="noopener">
        Подтвердить в Telegram
      </a>
    </p>

    <p>После перехода в бота нажмите Start. Затем объявление уйдёт на модерацию.</p>
  </article>
</x-layouts.app>

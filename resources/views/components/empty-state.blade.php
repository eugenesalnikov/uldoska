@props([
    'title' => 'Ничего не найдено',
    'text' => 'Пока нет объявлений. Загляните позже или подайте своё.',
])

<div {{ $attributes->class('empty') }}>
  <strong>{{ $title }}</strong>
  <p>{{ $text }}</p>
</div>

@props([
    'title' => 'Uldoska',
    'description' => 'Доска объявлений Ульяновска',
    'heading' => null,
    'category' => null,
])

    <!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title === 'Uldoska' ? $title : $title.' – Uldoska' }}</title>
  <meta name="description" content="{{ $description }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<x-header :category="$category" />

<main class="page">
  <x-flash />

  @if ($heading !== false)
    <h1>{{ $heading ?? $title }}</h1>
  @endif

  {{ $slot }}
</main>

<x-footer />
</body>
</html>

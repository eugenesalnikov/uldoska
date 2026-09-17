@props([
    'title' => 'Uldoska – объявления Ульяновска',
    'description' => 'Доска объявлений города Ульяновска',
    'heading' => null,
    'category' => null,
    'robots' => null,
])
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @if ($robots)
    <meta name="robots" content="{{ $robots }}">
  @endif
  {{ $head ?? '' }}
  <title>{{ $title }}</title>
  <meta name="description" content="{{ $description }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<x-header :category="$category"/>
<main class="page">
  <x-flash/>
  @if ($heading)
    <h1>{{ $heading }}</h1>
  @endif
  {{ $slot }}
</main>
<x-footer/>
</body>
</html>
